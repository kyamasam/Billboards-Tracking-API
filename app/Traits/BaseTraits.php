<?php

namespace App\Traits;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try{
            $result = $model::find($id);
            return true;
        }catch (ModelNotFoundException $exception){
            return $this->ErrorReporter($ModelName.' Not Found', $ModelName.' Id passed was not found in the database',422);
        }
    }
    public function ValidateAvailabilityModel(Model $model,$id){
        $result=$model::find($id);
        if(isset($result)){
            return true;
        }else{
            return false;
        }

    }

}
