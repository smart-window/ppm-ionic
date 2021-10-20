<?php

namespace App\Service;

/**
 * This class is used for Mollie Payment gateway service
 * 
 *---------------------------------------------------------------- */
class MollieService
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
     * @var configItem - configItem
     */
    protected $mollie;

    /**
     * Constructor.
     *
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        $this->configData = configItem();
        //collect stripe data in config array
        $this->configItem = getArrayItem($this->configData, 'payments.gateway_configuration.mollie', []);
        // Get mollie payment gateway object
        $this->mollie = new \Mollie\Api\MollieApiClient();
        // Check if mollie config item exists
        if (!empty($this->configItem)) {
            // Check if mollie enabled
            if ($this->configItem['enable'] == true) {
                // Check if mollie is used in test mode or production mode
                if ($this->configItem['testMode'] == true) {
                    $this->mollie->setApiKey($this->configItem['testApiKey']);
                } else {
                    $this->mollie->setApiKey($this->configItem['liveApiKey']);
                }
            }
        }
    }

    /**
     * Process Mollies Payment Request
     *
     * @param  array $request
     *
     * request to molie checkout
     *---------------------------------------------------------------- */
    public function processMollieRequest($request)
    {
        try {
            $orderId = $request['order_id'];
            $payment = $this->mollie->payments->create([
                "amount" => [
                    "currency" => $this->configItem['currency'],
                    "value" => $request['amounts'][$this->configItem['currency']] // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                "description" => "Order #{$orderId}",
                "redirectUrl" => getAppUrl($this->configItem['callbackUrl']) . "?order_id={$orderId}&amount=" . $request['amounts'][$this->configItem['currency']] . "&paymentOption=mollie",
                "webhookUrl" => getAppUrl($this->configItem['callbackUrl']) . "?paymentOption=mollie-webhook",
                "metadata" => [
                    "order_id" => $orderId,
                ],
            ]);

            return [
                'message' => 'success',
                'checkoutUrl' => $payment->getCheckoutUrl()
            ];
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            //if payment failed set failed message
            $errorMessage['message'] = 'failed';

            //set error message if payment failed
            $errorMessage['errorMessage'] = htmlspecialchars($e->getMessage());

            //return error message array
            return (array) $errorMessage;
        }
    }

    /**
     * Prepare mobile webhook data
     *
     * @param  array $request
     *
     * request to molie checkout
     *---------------------------------------------------------------- */
    public function prepareMollieWebhookData($inputData)
    {
        try {
            $payment = $this->mollie->payments->get($inputData["id"]);
            $orderId = $payment->metadata->order_id;

            $webhookData = [
                'raw_data' => $payment
            ];

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
                /*
                 * The payment is paid and isn't refunded or charged back.
                 * At this point you'd probably want to start the process of delivering the product to the customer.
                 */
                $webhookData['status'] = 'paid';
            } elseif ($payment->isOpen()) {
                /*
                 * The payment is open.
                 */
                $webhookData['status'] = 'open';
            } elseif ($payment->isPending()) {
                /*
                 * The payment is pending.
                 */
                $webhookData['status'] = 'pending';
            } elseif ($payment->isFailed()) {
                /*
                 * The payment has failed.
                 */
                $webhookData['status'] = 'failed';
            } elseif ($payment->isExpired()) {
                /*
                 * The payment is expired.
                 */
                $webhookData['status'] = 'expired';
            } elseif ($payment->isCanceled()) {
                /*
                 * The payment has been canceled.
                 */
                $webhookData['status'] = 'canceled';
            } elseif ($payment->hasRefunds()) {
                /*
                 * The payment has been (partially) refunded.
                 * The status of the payment is still "paid"
                 */
                $webhookData['status'] = 'refund';
            } elseif ($payment->hasChargebacks()) {
                /*
                 * The payment has been (partially) charged back.
                 * The status of the payment is still "paid"
                 */
                $webhookData['status'] = 'chargeback';
            }

            return $webhookData;
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            $webhookData['message'] = htmlspecialchars($e->getMessage());

            return $webhookData;
        }
    }
}
