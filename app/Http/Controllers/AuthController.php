<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Integer;

class AuthController extends Controller
{
    public function register(Request $request ){
        $this->validate($request, [
            'user_name' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'msisdn' => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user= User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('AdkloutToken')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request){
        $user_credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($user_credentials)) {
            $token = auth()->user()->createToken('AdkloutToken')->accessToken;
            return response()->json([
                "type"=>'token',
                "id"=>auth()->user()->id,
                "attributes"=> [
                    'user_name' =>auth()->user()->user_name,
                    'token' => $token
                ]

            ],
                200);
        } else {
            return response()->json(
                [
                    "message"=>"Unauthorised",
                    "errors"=>[
                        'detail' => ['Invalid Credentials']
                    ]
                ], 401);
        }
    }

    public function details(){
        return new UserResource(auth()->user());
    }
    public function create(){
        return new UserResource(auth()->user());
    }




    public function update(Request $request)
    {


//
//        //a user can only edit their own account
//        if($user_acc->id !== auth()->user()->id){
//            $this->ErrorReporter("Unauthorized" , "You Do not have rights to edit this account", 400);
//        }



        return response()->json([""=>$request]);

//        $user_array = (array) $user;
//        Validator::make($user_array, [
//            'user_name' => ['required','min:3'],
//            'msisdn' => ['required','unique:users'],
//        ]);
        $user_acc->name = $user->name;
        $user_acc->user_name = $user->user_name;
        $user_acc->last_name = $user->last_name;
        $user_acc->middle_name = $user->middle_name;
        $user_acc->first_name = $user->first_name;
        $user_acc->msisdn = $user->msisdn;
        $user_acc->account_type = $user->account_type;
        $user_acc->avatar = $user->avatar;
        $user_acc->cover_photo = $user->cover_photo;

        $user_acc->save();
//
        return response (new UserResource($user_acc))->setStatusCode(200);


    }

    public function ErrorReporter(String $error_title , String $error_detail,Integer $status){
        return response([
            "message"=>"Unauthorised",
            "errors"=>[
                'detail' => ['Invalid Credentials']
            ]
        ], $status);
    }


}
