<?php

namespace App\Components\Payment;

use App\Service\MollieService;

class MollieResponse
{

    /**
     * @var stripeData - StripeData
     */
    protected $mollieService;

    //construct method
    function __construct()
    {

        //create stripe instance
        $this->mollieService = new MollieService();
    }

    public function retrieveMollieWebhookData($inputData)
    {
        //get stripe payment request data
        return $this->mollieService->prepareMollieWebhookData($inputData);
    }
}
