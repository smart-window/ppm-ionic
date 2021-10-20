<?php

namespace App\Service;

use Exception;

/**
 * This MailService class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class MercadopagoService
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
        //collect mercadopago data in config array
        $this->configItem = getArrayItem($this->configData, 'payments.gateway_configuration.mercadopago', []);
        // Check if config item exists
        if (!empty($this->configItem)) {
            if ($this->configItem['enable'] == true) {
                if ($this->configItem['testMode'] == true) {
                    $mercadopagoAccessToken = $this->configItem['testAccessToken'];
                } else {
                    $mercadopagoAccessToken = $this->configItem['liveAccessToken'];
                }
                \MercadoPago\SDK::setAccessToken($mercadopagoAccessToken);
            }
        }
    }

    /**

     * @param  string $ordderData - Order ID
     * @param  string -$stripeToken - Stripe Token

     * request to Stripe checkout
     *---------------------------------------------------------------- */
    public function processMercadopagoRequest($request)
    {
        try {
            $amount = $request['amounts'][$this->configItem['currency']];
            // Create a preference object
            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item = new \MercadoPago\Item();
            $item->title = $request['item_name'];
            $item->quantity = $request['item_qty'];
            $item->unit_price = $amount;
            $preference->items = array($item);
            $backUrl = getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=mercadopago&order_id=' . $request['order_id'] . '&amount=$amount';
            // Back Urls
            $preference->back_urls = array(
                "success" => $backUrl,
                "failure" => $backUrl,
                "pending" => $backUrl
            );

            $ipnUrl = getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=mercadopago-ipn';
            $preference->notification_url = $ipnUrl;
            $preference->auto_return = "approved";
            $preference->save();

            // Create a customer object
            $payer = new \MercadoPago\Payer();
            // Create payer information
            $payer->name = $request['payer_name'];
            $payer->email = $request['payer_email'];
            $payer->address = array(
                "street_name" => $request['address']
            );

            // Check if test mode true or false
            // and set redirect url as per
            if ($this->configItem['testMode'] == true) {
                $redirectUrl = $preference->sandbox_init_point;
            } else {
                $redirectUrl = $preference->init_point;
            }

            return [
                'status'        => 'success',
                'id'            => $preference->id,
                'redirect_url'  => $redirectUrl
            ];
        } catch (Exception $e) {
            return [
                'status'   => 'error',
                'message'  => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @param  string $ordderData - Order ID
     * @param  string -$stripeToken - Stripe Token
     *
     * request to Stripe checkout
     *---------------------------------------------------------------- */
    public function prepareIpnRequestData($requestData)
    {
        $merchant_order = null;

        switch ($requestData["topic"]) {
            case "payment":
                $payment = \MercadoPago\Payment::find_by_id($requestData["id"]);
                // Get the payment and the corresponding merchant_order reported by the IPN.
                $merchant_order = \MercadoPago\MerchantOrder::find_by_id($payment->order->id);
                break;
            case "merchant_order":
                $merchant_order = \MercadoPago\MerchantOrder::find_by_id($requestData["id"]);
                break;
        }

        $paid_amount = 0;
        foreach ($merchant_order->payments as $payment) {
            if ($payment['status'] == 'approved') {
                $paid_amount += $payment['transaction_amount'];
            }
        }

        $status = $message = '';
        // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items
        if ($paid_amount >= $merchant_order->total_amount) {
            if (count($merchant_order->shipments) > 0) { // The merchant_order has shipments
                if ($merchant_order->shipments[0]->status == "ready_to_ship") {
                    $status = 'total_paid';
                    $message = "Totally paid. Print the label and release your item.";
                }
            } else { // The merchant_order don't has any shipments
                $status = 'total_paid';
                $message = "Totally paid. Release your item.";
            }
        } else {
            $status = 'not_paid';
            $message = "Not paid yet. Do not release your item.";
        }

        header("HTTP/1.1 200 OK");
        return [
            'status'    => $status,
            'message'   => $message,
            'raw_data'  => $merchant_order
        ];
    }
}
