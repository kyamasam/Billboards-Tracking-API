<?php

namespace App\Http\Controllers;

use App\Http\Resources\MpesaStkCallbackCollection;
use App\Http\Resources\WalletResource;
use App\MpesaStkCallback;
use App\Wallet;
use Illuminate\Support\Facades\Hash;
use App\Traits\BaseTraits;

class WalletController extends Controller
{
    use BaseTraits;
    public function index(){
        $user = auth()->user();
        $user_wallet = $user->Wallet();
        if (!($user_wallet->count())) {
            // the user has no wallet thus create one
            $user_wallet = new Wallet();
            $user_wallet->user_id = $user->id;
            //save the balance directly
            $user_wallet->credit_balance =0;
            $zero=0;
            //create a credit verifier
            $user_wallet->credit_balance_verifier=Hash::make($user->id.(float)$zero);
            $user_wallet->save();
            return new WalletResource($user_wallet);
        }else{
            //the user actually has a wallet
            //verify their credit balance
            $user_wallet = $user_wallet->first();
            $zero=0;
            $new_hash =  Hash::make($user->id.(float)$zero);
            $stored_hash = $user_wallet->credit_balance_verifier;

            //todo compare hashes
//            if(!($new_hash == $stored_hash)){
//                return response()->json(["new"=>$new_hash,"stored"=>$stored_hash]);
//            }


            return new WalletResource($user_wallet);
        }
    }
    public function Transactions(){
        $user = auth()->user();

        $transactions = MpesaStkCallback::where('user_id','=',$user->id)->paginate();

        return new MpesaStkCallbackCollection($transactions);
    }

    public function AllTransactions()
    {
        $transactions = MpesaStkCallback::all();
        return new MpesaStkCallbackCollection($transactions);
//        if ($this->IsAdmin((int)auth()->user()->id)) {
//            $transactions = MpesaStkCallback::all();
//            return new MpesaStkCallbackCollection($transactions);
//        } else {
//            return $this->ErrorReporter("Unauthorized", "You Do not have rights to access this resource", 401);
//        }
    }
}
