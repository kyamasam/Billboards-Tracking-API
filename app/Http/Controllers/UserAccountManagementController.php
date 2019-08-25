<?php

namespace App\Http\Controllers;

use AfricasTalking\SDK\AfricasTalking;
use App\Http\Resources\PhoneNumberVerificationResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Notifications\ConfirmEmail;
use App\PhoneNumberVerification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\BaseTraits;


class UserAccountManagementController extends Controller
{
    use BaseTraits;

    /**
     * @return UserCollection|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        if($this->IsAdmin((int)auth()->user()->id)){
            return new UserCollection(User::paginate());
        }
        else{
            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to access this resource", 401);
        }
    }


    /**
     * @param $id
     * @return UserResource|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */
//    public function show($id)
//    {
//        $user = User::find($id);
//
//        if($this->OwnsAccountOrAdmin((int)$id)){
//            //the user owns this account and can therefore edit it
//        }
//        else{
//            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to view this account", 401);
//        }
//       return new UserResource($user);
//    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request,$id)
    {

        if($this->OwnsAccount((int)$id)){
            //the user owns this account and can therefore edit it
        }
        else{
            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to edit this account", 401);
        }

        $this->validate($request, [
            "user_name"=> "required|unique:users,user_name,".$id,
        ]);


        $input =  $request->all();
        //fetch user
        $user = User::find($id);
        $user->update($input);
        $user->save();


        return response (new UserResource($user))->setStatusCode(200);


//        return response()->json(['data' => $user]);
    }

    public function change_phone_number(Request $request, $id){
        if($this->OwnsAccount((int)$id)){
            //the user owns this account and can therefore edit it
        }
        else{
            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to edit this account", 401);
        }

        //send verification code
        $username = env('AFRICAS_TALKING_USERNAME');
        $apiKey   = env('AFRICAS_TALKING_APIKEY');
        $AT       = new AfricasTalking($username, $apiKey);

        // Get one of the services
        $sms      = $AT->sms();


        //get all inputs
        $input = $request->all();

        $this->validate($request, [
            "msisdn"=> "required|numeric|unique:users,msisdn,".$id,
        ]);

        //generate random 4 digit code
        $code = rand(1000, 10000);

        // Use the service
        $result   = $sms->send([
            'to'      => $input['msisdn'],
            'message' => 'Your Adklout verification code is:'. $code
        ]);


        //change number in user acc
        $user = User::find($id);
        $user->update(['msisdn_verified'=>false, 'msisdn'=>$input['msisdn']]);


        $phone_verification = new PhoneNumberVerification();
        $phone_verification->verification_code=$code;
        $phone_verification->phone_number=$input['msisdn'];
        $phone_verification->save();

        return new PhoneNumberVerificationResource($result);
    }


    public function confirm_email(Request $request){
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if(!isset($user)){
            return $this->ErrorReporter("User Not Found","Could not find a user with that email address",422);
        }

        //generate random 4 digit code
        $email_verification_code = rand(1000, 10000);

        $user->update(['email_verification_code'=>$email_verification_code]);
        $user->notify(new ConfirmEmail($email_verification_code));

        return $this->SuccessReporter("Verification Code Sent via email","We have emailed you a verification Code Sent via email",200);
    }


    public function confirm_email_complete(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'verification_code' => 'required',
        ]);
        $input = $request->all();
        $user = User::where('email', $request->email)->first();
        $stored_verification_code = $user->email_verification_code;
        $stored_email = $user->email;
        //check if email is already verified

        if($stored_verification_code ==$input['verification_code'] &&$stored_email == $input['email']){
            $user->email_verified_at=strtotime(date("h:i:sa"));
            $user->email_verification_code='';
            $user->save();
            return $this->SuccessReporter("Email Verified", "Email was successfully verified",200);
        }else{
            return $this->ErrorReporter("Email could not be verified", "The passed code did not match any records",422);
        }
    }

    public function change_email(Request $request, $id){

        $request->validate([
            'email' => 'required|string|email',
        ]);
        $input = $request->all();
        //get the old email
        $user =auth()->user();

        //check if old and new email are the same
        if($user->email == $input['email']){
            return $this->ErrorReporter("Email address is similar to existing address","Enter a different email address from existing email",422);
        }
        if(!isset($user)){
            return $this->ErrorReporter("User Not Found","Could not find a user with that email address",422);
        }

        //generate random 4 digit code
        $email_verification_code = rand(1000, 10000);

        $user->update(['email_verification_code'=>$email_verification_code,'email'=>$input['email']]);
        $user->email_verified_at=null;
        $user->save();
        $user->notify(new ConfirmEmail($email_verification_code));

        return $this->SuccessReporter("Verification Code Sent via email","We have emailed you a verification Code Sent via email",200);
    }







    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->OwnsAccountOrAdmin((int)$id)){
            //the user owns this account and can therefore edit it
        }
        else{
            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to delete this account", 401);
        }

        User::destroy($id);

        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);

    }

    public function bulk_delete(Request $request)
    {
        $user_ids = explode(',',$request['user_ids']);

        if($this->IsAdmin((int)auth()->user()->id)){
            User::destroy($user_ids);
        }
        else{
            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to access this resource", 401);
        }


        return $this->SuccessReporter('Records Deleted', 'Records were successfully deleted',200);

    }

    public function admin_update(Request $request,$id)
    {

        if($this->IsAdmin((int)auth()->user()->id)){

            $this->validate($request, [
                "is_verified"=> "required|numeric",
                "is_trusted"=> "required|numeric",
                "account_status"=> "required|numeric",
            ]);


            $input =  $request->all();
            //fetch user
            $user = User::find($id);
            $user->is_verified = $input['is_verified'];
            $user->is_trusted = $input['is_trusted'];
            $user->account_status = $input['account_status'];

            $user->save();

            return response (new UserResource($user))->setStatusCode(200);
        }
        else{
            return $this->ErrorReporter("Unauthorized" , "You Do not have rights to edit this account", 401);
        }


    }


}
