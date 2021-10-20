<?php
require('../assets/includes/config.php');
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
$mysqli->set_charset('utf8mb4');
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}

function getData($table,$col,$filter=''){
    global $mysqli;
    $q = $mysqli->query("SELECT $col FROM $table $filter");
    $result = 'noData';
    if($q->num_rows >= 1) {
        $r = $q->fetch_object();
        $result = $r->$col;
    }
    return $result;
}

function updateData($table,$col,$val,$filter=''){
    global $mysqli;
    $mysqli->query("UPDATE $table SET $col = $val $filter");
}

include 'header.php';

use App\Components\Payment\PaytmResponse;
use App\Components\Payment\PaystackResponse;
use App\Components\Payment\StripeResponse;
use App\Components\Payment\RazorpayResponse;
use App\Components\Payment\InstamojoResponse;
use App\Components\Payment\IyzicoResponse;
use App\Components\Payment\PaypalIpnResponse;
use App\Components\Payment\BitPayResponse;
use App\Components\Payment\MercadopagoResponse;
use App\Components\Payment\PayUmoneyResponse;
use App\Components\Payment\MollieResponse;

// Get Config Data 
$configData = configItem();
// Get Request Data when payment success or failed
$requestData = $_REQUEST;


// Check payment Method is paytm
if ($requestData['paymentOption'] == 'paytm') {
    // Get Payment Response instance
    $paytmResponse  = new PaytmResponse();

    // Fetch payment data using payment response instance
    $paytmData = $paytmResponse->getPaytmPaymentData($requestData);
    
    // Check if payment status is success
    if ($paytmData['STATUS'] == 'TXN_SUCCESS') {

        // Create payment success response data.
        $paymentResponseData = [
            'status'   => true,
            'rawData'  => $paytmData,
            'data'     => preparePaymentData($paytmData['ORDERID'], $paytmData['TXNAMOUNT'], $paytmData['TXNID'], 'paytm')
        ];
        // Send data to payment response.
        paymentResponse($paymentResponseData);
    } else {
        // Create payment failed response data.
        $paymentResponseData = [
            'status'   => false,
            'rawData'  => $paytmData,
            'data'     => preparePaymentData($paytmData['ORDERID'], $paytmData['TXNAMOUNT'], $paytmData['TXNID'], 'paytm')
        ];
        // Send data to payment response function
        paymentResponse($paymentResponseData);
    }   
// Check payment method is instamojo
} else if ($requestData['paymentOption'] == 'instamojo') {
    
    // Check if payment successfully procced
    if ($requestData['payment_status'] == "Credit") {

        // Get Instance of instamojo response service
        $instamojoResponse  = new InstamojoResponse();

        // fetch payment data from instamojo response instance
        $instamojoData = $instamojoResponse->getInstamojoPaymentData($requestData);
        
        // Prepare data for payment response
        $paymentResponseData = [
            'status'   => true,
            'rawData'  => $instamojoData,
            'data'     => preparePaymentData($requestData['orderId'], $instamojoData['amount'], $instamojoData['payment_id'], 'instamojo')
        ];
        // Send data to payment response
        paymentResponse($paymentResponseData);
    // Check if payment failed then send failed response
    } else {
        // Prepare data for failed response data
        $paymentResponseData = [
            'status'   => false,
            'rawData'  => $requestData,
            'data'     => preparePaymentData($requestData['orderId'], $instamojoData['amount'], null, 'instamojo')
        ];
        // Send data to payment response function
        paymentResponse($paymentResponseData);
    }

// Check if payment method is iyzico.
} else if ($requestData['paymentOption'] == 'iyzico') {
    
    // Check if payment status is success for iyzico.
    if ($_REQUEST['status'] == 'success') {
        // Get iyzico response.
        $iyzicoResponse  = new IyzicoResponse();

        // fetch payment data using iyzico response instance.
        $iyzicoData = $iyzicoResponse->getIyzicoPaymentData($requestData);
        $rawResult = json_decode($iyzicoData->getRawResult(), true);
        
        // Check if iyzico payment data is success
        // Then create a array for success data
        if ($iyzicoData->getStatus() == 'success') {
            $paymentResponseData = [
                'status'   => true,
                'rawData'  => (array) $iyzicoData,
                'data'     => preparePaymentData($requestData['orderId'], $rawResult['price'], $rawResult['conversationId'], 'iyzico')
            ];
            // Send data to payment response
            paymentResponse($paymentResponseData);
        // If payment failed then create data for failed
        } else {
            // Prepare failed payment data
            $paymentResponseData = [
                'status'   => false,
                'rawData'  => (array) $iyzicoData,
                'data'     => preparePaymentData($requestData['orderId'], $rawResult['price'], $rawResult['conversationId'], 'iyzico')
            ];
            // Send data to payment response
            paymentResponse($paymentResponseData);
        }
    // Check before 3d payment process payment failed
    } else {
        // Prepare failed payment data
        $paymentResponseData = [
            'status'   => false,
            'rawData'  => $requestData,
            'data'     => preparePaymentData($requestData['orderId'], $rawResult['price'], null, 'iyzico')
        ];
        // Send data to process response
        paymentResponse($paymentResponseData);
    }

// Check Paypal payment process
} else if ($requestData['paymentOption'] == 'paypal') {
    // Get instance of paypal 
    $paypalIpnResponse  = new PaypalIpnResponse();

    // fetch paypal payment data
    $paypalIpnData = $paypalIpnResponse->getPaypalPaymentData();
    $rawData = json_decode($paypalIpnData, true);

    // Note : IPN and redirects will come here
    // Check if payment status exist and it is success
    if (isset($requestData['payment_status']) and $requestData['payment_status'] == "Completed") {

        // Then create a data for success paypal data
        $paymentResponseData = [
            'status'    => true,
            'rawData'   => (array) $paypalIpnData,
            'data'     => preparePaymentData($rawData['invoice'], $rawData['payment_gross'], $rawData['txn_id'], 'paypal')
        ];
        // Send data to payment response function for further process
        paymentResponse($paymentResponseData);
    // Check if payment not successfull    
    } else {
        // Prepare payment failed data
        $paymentResponseData = [
            'status'   => false,
            'rawData'  => [],
            'data'     => preparePaymentData($rawData['invoice'], $rawData['payment_gross'], null, 'paypal')
        ];
        // Send data to payment response function for further process
        paymentResponse($paymentResponseData);
    }

// Check Paystack payment process
} else if ($requestData['paymentOption'] == 'paystack') {

    $requestData = json_decode($requestData['response'], true);
    
    // Check if status key exists and payment is successfully completed
    if (isset($requestData['status']) and $requestData['status'] == "success") {
        // Create data for payment success
        $paymentResponseData = [
            'status'   => true,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($requestData['data']['reference'], $requestData['data']['amount'], $requestData['data']['reference'], 'paystack')
        ];
        // Send data to payment response for further process
        paymentResponse($paymentResponseData);
    // If paystack payment is failed    
    } else {
        // Prepare data for failed payment 
        $paymentResponseData = [
            'status'   => false,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($requestData['data']['reference'], $requestData['data']['amount'], $requestData['data']['reference'], 'paystack')
        ];
        // Send data to payment response to further process
        paymentResponse($paymentResponseData);
    }

// Check Stripe payment process
} else if ($requestData['paymentOption'] == 'stripe') {

    $stripeResponse = new StripeResponse();

    $stripeData = $stripeResponse->retrieveStripePaymentData($requestData['stripe_session_id']);
    

    // Check if payment charge status key exist in stripe data and it success
    if (isset($stripeData['status']) and $stripeData['status'] == "succeeded") {
        // Prepare data for success
        $paymentResponseData = [
            'status'   => true,
            'rawData'   => $stripeData,
            'data'     => preparePaymentData($requestData['orderId'], $stripeData->amount, $stripeData->charges->data[0]['balance_transaction'], 'stripe')
        ];
        
    // Check if stripe data is failed    
    } else {
        // Prepare failed payment data
        $paymentResponseData = [
            'status'   => false,
            'rawData'   => $stripeData,
            'data'     => preparePaymentData($requestData['orderId'], $stripeData->amount, null, 'stripe')
        ];
    }

    // Send data to payment response for further process
    paymentResponse($paymentResponseData);

// Check Razorpay payment process
} else if ($requestData['paymentOption'] == 'razorpay') {
    $orderId = $requestData['orderId'];
    
    $requestData = json_decode($requestData['response'], true);
    
    // Check if razorpay status exist and status is success
    if (isset($requestData['status']) and $requestData['status'] == 'captured') {
        // prepare payment data
        $paymentResponseData = [
            'status'   => true,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($orderId, $requestData['amount'], $requestData['id'], 'razorpay')
        ];
        // send data to payment response
        paymentResponse($paymentResponseData);
    // razorpay status is failed
    } else {
        // prepare payment data for failed payment
        $paymentResponseData = [
            'status'   => false,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($orderId, $requestData['amount'], $requestData['id'], 'razorpay')
        ];
        // send data to payment response
        paymentResponse($paymentResponseData);
    }
} else if ($requestData['paymentOption'] == 'authorize-net') {
    $orderId = $requestData['order_id'];
    
    $requestData = json_decode($requestData['response'], true);
    
    // Check if razorpay status exist and status is success
    if (isset($requestData['status']) and $requestData['status'] == 'success') {
        // prepare payment data
        $paymentResponseData = [
            'status'   => true,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($orderId, $requestData['amount'], $requestData['transaction_id'], 'authorize-net')
        ];
        // send data to payment response
        paymentResponse($paymentResponseData);
    // razorpay status is failed
    } else {
        // prepare payment data for failed payment
        $paymentResponseData = [
            'status'   => false,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($orderId, $requestData['amount'], $requestData['transaction_id'], 'authorize-net')
        ];
        // send data to payment response
        paymentResponse($paymentResponseData);
    }
} else if ($requestData['paymentOption'] == 'bitpay') {
    // prepare payment data
    $paymentResponseData = [
        'status'   => true,
        'rawData'  => $requestData,
        'data'     => preparePaymentData($requestData['orderId'], $requestData['amount'], $requestData['orderId'], 'bitpay')
    ];
    // send data to payment response
    paymentResponse($paymentResponseData);
} else if ($requestData['paymentOption'] == 'bitpay-ipn') {
    $bitpayResponse = new BitPayResponse;
    $rawPostData = file_get_contents('php://input');
    $ipnData = $bitpayResponse->getBitPayPaymentData($rawPostData);
    if ($ipnData['status'] == 'success') {
        // code here
    } else {
        // code here
    }
} else if ($requestData['paymentOption'] == 'mercadopago') {
    if ($requestData['collection_status'] == 'approved') {
        $paymentResponseData = [
            'status'   => true,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($requestData['order_id'], $requestData['amount'], $requestData['collection_id'], 'mercadopago')
        ];
    } elseif ($requestData['collection_status'] == 'pending') {
        $paymentResponseData = [
            'status'   => 'pending',
            'rawData'   => $requestData,
            'data'     => preparePaymentData($requestData['order_id'], $requestData['amount'], $requestData['collection_id'], 'mercadopago')
        ];
    } else {
        $paymentResponseData = [
            'status'   => false,
            'rawData'   => $requestData,
            'data'     => preparePaymentData($requestData['order_id'], $requestData['amount'], $requestData['collection_id'], 'mercadopago')
        ];
    }
    paymentResponse($paymentResponseData);
} else if ($requestData['paymentOption'] == 'mercadopago-ipn') {
    $mercadopagoResponse = new MercadopagoResponse;
    $mercadopagoIpnData = $mercadopagoResponse->getMercadopagoPaymentData($requestData);

    // Ipn data recieved here are as following
    //$mercadopagoIpnData['status'] = 'total_paid or not_paid';
    //$mercadopagoIpnData['message'] = 'Message';    
    //$mercadopagoIpnData['raw_data'] = 'Raw Ipn Data';   
} else if ($requestData['paymentOption'] == 'payumoney') {
    $payUmoneyResponse = new PayUmoneyResponse;
    $payUmoneyResponseData = $payUmoneyResponse->getPayUmoneyPaymentResponse($requestData);
    if ($payUmoneyResponseData['status'] == 'success') {
        $paymentResponseData = [
            'status'    => true,
            'order_id'  => $payUmoneyResponseData['raw_Data'],
            'rawData'   => $payUmoneyResponseData['raw_Data'],
            'data'      => preparePaymentData($payUmoneyResponseData['order_id'], $payUmoneyResponseData['amount'], $payUmoneyResponseData['txn_id'], 'payumoney')
        ];
    } else if ($payUmoneyResponseData['status'] == 'failed') {
        $paymentResponseData = [
            'status'    => false,
            'order_id'  => '',
            'rawData'   => $payUmoneyResponseData['raw_Data'],
            'data'      => preparePaymentData($payUmoneyResponseData['order_id'], $payUmoneyResponseData['amount'], $payUmoneyResponseData['txn_id'], 'payumoney')
        ];
    }

    paymentResponse($paymentResponseData);

} else if ($requestData['paymentOption'] == 'mollie') {

    $paymentResponseData = [
        'status'    => true,
        'order_id'  => $requestData['order_id'],
        'rawData'   => $requestData,
        'data'      => preparePaymentData($requestData['order_id'], $requestData['amount'], null, 'mollie')
    ];

    paymentResponse($paymentResponseData);

} else if ($requestData['paymentOption'] == 'mollie-webhook') {
    $mollieResponse = new MollieResponse;
    $webhookData = $mollieResponse->retrieveMollieWebhookData($requestData);

    // mollie webhook data received here with following option
    // $webhookData['status']; - payment status (paid|open|pending|failed|expired|canceled|refund|chargeback)
    // $webhookData['raw_data']; - webhook all raw data
    // $webhookData['message']; - if payment failed then message
}

