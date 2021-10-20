<?php  
require('../assets/includes/config.php');
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
$mysqli->set_charset('utf8mb4');
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}

function paymentData($p,$s) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = '".$p."' and setting = '".$s."'");
    $result = $config->fetch_object();
    return $result->setting_val;
}

include 'header.php';
use App\Components\Payment\PaymentProcess;
use App\Service\PaytmService;
use App\Service\InstamojoService;
use App\Service\IyzicoService;
use App\Service\PaypalService;
use App\Service\PaystackService;
use App\Service\RazorpayService;
use App\Service\StripeService;
use App\Service\AuthorizeNetService;
use App\Service\BitPayService;
use App\Service\MercadopagoService;
use App\Service\PayUmoneyService;
use App\Service\MollieService;

if(paymentData('paytm','enabled') == 'Yes'){
    $paytmService       = new PaytmService();
} else {
    $paytmService = [];   
}
if(paymentData('instamojo','enabled') == 'Yes'){
    $instamojoService   = new InstamojoService();
} else {
    $instamojoService = []; 
}
if(paymentData('iyzico','enabled') == 'Yes'){
    $iyzicoService      = new IyzicoService();
} else {
    $iyzicoService = []; 
}
/*
if(paymentData('paypal','enabled') == 'Yes'){
    $paypalService      = new PaypalService();    
} else {
    $paypalService = []; 
}*/
$paypalService = []; 
if(paymentData('paystack','enabled') == 'Yes'){
    $paystackService      = new PaystackService();    
} else {
    $paystackService = []; 
}
if(paymentData('razorpay','enabled') == 'Yes'){
    $razorpayService      = new RazorpayService();
} else {
    $razorpayService = []; 
}
if(paymentData('stripe','enabled') == 'Yes'){
    $stripeService      = new StripeService();    
} else {
    $stripeService = []; 
}
if(paymentData('authorize','enabled') == 'Yes'){
    $authorizeNetService = new AuthorizeNetService();    
} else {
    $authorizeNetService = []; 
}
if(paymentData('bitpay','enabled') == 'Yes'){
    $bitPayService = new BitPayService();
} else {
    $bitPayService = []; 
}
if(paymentData('mercadopago','enabled') == 'Yes'){
    $mercadopagoService = new MercadopagoService();
} else {
    $mercadopagoService = []; 
}
if(paymentData('payu','enabled') == 'Yes'){
    $payUmoneyService = new PayUmoneyService();
} else {
    $payUmoneyService = []; 
}
if(paymentData('mollie','enabled') == 'Yes'){
    $mollieService = new MollieService();
} else {
    $mollieService = []; 
}

$ravepayService = [];
$pagseguroService = [];

$paymentProcess     = new PaymentProcess(
        $paytmService, 
        $instamojoService, 
        $iyzicoService, 
        $paypalService, 
        $paystackService, 
        $razorpayService, 
        $stripeService,
        $authorizeNetService,
        $bitPayService,
        $mercadopagoService,
        $payUmoneyService,
        $mollieService,
        $ravepayService,
        $pagseguroService        
);  

$gump = new GUMP();

//check post data is not empty
if (isset($_POST) && count($_POST) > 0 ) {
    // Sanitize form input data, remove tags for security purpose
    $insertData = $gump->sanitize($_POST);

    // Apply validation rule for post request.
    $validation = GUMP::is_valid($insertData, array(
        //'amount'        => 'required|numeric|min_numeric,0',
        'paymentOption' => 'required'
    ));
  
    $paymentOption = $insertData['paymentOption'];
    


    // amount, option, cardname, card number, expiry month, expiry year, cvv etc and validate it
    if ($paymentOption == 'iyzico' or $paymentOption == 'authorize-net') {
        $validation = GUMP::is_valid($insertData, array(
            //'amount'        => 'required|numeric',
            'paymentOption' => 'required',
            'cardname'     => 'required',
            'cardnumber'   => 'required',
            'expmonth'     => 'required',
            'expyear'      => 'required',
            'cvv'          => 'required'
        ));
    }

    // Check server side validation success then process for next step
    if ($validation === true) {
        global $mysqli;
        $mysqli->query('INSERT INTO orders (order_id,user_id,order_type,order_package,order_gateway,order_status,order_date) 
        VALUES ("'.$insertData['order_id'].'","'.$insertData['customer_id'].'","'.$insertData['order_type'].'","'.$insertData['order_package'].'","'.$paymentOption.'","process","'.time().'")');

        // Then send data to payment process service for process payment
        // This service will return payment data

        $paymentData = $paymentProcess->getPaymentData($insertData);


        // set select payment option in return paymentData array
        $paymentData['paymentOption'] = $paymentOption;

        //on success paytm response
        if ($paymentOption == 'paytm') {
           
            // If paytm payment method are selected then get payment merchant form
            $paymentData['merchantForm'] = getPaytmMerchantForm($paymentData);
           
            // return payment array on ajax request
            echo json_encode($paymentData);

            // on success instamojo, paystack, stripe, razorpay, iyzico & paypal response
        //} else if () {
            
        } else if ($paymentOption == 'instamojo' || $paymentOption == 'paystack' || $paymentOption == 'iyzico' || $paymentOption == 'paypal' || $paymentOption == 'stripe' || $paymentOption == 'authorize-net' || $paymentOption == 'bitpay' || $paymentOption == 'mercadopago' || $paymentOption == 'payumoney' || $paymentOption == 'mollie') {

            // return payment array on ajax request
            echo json_encode($paymentData);    

        } else if ($paymentOption == 'razorpay') {
            echo json_encode(array_values($paymentData)[0]);
        }

    } else {
        // If Validation errors occurred then show it on the form
        $validationMessage = [];
        
        // get collection of validation messages
        foreach ($validation as $valid) {
            $validationMessage['validationMessage'][] = strip_tags($valid);
        }
      
        // return validation array on ajax request
        echo json_encode($validationMessage);
        
        exit();
    }
}