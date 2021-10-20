<?php

namespace App\Components\Payment;

use App\Service\PagseguroService;

class PagseguroResponse
{
    /**
     * @var pagseguroService - PagseguroService
     */
    protected $pagseguroService;

    //construt method
    function __construct()
    {

        //create pagseguro instance
        $this->pagseguroService = new PagseguroService();
    }


    /**
     * fetch payment data
     * @var pagseguroService - PagseguroService
     */
    public function fetchTransactionByRefrenceId($referenceID)
    {
        //get pagseguro payment request data
        $pagseguroData = $this->pagseguroService->captureTransactionByReferenceId($referenceID);

        //return response data
        return $pagseguroData;
    }

    /**
     * fetch payment data
     * @var pagseguroService - PagseguroService
     */
    public function fetchTransactionByTxnCode($transactionCode)
    {
        //get pagseguro payment request data
        $pagseguroData = $this->pagseguroService->captureTransactionByTxnCode($transactionCode);

        //return response data
        return $pagseguroData;
    }
}
