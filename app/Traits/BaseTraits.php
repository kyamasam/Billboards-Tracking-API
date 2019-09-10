<?php

namespace App\Traits;

use AfricasTalking\SDK\AfricasTalking;
use App\User;
use App\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

trait BaseTraits
{
    /**
     * @param String $error_title
     * @param String $error_detail
     * @param Integer $status
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */

    public function ErrorReporter(String $error_title , String $error_detail,int $status){
        return response([
            "message"=>$error_title,
            "errors"=>[
                'detail' => [$error_detail]
            ],
            "data"=>null,
        ], $status);
    }

    /**
     * default unauthenticated  error message.
     * Used to avoid calling one method
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */

    public function DefaultUnauthrized(){
        return $this->ErrorReporter("Unauthorized" , "You Do not have rights to access this resource", 401);
    }

    /**
     * @param String $success_title
     * @param String $success_detail
     * @param int $status
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function SuccessReporter(String $success_title , String $success_detail, int $status){
        return response([
            "message"=>$success_title,
            "errors"=>null,
            "data"=>[
                'detail' => [$success_detail]
            ]
        ], $status);
    }
    public function IsAdmin(int $user_id){
        $is_admin=false;
        if(auth()->user()->account_type == 2){
            //the user is an admin
            $is_admin=true;
        }else{
            $is_admin=false;
        }
        return $is_admin;
    }

    public function OwnsAccountOrAdmin(int $user_id){

        $owns_acc = true;
        //a user can only edit their own account
        if($user_id == auth()->user()->id || auth()->user()->account_type == 2){
            //the user owns this account or is an admin
            $owns_acc = true;
        }else{
            //the user is an admin and is therefore allowed to edit thi
            $owns_acc = false;
        }

        return $owns_acc;
    }




    public function OwnsAccount(int $user_id){

        $owns_acc = true;
        //a user can only edit their own account
        if($user_id != auth()->user()->id){
            $owns_acc = false;
        }
        return $owns_acc;
    }

    /**
     * When a resource Id passed need
     * @param $resource
     * @param $resource_name
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */

    public function ResourceNotFound($resource, $resource_name){
        if (!isset($resource)){
            return $this->ErrorReporter($resource_name.' Not Found', $resource_name.' Id passed was not found in the database',422);
        }
    }

    /**
     * Use this Generic Method to Find if An Id exists in the database
     * @param Model $model
     * @param $id
     * @param $ModelName
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */

    public function ValidateAvailability(Model $model,$id, $ModelName){
       $data = $model::find($id);
       if(isset($data)){
           $message=true;
       }else{
           $message= $this->ErrorReporter($ModelName.' Not Found', $ModelName.' Id passed was not found in the database',422);
       }
       return $message;

    }
    public function ValidateAvailabilityModel(Model $model,$id){
        $result=$model::find($id);
        if(isset($result)){
            return true;
        }else{
            return false;
        }

    }
    public function createUserWallet($user_id){
        $user_wallet = new Wallet();
        $user_wallet->user_id = $user_id;
        //save the balance directly
        $user_wallet->credit_balance =0;
        $zero=0;
        //create a credit verifier
        $user_wallet->credit_balance_verifier=Hash::make($user_id.$zero);
        $user_wallet->save();
    }

    public function SendMessage($phone_number, $message){
        $username = env('AFRICAS_TALKING_USERNAME');
        $apiKey   = env('AFRICAS_TALKING_APIKEY');
        $AT       = new AfricasTalking($username, $apiKey);

        // Get one of the services
        $sms      = $AT->sms();


        // Use the service
        $result   = $sms->send([
            'to'      => $phone_number,
            'message' => $message
        ]);
    }

}
