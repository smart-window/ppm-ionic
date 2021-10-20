<?php
//Set Access-Control-Allow-Origin with PHP
// header('Access-Control-Allow-Origin: http://site-a.com', false);
namespace App\Components\Payment;

class PaymentProcess
{

    /**
     * @var paytmService - Paytm Service
     */
    protected $paytmService;

    /**
     * @var instamojoService - Instamojo Service
     */
    protected $instamojoService;

    /**
     * @var iyzicoService - Iyzico Service
     */
    protected $iyzicoService;

    /**
     * @var paypalService - Paypal Service
     */
    protected $paypalService;

    /**
     * @var paystackService - Paystack Service
     */
    protected $paystackService;

    /**
     * @var razorpayService - Razorpay Service
     */
    protected $razorpayService;

    /**
     * @var stripeService - Stripe Service
     */
    protected $stripeService;

    /**
     * @var authorizeNetService - Authorize.Net Service
     */
    protected $authorizeNetService;

    /**
     * @var bitPayService - BitPay Service
     */
    protected $bitPayService;

    /**
     * @var mercadopagoService - Mercadopago Service
     */
    protected $mercadopagoService;

    /**
     * @var payUmoneyService - Mercadopago Service
     */
    protected $payUmoneyService;

    /**
     * @var mollieService - mollie Service
     */
    protected $mollieService;

    /**
     * @var ravepayService - ravepay Service
     */
    protected $ravepayService;

    /**
     * @var pagseguroService - pagseguro Service
     */
    protected $pagseguroService;

    function __construct($paytmService, $instamojoService, $iyzicoService, $paypalService, $paystackService, $razorpayService, $stripeService, $authorizeNetService, $bitPayService, $mercadopagoService, $payUmoneyService, $mollieService, $ravepayService, $pagseguroService)
    {
        $this->paytmService          = $paytmService;
        $this->instamojoService      = $instamojoService;
        $this->iyzicoService         = $iyzicoService;
        $this->paypalService         = $paypalService;
        $this->paystackService       = $paystackService;
        $this->razorpayService       = $razorpayService;
        $this->stripeService         = $stripeService;
        $this->authorizeNetService   = $authorizeNetService;
        $this->bitPayService         = $bitPayService;
        $this->mercadopagoService    = $mercadopagoService;
        $this->payUmoneyService      = $payUmoneyService;
        $this->mollieService         = $mollieService;
        $this->ravepayService        = $ravepayService;
        $this->pagseguroService      = $pagseguroService;
    }

    public function getPaymentData($request)
    {
        $processResponse = [];
        if ($request['paymentOption'] == 'paytm') {
            //get paytm request data
            $processResponse = $this->paytmService->handlePaytmRequest($request);

            return $processResponse;
        } else if ($request['paymentOption'] == 'instamojo') {
            //get instamojo request data
            $processResponse = $this->instamojoService->processInstamojoRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'iyzico') {
            //get iyzico request data
            $processResponse = $this->iyzicoService->processIyzicoRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'paypal') {
            //get paypal request data
            $processResponse = $this->paypalService->processPaypalRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'stripe') {
            // Get Stripe request Data
            $processResponse = $this->stripeService->processStripeRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'paystack') {
            // Get Stripe request Data
            $processResponse = $this->paystackService->processPaystackRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'razorpay') {
            // Get Stripe request Data
            $processResponse = $this->razorpayService->processRazorpayRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'authorize-net') {
            $processResponse = $this->authorizeNetService->processAuthorizeNetRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'bitpay') {
            $processResponse = $this->bitPayService->processBitPayRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'mercadopago') {
            $processResponse = $this->mercadopagoService->processMercadopagoRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'payumoney') {
            $processResponse = $this->payUmoneyService->processPayUmoneyRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'mollie') {
            $processResponse = $this->mollieService->processMollieRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'ravepay') {
            $processResponse = $this->ravepayService->processRavepayRequest($request);
            return $processResponse;
        } else if ($request['paymentOption'] == 'pagseguro') {
            $processResponse = $this->pagseguroService->processPagseguroRequest($request);
            return $processResponse;
        }
    }
}
