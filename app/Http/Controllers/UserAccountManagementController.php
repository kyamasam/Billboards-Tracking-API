<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\BaseTraits;


class UserAccountManagementController extends Controller
{
    use BaseTraits;

    /**
     *
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            "last_name"=> "required|min:3",
            "middle_name"=> "required|min:3",
            "first_name"=> "required|min:3",
            "msisdn"=> "required|numeric",
            "avatar"=> "required|",
            "cover_photo"=> "required|",
        ]);



        $input =  $request->all();
        //fetch user
        $user = User::find($id);

        $user->user_name =  $input['user_name'];
        $user->last_name = $input['last_name'];
        $user->middle_name = $input['middle_name'];
        $user->first_name = $input['first_name'];
        $user->msisdn = $input['msisdn'];
        $user->avatar = $input['avatar'];
        $user->cover_photo = $input['cover_photo'];

        $user->save();

//        $user_edited = User::find($id);

        return response (new UserResource($user))->setStatusCode(200);


//        return response()->json(['data' => $user]);
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
