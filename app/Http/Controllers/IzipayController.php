<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use Lyra\Client;
use Lyra\Exceptions\LyraException;

class IzipayController extends Controller
{
    private $client;

    public function __construct()
    {
    }

    public function getFormToken()
    {
        $store = array(
            "amount" => 250,
            "currency" => "PEN",
            "orderId" => uniqid("MyOrderId"),
            "customer" => array(
                "email" => "sample@example.com",
            )
        );
        $response = $this->post("V4/Charge/CreatePayment", $store);

        if ($response['status'] != 'SUCCESS') {
            echo ($response);
            $error = $response['answer'];
            throw ("error " . $error['errorCode'] . ": " . $error['errorMessage']);
        }

        $formToken = $response["answer"]["formToken"];
        return view('izipay.incrustado', compact('formToken'));
    }

    public function success(Request $request)
    {
        if (empty($_POST)) throw ("no post data received!");

        $formAnswer['kr-hash'] = $_POST['kr-hash'];
        $formAnswer['kr-hash-algorithm'] = $_POST['kr-hash-algorithm'];
        $formAnswer['kr-answer-type'] = $_POST['kr-answer-type'];
        $formAnswer['kr-answer'] = json_decode($_POST['kr-answer'], true);

        if (!$this->checkHash()) {
            //something wrong, probably a fraud ....
            throw ('invalid signature');
        }

        if ($formAnswer['kr-answer']['orderStatus'] != 'PAID') {
            return 'Transaction not paid !';
        } else {
            $dataPost = json_encode($_POST, JSON_PRETTY_PRINT);
            $formAnswer = json_encode($formAnswer["kr-answer"], JSON_PRETTY_PRINT);
            return view('izipay.paid', compact('formAnswer', 'dataPost'));
        }
    }

    public function notificationIpn(Request $request)
    {
        if (empty($_POST)) throw 'no post data received!';
        if (!$this->checkHash()) throw 'invalid signature';

        /* Retrieve the IPN content */
        $rawAnswer['kr-hash'] = $_POST['kr-hash'];
        $rawAnswer['kr-hash-algorithm'] = $_POST['kr-hash-algorithm'];
        $rawAnswer['kr-answer-type'] = $_POST['kr-answer-type'];
        $rawAnswer['kr-answer'] = json_decode($_POST['kr-answer'], true);

        $formAnswer = $rawAnswer['kr-answer'];
        /* Retrieve the transaction id from the IPN data */
        $transaction = $formAnswer['transactions'][0];
        /* get some parameters from the answer */
        $orderStatus = $formAnswer['orderStatus'];
        $orderId = $formAnswer['orderDetails']['orderId'];
        $transactionUuid = $transaction['uuid'];

        print 'OK! OrderStatus is ' . $orderStatus;
    }

    public function createPayment(Request $request)
    {

        $store = array(
            "amount" => 250,
            "currency" => "PEN",
            "orderId" => uniqid("MyOrderId-"),
            "customer" => array(
                "email" => "sample@example.com"
            )
        );

        $response = $this->post("V4/Charge/CreatePayment", $store);

        /* I check if there are some errors */
        if ($response['status'] != 'SUCCESS') {
            /* an error occurs, I throw an exception */
            echo ($response);
            $error = $response['answer'];
            throw ("error " . $error['errorCode'] . ": " . $error['errorMessage']);
        }
        /* everything is fine, I extract the formToken */
        $formToken = $response["answer"]["formToken"];

        $data = [
            'status' => 200,
            'formToken' => $formToken
        ];

        return response()->json($data);
    }



    private function post(string $target, array $datos)
    {
        $auth = env('IZIPAY_USERNAME') . ":" . env('IZIPAY_PASSWORD');
        $url = env('IZIPAY_ENDPOINT') . "/api-payment/" . $target;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERPWD, $auth);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        $raw_response = curl_exec($curl);
        $response = json_decode($raw_response, true);
        return $response;
    }

    private function checkHash()
    {
        if (!in_array($_POST["kr-hash-algorithm"], array("sha256_hmac"))) return false;

        if ($_POST['kr-hash-algorithm'] == "sha256_hmac") {
            $key = env('IZIPAY_SHA256_KEY');
        } elseif ($_POST['kr-hash-algorithm'] == "password") {
            $key = env('IZIPAY_PASSWORD');
        } else {
            return false;
        }
        /* on some servers, / can be escaped */
        $krAnswer = str_replace('\/', '/',  $_POST["kr-answer"]);
        $calculateHash = hash_hmac("sha256", $krAnswer, $key);

        return ($calculateHash == $_POST["kr-hash"]);
    }
}
