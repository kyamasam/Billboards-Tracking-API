<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

$time_of_new_token='';

class StkPushContoller extends Controller
{


    /*
     * im using this to generate auth token in the sandbox mode
     * @return mixed
     */

    public function generate_sandbox_token(){

        try {
            $consumer_key = env("MPESA_CONSUMER_KEY");
            $consumer_secret = env("MPESA_CONSUMER_SECRET");
        } catch (\Throwable $th) {
            $consumer_key = self::env("MPESA_CONSUMER_KEY");
            $consumer_secret = self::env("MPESA_CONSUMER_SECRET");
        }
        if(!isset($consumer_key)||!isset($consumer_secret)){
            die("please declare the consumer key and consumer secret as defined in the documentation");
        }
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
//        date_default_timezone_set('Africa/Nairobi');
//        $GLOBALS["time_of_new_token"]=date("h:i:s");
        return json_decode($curl_response)->access_token;


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($phone,$paybill=null)
    {
        if(isset($paybill)){

        }
        else{
            $paybill = 174379;
        }
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generate_sandbox_token())); //setting custom header


        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => '174379',
            'Password' => 'MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMTkwMTI1MTYzMTI2',
            'Timestamp' => '20190125163126',
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => '1',
            'PartyA' => $phone,
            'PartyB' => $paybill,
            'PhoneNumber' => $phone,
//            'PhoneNumber' => '254716651687',
            'CallBackURL' => 'http://sefapay.skalityprojects.com//stkpush_callback.php',
            'AccountReference' => 'ref',
            'TransactionDesc' => 'please work'

        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
//        print_r($curl_response);

        return $curl_response;


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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



}
