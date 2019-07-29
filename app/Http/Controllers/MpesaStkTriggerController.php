<?php

namespace App\Http\Controllers;

use App\Http\Resources\MpesaStkCallbackResource;
use App\Http\Resources\MpesaStkTriggerResource;
use App\Jobs\SendEmailJob;
use App\Mail\PaymentVerified;
use App\MpesaStkCallback;
use App\MpesaStkTrigger;
use App\Traits\BaseTraits;
use App\Wallet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PDF;

class MpesaStkTriggerController extends Controller
{
    use BaseTraits;

    /**
     * generate auth token
     * @return mixed
     */

    public function generate_sandbox_token()
    {

        try {
            $consumer_key = env("MPESA_CONSUMER_KEY");
            $consumer_secret = env("MPESA_CONSUMER_SECRET");
        } catch (\Throwable $th) {
            $consumer_key = self::env("MPESA_CONSUMER_KEY");
            $consumer_secret = self::env("MPESA_CONSUMER_SECRET");
        }
        if (!isset($consumer_key) || !isset($consumer_secret)) {
            die("please declare the consumer key and consumer secret as defined in the documentation");
        }
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
//        date_default_timezone_set('Africa/Nairobi');
//        $GLOBALS["time_of_new_token"]=date("h:i:s");
        return json_decode($curl_response)->access_token;


    }

    /**
     * @param $phone
     * @param null $paybill
     * @return MpesaStkTriggerResource
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            "phone" => "required|numeric",
            "amount" => "required|numeric",
        ]);
        $phone = $request->phone;
        $business_short_code = env('BUSINESS_SHORT_CODE');
        $amount = $request->amount;


        // = 174379;
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->generate_sandbox_token())); //setting custom header


        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $business_short_code,
            'Password' => 'MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMTkwMTI1MTYzMTI2',
            'Timestamp' => '20190125163126',
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $business_short_code,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'http://sefapay.skalityprojects.com/stkpush_callback.php',
            'AccountReference' => 'ref',
            'TransactionDesc' => 'transaction'

        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        $json_data = json_decode($curl_response, true);

        $merchant = $json_data['MerchantRequestID'];
        $mpesa_stk_response = new MpesaStkTrigger();
        $mpesa_stk_response->merchant_request_id = $merchant;
        $mpesa_stk_response->checkout_request_id = $json_data['CheckoutRequestID'];
        $mpesa_stk_response->response_code = $json_data['ResponseCode'];
        $mpesa_stk_response->response_description = $json_data['ResponseDescription'];
        $mpesa_stk_response->customer_message = $json_data['CustomerMessage'];

        $mpesa_stk_response->save();

        return new MpesaStkTriggerResource($mpesa_stk_response);


    }

    public function verify(Request $request)
    {
        $this->validate($request, [
            "merchant_request_id" => "required",
        ]);
        $merchant_request_id = $request->merchant_request_id;
        $user = auth()->user();

        //todo add even more security to ensure people don't verify transactions twice thus adding more money to their wallets
        //find the record in the callbacks table
        try{
            $callback_record = MpesaStkCallback::where('merchantRequestID', '=', $merchant_request_id)->WhereNull('user_id')->firstOrFail();

        }catch (ModelNotFoundException $exception){
            //if model is not found, it could be either the transaction has already been verified
            //or the transaction doesnt exist at all
            try{
                $callback_record = MpesaStkCallback::where('merchantRequestID', '=', $merchant_request_id)->firstOrFail();
                if($callback_record->count()){
                    return $this->ErrorReporter('Transaction already verified','The Transaction has already been verified',422);
                }
            }
            catch (ModelNotFoundException $exception) {
                return $this->ErrorReporter('Record Not Found','The Merchant Request Id Does Not Exist. Try again After a few seconds',422);
            }

            return $this->ErrorReporter('Record Not Found','The Merchant Request Id Does Not Exist. Try again After a few seconds',422);
        }


        $now = strtotime(date("h:i:sa"));
        $receipt_file_name='Mpesa'.$now.'.'.'pdf';
        $receipt_file_path='public/receipts/'.$receipt_file_name;
        $absolute_file_path=env('MEDIA_SERVER_URL').'receipts/';

        //check that the transaction was a success
        if ($callback_record->resultCode == 0) {

            //now continue with execution;
            // the payment was successful therefore we can update users wallet
            $user_wallet = $user->Wallet();
            if (!($user_wallet->count())) {
                // the user has no wallet thus create one
                $user_wallet = new Wallet();
                $user_wallet->user_id = $user->id;
                //save the balance directly
                $user_wallet->credit_balance =$callback_record->amount;
                //create a credit verifier
                $user_wallet->credit_balance_verifier=Hash::make($user->id.$callback_record->amount);
                $user_wallet->save();
                //modify callback record
                $callback_record->update(['user_id' => $user->id]);

                //send email
                //callback , user , payment_method
                $payment_details['payment_details']=  $callback_record;
                $payment_details['payment_method']='M-Pesa';
                $payment_details['user']=$request->user();
                //recipient
                //mailer_class
                $email_details['recipient']= $request->user();


                $email_details['mailer_class']= new PaymentVerified($payment_details);

                dispatch(new SendEmailJob($email_details));
                //generate pdf
                //generate pdf
                $pdf = PDF::loadView('pdf.payment_receipt', array('payment_details' => $payment_details));
                $receipt_file=  $pdf->download()->getOriginalContent();


                Storage::disk('custom')->put($receipt_file_path,$receipt_file);
                //add to database
                $callback_record->update(['receipt' => $absolute_file_path.$receipt_file_name]);


                return new MpesaStkCallbackResource($callback_record);

            }else{
                // the user has a wallet
                $user_wallet= $user_wallet->first();
                $old_balance = $user_wallet->credit_balance;
                //calculate new bal
                $new_balance = (float)$old_balance+$callback_record->amount;
                //now update the balance
                $user_wallet->credit_balance = $new_balance;
                $user_wallet->credit_balance_verifier=Hash::make($user->id.$new_balance);
                $user_wallet->save();
                //modify callback record
                $callback_record->update(['user_id' => $user->id]);
                //send email
                //callback , user , payment_method
                $payment_details['payment_details']=  $callback_record;
                $payment_details['payment_method']='M-Pesa';
                $payment_details['user']=$request->user();
                //recipient
                //mailer_class
                $email_details['recipient']= $request->user();

//                return response()->json([""=>$payment_details]);
                $email_details['mailer_class']= new PaymentVerified($payment_details);

                dispatch(new SendEmailJob($email_details));

                //generate pdf
                $pdf = PDF::loadView('pdf.payment_receipt', array('payment_details' => $payment_details));
                $receipt_file=  $pdf->download()->getOriginalContent();


                Storage::disk('custom')->put($receipt_file_path,$receipt_file);
                //add to database
                $callback_record->update(['receipt' => $absolute_file_path.$receipt_file_name]);


                return new MpesaStkCallbackResource($callback_record);
            }



        } else {
            return $this->ErrorReporter('Payment could not be processed', $callback_record->resultDesc, 422);
        }
    }
}
