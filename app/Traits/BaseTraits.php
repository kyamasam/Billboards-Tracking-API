<?php

namespace App\Traits;

use App\User;

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
            ]
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


}
