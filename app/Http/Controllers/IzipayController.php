<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;


class IzipayController extends Controller
{
    public function index(){
        return view('izipay.index');
    }

    public function checkout(){

        $formToken = $this->getFormToken();
        $publicKey = env("IZIPAY_PUBLIC_KEY");

        return view('izipay.checkout', compact("publicKey", "formToken"));
    }

    public function result(Request $request){
        if (empty($request)) {
            throw new Exception("No post data received!");
        }
          
        // Validación de firma
        if (!$this->checkHash($request)) {
            throw new Exception("Invalid signature");
        }
        
        $answer = json_decode($request['kr-answer'], true);
        $orderStatus = $answer['orderStatus'];

        return view('izipay.result', compact('orderStatus', 'answer'));
    }


    public function getFormToken()
    {
        $datos = array(
            "amount" => 250,
            "currency" => "PEN",
            "orderId" => uniqid("MyOrderId"),
            "customer" => array(
                "email" => "sample@example.com",
            )
        );

        $auth = env('IZIPAY_USERNAME') . ":" . env('IZIPAY_PASSWORD');
        $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERPWD, $auth);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $raw_response = curl_exec($curl);
        $response = json_decode($raw_response, true);

        if ($response['status'] != 'SUCCESS') {
            echo ($response);
            $error = $response['answer'];
            throw ("error " . $error['errorCode'] . ": " . $error['errorMessage']);
        }

        return  $response["answer"]["formToken"];
    }

    public function ipn(Request $request)
    {
       
        if (empty($request)) throw new Exception("No post data received!");

        // Validación de firma en IPN
        if (!$this->checkHash($request)) throw new Exception("Invalid signature");

        // Ejemplos de extracción de datos
        $answer = json_decode($request["kr-answer"], true);
        $transaction = $answer['transactions'][0];
        
        // Verifica orderStatus PAID
        $orderStatus = $answer['orderStatus'];
        $orderId = $answer['orderDetails']['orderId'];
        $transactionUuid = $transaction['uuid'];

        print 'OK! OrderStatus is ' . $orderStatus;
    }


    private function checkHash(Request $request)
    {
        if ($request['kr-hash-key'] == "sha256_hmac") {
            $key = env('IZIPAY_SHA256_KEY');
        } elseif ($request['kr-hash-key'] == "password") {
            $key = env('IZIPAY_PASSWORD');
        } else {
            return false;
        }

        $krAnswer = str_replace('\/', '/',  $request["kr-answer"]);
        $calculateHash = hash_hmac("sha256", $krAnswer, $key);

        return ($calculateHash == $request["kr-hash"]);
    }
}