/*
 * This payment used for get Success / Failed data for any payment method.
 *
 * @param array $paymentResponseData - contains : status and rawData
 *
 */
function paymentResponse($paymentResponseData) {

    if ($paymentResponseData['status'] === true) {
        global $mysqli;
        if(isset($paymentResponseData['data']['order_id'])){
            $order_id = $paymentResponseData['data']['order_id'];
        } else {
            $order_id = '';
        }
        
        $checkOrder = getData('orders','order_id','WHERE order_id = "'.$order_id.'"');

        if($checkOrder != 'noData'){
            updateData('orders','order_status','"success"','WHERE order_id = "'.$order_id.'"');
            $orderType = getData('orders','order_type','WHERE order_id = "'.$order_id.'"');
            $orderUser = getData('orders','user_id','WHERE order_id = "'.$order_id.'"');
            $orderPackage = getData('orders','order_package','WHERE order_id = "'.$order_id.'"');
            $orderGateway = getData('orders','order_gateway','WHERE order_id = "'.$order_id.'"');
            $saledate = date('m/d/Y');
            if($orderType == 'credits'){
                $packageId = $orderPackage+1;
                $credits = getData('config_credits','credits','WHERE id = "'.$packageId.'"');
                $price = getData('config_credits','price','WHERE id = "'.$packageId.'"');
                $actionText = $credits.' Credits';   
                                
                $mysqli->query("UPDATE users SET credits = credits+".$credits." WHERE id =".$orderUser);
                $mysqli->query("INSERT INTO sales (u_id,amount,gateway,action,time,type,quantity,saledate) 
                VALUES ('".$orderUser."','".$price."','".$orderGateway."','".$actionText."','".time()."','credits','".$credits."','".$saledate."')");                 
            } else {
                $packageId = $orderPackage+1;
                $days = getData('config_premium','days','WHERE id = "'.$packageId.'"');
                $price = getData('config_premium','price','WHERE id = "'.$packageId.'"');
                $time = time(); 
                $extra = 86400 * $days;
                $premium = $time + $extra;    
                $actionText = $days.' days premium';         
                $mysqli->query("UPDATE users_premium SET premium = ".$premium." WHERE uid =".$orderUser);
                $mysqli->query("INSERT INTO sales (u_id,amount,gateway,action,time,type,quantity,saledate) 
                VALUES ('".$orderUser."','".$price."','".$orderGateway."','".$actionText."','".time()."','premium','".$days."','".$saledate."')");                
            }
        } else {
            $mysqli->query('INSERT INTO orders (order_status,order_date,raw_data) 
        VALUES ("success","'.time().'", "'.$paymentResponseData['rawData'].'")');
        }
        
        header('Location: '. getAppUrl('payment-success.php'));        
    } elseif ($paymentResponseData['status'] === 'pending') {
        header('Location: '. getAppUrl('payment-pending.php'));      
    } else {        
        header('Location: '. getAppUrl('payment-failed.php'));
    }
}

/*
* Prepare Payment Data.
*
* @param array $paymentData
*
*/
function preparePaymentData($orderId, $amount, $txnId='', $paymentGateway) {
    return [
        'order_id'              => $orderId,
        'amount'                => $amount,
        'payment_reference_id'  => $txnId,
        'payment_gatway'        => $paymentGateway
    ];
}