<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Lyra\Client;
use Lyra\Exceptions\LyraException;
class IzipayController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setUsername(env('IZIPAY_USERNAME'));
        $this->client->setPassword(env('IZIPAY_PASSWORD'));
        $this->client->setEndpoint(env('IZIPAY_ENDPOINT'));
        $this->client->setPublicKey(env('IZIPAY_PUBLIC_KEY'));
        $this->client->setSHA256Key(env('IZIPAY_SHA256_KEY'));
        $this->client->setClientEndpoint(env('IZIPAY_CLIENT_ENDPOINT'));
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
        $response = $this->client->post("V4/Charge/CreatePayment", $store);

        if ($response['status'] != 'SUCCESS') {
            echo ($response);
            $error = $response['answer'];
            throw new LyraException("error " . $error['errorCode'] . ": " . $error['errorMessage']);
        }

        $formToken = $response["answer"]["formToken"];
        return view('izipay.incrustado', compact('formToken'));
    }

    public function success(Request $request)
    {
        if (empty($_POST)) throw new LyraException("no post data received!");

        $formAnswer = $this->client->getParsedFormAnswer();

        if (!$this->client->checkHash()) {
            //something wrong, probably a fraud ....
            throw new LyraException('invalid signature');
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
        if (empty($_POST)) throw new LyraException('no post data received!');
        if (!$this->client->checkHash()) throw new LyraException('invalid signature');

        /* Retrieve the IPN content */
        $rawAnswer = $this->client->getParsedFormAnswer();
        $formAnswer = $rawAnswer['kr-answer'];
        /* Retrieve the transaction id from the IPN data */
        $transaction = $formAnswer['transactions'][0];
        /* get some parameters from the answer */
        $orderStatus = $formAnswer['orderStatus'];
        $orderId = $formAnswer['orderDetails']['orderId'];
        $transactionUuid = $transaction['uuid'];
        /* I update my database if needed */
        /* Add here your custom code */

        /**
         * Message returned to the IPN caller
         * You can return want you want but
         * HTTP response code should be 200
         */
        print 'OK! OrderStatus is ' . $orderStatus;
    }

    public function createPayment(Request $request)
    {

        $store = array(
            "amount" => 250,
            "currency" => "PEN",
            "orderId" => uniqid("MyOrderId"),
            "customer" => array(
                "email" => "sample@example.com"
            )
        );

        $response = $this->client->post("V4/Charge/CreatePayment", $store);

        /* I check if there are some errors */
        if ($response['status'] != 'SUCCESS') {
            /* an error occurs, I throw an exception */
            echo ($response);
            $error = $response['answer'];
            throw new LyraException("error " . $error['errorCode'] . ": " . $error['errorMessage']);
        }
        /* everything is fine, I extract the formToken */
        $formToken = $response["answer"]["formToken"];

        $data = [
            'status' => 200,
            'formToken' => $formToken
        ];

        return response()->json($data);
    }
}
