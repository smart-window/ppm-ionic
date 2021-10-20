<?php

namespace App\Service;


use Unirest;
use Unirest\Request;
use Exception;

/**
 * This MailService class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class RavepayService
{
    /**
     * @var configData - configData
     */
    protected $configData;

    /**
     * @var configItem - configItem
     */
    protected $configItem;

    /**
     * Constructor.
     *
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        $this->configData = configItem();
    }

    /**

     * @param  string $ordderData - Order ID

     * request to Stripe checkout
     *---------------------------------------------------------------- */
    public function processRavepayRequest($request)
    {

        //get payment request details data in try catch block
        try {
            //collect ravepay data in config array
            $configItem = getArrayItem($this->configData, 'payments.gateway_configuration.ravepay', []);

            //check test mode or product mode set ravepaykeyId or ravepaySecretkey, 
            if (!empty($configItem)) {
                if ($configItem['testMode'] == true) {
                    $ravepaySecretkey = $configItem['testSecretApiKey'];
                    $verifyPaymentUrl = $configItem['sandboxVerifyPaymentUrl'];
                } else {
                    $ravepaySecretkey = $configItem['liveSecretApiKey'];
                    $verifyPaymentUrl = $configItem['productionVerifyPaymentUrl'];
                }
            }

            $data = array(
                'txref' => $request['ravepayTxnRefId'],
                'SECKEY' => $ravepaySecretkey //secret key from pay button generated on rave dashboard
            );

            // make request to endpoint using unirest.
            $headers = array('Content-Type' => 'application/json');
            //get data in request body
            $body = Request\Body::json($data);
            //url to staging server. please make sure to change when in production.
            $url = $verifyPaymentUrl;

            // Make `POST` request and handle response with unirest
            $response = Unirest\Request::post($url, $headers, $body);


            if ($response->body->status == 'error') {
                throw new Exception($response->body->message);
            }

            //ravepay amount
            $ravepayAmount = (int) $request['ravepayAmount'];

            //check the status is success
            if ($response->body->data->status === "successful" && $response->body->data->chargecode === "00") {

                //confirm that the amount is the amount you wanted to charge
                if ($response->body->data->amount === $ravepayAmount) {
                    //return transaction detail array
                    return (array) $response;
                }
            } else {
                //set error message if payment failed
                $errorMessage['errorMessage'] = 'Payment Failed';

                //return error message array
                return (array) $errorMessage;
            }

            //if error throw exception message
        } catch (Exception $e) {
            header("HTTP/1.0 500 Something went wrong");
            //set error message if payment failed
            $errorMessage['errorMessage'] = $e->getMessage();

            //return error message array
            return (array) $errorMessage;
        }
    }
}
