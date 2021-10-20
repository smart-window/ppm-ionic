<?php

namespace App\Components\Payment;

use App\Service\PayUmoneyService;

class PayUmoneyResponse
{
    /**
     * @var PayUmoney - PayUmoney
     */
    protected $payUmoneyService;

    //construt method
    function __construct()
    {

        //create instamojo instance
        $this->payUmoneyService = new PayUmoneyService();
    }

    /**
     * Get PayUmoney payment response
     * @param array $requestData
     *
     *---------------------------------------------------------------- */
    public function getPayUmoneyPaymentResponse($requestData)
    {

        //get payUmoney payment request data
        $payUmoneyData = $this->payUmoneyService->preparePayUmoneyPaymentResponse($requestData);

        //return response data
        return $payUmoneyData;
    }
}
