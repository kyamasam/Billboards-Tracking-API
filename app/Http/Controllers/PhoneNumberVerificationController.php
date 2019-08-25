<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhoneNumberVerificationResource;
use App\Http\Resources\UserResource;
use App\PhoneNumberVerification;
use App\User;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use App\Traits\BaseTraits;


class PhoneNumberVerificationController extends Controller
{
    use BaseTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }




    public function send_code(Request $request)
    {
        $username = env('AFRICAS_TALKING_USERNAME');
        $apiKey   = env('AFRICAS_TALKING_APIKEY');
        $AT       = new AfricasTalking($username, $apiKey);

        // Get one of the services
        $sms      = $AT->sms();


        //get all inputs
        $input = $request->all();

        $this->validate($request, [
            "phone_number" => "numeric|required",
        ]);

        //generate random 4 digit code
        $code = rand(1000, 10000);

        // Use the service
        $result   = $sms->send([
            'to'      => $input['phone_number'],
            'message' => 'Your Adklout verification code is:'. $code
        ]);

        $phone_verification = new PhoneNumberVerification();
        $phone_verification->verification_code=$code;
        $phone_verification->phone_number=$input['phone_number'];
        $phone_verification->save();

        return new PhoneNumberVerificationResource($result);
    }

    public function send_code_complete(Request $request){
        $this->validate($request, [
            "verification_code" => "numeric|required",
            "phone_number" => "numeric|required",
        ]);

        //get all inputs
        $input = $request->all();
        $phoneNumberVerification = PhoneNumberVerification::where('verification_code','=',$input['verification_code'])->where('phone_number','=',$input['phone_number'])->where('used','!=',true)->first();
        if(isset($phoneNumberVerification)){
            //find the user with this phone number
            $user=User::where('msisdn','=',$input['phone_number'])->first();
            if(isset($user)){
                //the number was found
                $user->update(['msisdn_verified'=>true]);

                //set the code to used
                $phoneNumberVerification->update(['used'=>true]);

                return new UserResource($user);
            }else{
                //error
                return $this->ErrorReporter("Passed Data Was Invalid","The Phone number was not found",422);
            }


                return new PhoneNumberVerificationResource($phoneNumberVerification);
        }else{
            return $this->ErrorReporter("Passed Data Was Invalid","Either The Phone number was not found Or the code was incorrect",422);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PhoneNumberVerification  $phoneNumberVerification
     * @return \Illuminate\Http\Response
     */
    public function destroy(PhoneNumberVerification $phoneNumberVerification)
    {
        //
    }
}
