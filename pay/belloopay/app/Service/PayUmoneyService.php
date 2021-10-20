<?php

namespace App\Service;

/**
 * PayUmoney Payment Process Service
 * 
 *---------------------------------------------------------------- */
class PayUmoneyService
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
        $this->configData       = configItem();
        $this->configItem       = getArrayItem($this->configData, 'payments.gateway_configuration.payumoney', []);
    }

    /**
     * Process PayUmoney payment data
     * @param  string $ordderData - Order ID
     * @param  string -$instamojoPyamentId - Instamojo Payment Id

     * request to Instamojo checkout
     *---------------------------------------------------------------- */
    public function processPayUmoneyRequest($request)
    {
        // Check if payment gateway is enable
        if ($this->configItem['enable'] == true) {
            if ($this->configItem['testMode'] == true) {
                $merchantKey = $this->configItem['merchantTestKey'];
                $merchantSalt = $this->configItem['merchantTestSalt'];
            } else {
                $merchantKey = $this->configItem['merchantLiveKey'];
                $merchantSalt = $this->configItem['merchantLiveSalt'];
            }

            $hash = hash('sha512', $merchantKey . '|' . $this->configItem['txnId'] . '|' . $request['amounts'][$this->configItem['currency']] . '|' . $request['item_name'] . '|' . $request['payer_name'] . '|' . $request['payer_email'] . '|||||' . 'BOLT_KIT_PHP7' . '||||||' . $merchantSalt);

            return [
                'status'        => true,
                'key'           => $merchantKey,
                'txnid'         => $this->configItem['txnId'],
                'hash'          => $hash,
                'amount'        => $request['amounts'][$this->configItem['currency']],
                'firstname'     => $request['payer_name'],
                'email'         => $request['payer_email'],
                'phone'         => $request['payer_mobile'],
                'productinfo'   => $request['item_name'],
                'udf5'          => 'BOLT_KIT_PHP7',
                'surl'          => getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=payumoney&order_id=' . $request['order_id'],
                'furl'          => getAppUrl($this->configItem['callbackUrl']),
                'mode'          => 'dropout'
            ];
        }

        return [
            'status' => false
        ];
    }

    /**
     * Process PayUmoney payment data
     * @param  string $ordderData - Order ID
     * @param  string -$instamojoPyamentId - Instamojo Payment Id

     * request to Instamojo checkout
     *---------------------------------------------------------------- */
    public function preparePayUmoneyPaymentResponse($requestData)
    {
        if ($this->configItem['testMode'] == true) {
            $merchantKey = $this->configItem['merchantTestKey'];
            $merchantSalt = $this->configItem['merchantTestSalt'];
        } else {
            $merchantKey = $this->configItem['merchantLiveKey'];
            $merchantSalt = $this->configItem['merchantLiveSalt'];
        }

        $responseHash = $requestData['hash'];
        $keyString = $merchantKey . '|' . $requestData['txnid'] . '|' . $requestData['amount'] . '|' . $requestData['productinfo'] . '|' . $requestData['firstname'] . '|' . $requestData['email'] . '|||||' . $requestData['udf5'] . '|||||';
        $keyArray = explode("|", $keyString);
        $reverseKeyArray = array_reverse($keyArray);
        $reverseKeyString = implode("|", $reverseKeyArray);
        $calcHashString = strtolower(hash('sha512', $merchantSalt . '|' . $requestData['status'] . '|' . $reverseKeyString));

        // Check if status is success
        if ($requestData['status'] == 'success' and $responseHash == $calcHashString) {
            return [
                'status'    => 'success',
                'order_id'  => $requestData['order_id'],
                'txn_id'    => $requestData['txnid'],
                'amount'    => $requestData['amount'],
                'raw_Data' => $requestData
            ];
        } else {
            return [
                'status' => 'failed',
                'order_id'  => $requestData['order_id'],
                'txn_id'    => $requestData['txnid'],
                'amount'    => $requestData['amount'],
                'raw_Data' => $requestData
            ];
        }
    }
}
