<?php

namespace App\Components\Payment;

use App\Service\RavepayService;

class RavepayResponse
{
    /**
     * @var ravepayservice - RavepayService
     */
    protected $ravepayService;

    //construt method
    function __construct()
    {

        //create ravepay instance
        $this->ravepayservice = new RavepayService();
    }

    public function getRavepayPaymentData($requestData)
    {
        //collect txnRefId and total amount for fetch payment data
        $ravepayPaymentData = [
            'ravepayTxnRefId' => $requestData['body']['data']['txref'],
            'ravepayAmount' => $requestData['body']['data']['amount']
        ];

        //get ravepay payment request data
        $ravepayData = $this->ravepayservice->processRavepayRequest($ravepayPaymentData);

        //return response data
        return  (array) $ravepayData;
    }
}
