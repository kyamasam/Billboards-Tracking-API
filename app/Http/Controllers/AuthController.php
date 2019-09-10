<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Traits\BaseTraits;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Integer;

class AuthController extends Controller

{
    use BaseTraits;
    public function register(Request $request ){

        //todo: validate length of phone numbers
        $request->validate([
            'user_name' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'msisdn' => 'required|numeric|unique:users',
            'password' => 'required|min:6|confirmed',
        ],[
            'msisdn.required'=>'Phone Number is required',
            'msisdn.numeric'=>'Phone Number should be numeric',
            'msisdn.unique'=>'Phone Number has already been taken',
            ]);

        $user= User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'msisdn' => $request->msisdn,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('AdkloutToken')->accessToken;

        //create a user wallet
        $this->createUserWallet($user->id);
        return response()->json([
            "type"=>'token',
            "id"=>$user->id,
            "attributes"=> [
                'user_name' =>$request->user_name,
                'token' => $token
            ]

        ],
        200);    }

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
