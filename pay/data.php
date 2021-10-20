<?php 
$baseUrl = '';
if(isset($sm)){
    global $sm;
    $baseUrl = $sm['config']['site_url'].'pay/';
    $merchantName = $sm['config']['name'];
    $merchantEmail = $sm['config']['email'];
    if (!function_exists('gatewayData')) {
        function gatewayData($p,$s) {
            global $mysqli;
            $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = '".$p."' and setting = '".$s."'");
            $result = $config->fetch_object();
            $data = $result->setting_val;
            if($s == 'enabled'){
                if($result->setting_val == 'Yes'){
                    $data = true;
                } else {
                    $data = false;
                }  
                return $data;
            } else {
                return $result->setting_val;
            }
        }
    }
    function globalSiteUrl() {
        global $sm;
            $url = $sm['config']['site_url'].'pay/';
        return $url;
    }     
} else {
    if (!function_exists('gatewayData')) {
        require('../assets/includes/config.php');
        $mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
        $mysqli->set_charset('utf8mb4');
        if (mysqli_connect_errno($mysqli)) {
            exit(mysqli_connect_error());
        }

        $check_bar = substr($site_url, -1);
        if($check_bar == '/'){
            $baseUrl = $site_url.'pay/';  
        } else {
            $baseUrl = $site_url.'/pay/';  
        }

        function gatewayData($p,$s) {
            global $mysqli;
            $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = '".$p."' and setting = '".$s."'");
            $result = $config->fetch_object();
            $data = $result->setting_val;
            if($s == 'enabled'){
                if($result->setting_val == 'Yes'){
                    $data = true;
                } else {
                    $data = false;
                }  
                return $data;
            } else {
                return $result->setting_val;
            }
        }

        function globalSiteUrl() {
            global $site_url;
                $url = $site_url;
                $check_bar = substr($url, -1);
                if($check_bar == '/'){
                    $url = $url.'pay/';  
                } else {
                    $url = $url.'/pay/';  
                }            
            return $url;
        }        

        $merchantName = gatewayData('settings','siteName');
        $merchantEmail = gatewayData('settings','siteEmail');
    }
}
global $baseUrl,$merchantEmail,$merchantName;
if($baseUrl == ''){
    $baseUrl = globalSiteUrl();
}
$techAppConfig = [
    'base_url' =>  $baseUrl,
    'amount' => null,
    'payments' => [
        'gateway_configuration' => [
            'paypal' => [
                'enable'                        => gatewayData('paypal','enabled'),      
                'testMode'                      => false,
                'gateway'                       => 'Paypal',
                'paypalSandboxBusinessEmail'        => '',
                'paypalProductionBusinessEmail'     => '',
                'currency'                  => gatewayData('settings','currency'),
                'currencySymbol'              => gatewayData('settings','currencySymbol'),
                'paypalSandboxUrl'          => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
                'paypalProdUrl'             => 'https://www.paypal.com/cgi-bin/webscr',
                'notifyIpnURl'              => 'payment-response.php',
                'cancelReturn'              => 'payment-response.php',
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => []
            ],           
            'paytm' => [
                'enable'                    => gatewayData('paytm','enabled'),
                'testMode'                  => false,
                'gateway'                   => 'Paytm',
                'currency'                  => 'INR', 
                'currencySymbol'              => '₹',
                'paytmMerchantTestingMidKey'       => '',
                'paytmMerchantTestingSecretKey'    => '',
                'paytmMerchantLiveMidKey'       => gatewayData('paytm','key'), 
                'paytmMerchantLiveSecretKey'    => gatewayData('paytm','secret'),
                'industryTypeID'            => 'Retail',
                'channelID'                 => 'WEB',
                'website'                   => 'WEBSTAGING', 
                'paytmTxnUrl'               => 'https://securegw-stage.paytm.in/theia/processTransaction',
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => [
                                                'paytmMerchantTestingSecretKey',
                                                'paytmMerchantLiveSecretKey'
                                            ]
            ],
            'instamojo' => [
                'enable'                    => gatewayData('instamojo','enabled'),
                'testMode'                  => false,
                'gateway'                   => 'Instamojo',
                'currency'                  => 'INR',
                'currencySymbol'              => '₹',
                'sendEmail'                 => false, 
                'instamojoTestingApiKey'           => '',
                'instamojoTestingAuthTokenKey'     => '',
                'instamojoLiveApiKey'           => gatewayData('instamojo','key'),
                'instamojoLiveAuthTokenKey'     => gatewayData('instamojo','auth'),
                'instamojoSandboxRedirectUrl'   => 'https://test.instamojo.com/api/1.1/',
                'instamojoProdRedirectUrl'      => 'https://www.instamojo.com/api/1.1/',
                'webhook'                   => 'http://instamojo.com/webhook/',
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => [
                                                'instamojoTestingApiKey',
                                                'instamojoTestingAuthTokenKey',
                                                'instamojoLiveApiKey',
                                                'instamojoLiveAuthTokenKey',
                                                'instamojoSandboxRedirectUrl',
                                                'instamojoProdRedirectUrl'
                                            ]
            ],
            'paystack' => [
                'enable'                    => gatewayData('paystack','enabled'),
                'testMode'                  => true,
                'gateway'                   => 'Paystack',
                'currency'                  => 'NGN',
                'currencySymbol'              => '₦',
                'paystackTestingSecretKey'         => '',
                'paystackTestingPublicKey'         => '',
                'paystackLiveSecretKey'         => gatewayData('paystack','secret'),
                'paystackLivePublicKey'         => gatewayData('paystack','publish'),
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => [
                                                'paystackTestingSecretKey',
                                                'paystackLiveSecretKey'
                                            ]
            ],
            'stripe'    => [
                'enable'                    => gatewayData('stripe','enabled'),
                'testMode'                  => false,
                'gateway'                   => 'Stripe',
                'locale'                    => 'auto',
                'allowRememberMe'           => true,
                'currency'                  => gatewayData('settings','currency'),
                'currencySymbol'              => gatewayData('settings','currencySymbol'),
                'stripeTestingSecretKey'    => gatewayData('stripe','secret'), 
                'stripeTestingPublishKey'   => gatewayData('stripe','publish'),
                'stripeLiveSecretKey'       => gatewayData('stripe','secret'),
                'stripeLivePublishKey'      => gatewayData('stripe','publish'),
                'callbackUrl'               => 'payment-response.php',
                'paymentMethodTypes'        => [
                    // before activating additional payment methods
                    // make sure that these methods are enabled in your stripe account
                    // https://dashboard.stripe.com/settings/payments
                    'card',
                    // 'ideal',
                    // 'bancontact',
                    // 'giropay',
                    // 'p24',
                    // 'eps'
                ],                
                'privateItems'              => [
                                                'stripeTestingSecretKey',
                                                'stripeLiveSecretKey'
                                            ]
            ],
            'razorpay'    => [
                'enable'                    => gatewayData('razorpay','enabled'),
                'testMode'                  => false,
                'gateway'                   => 'Razorpay', 
                'merchantname'              => $merchantName, 
                'themeColor'                => '#4CAF50',
                'currency'                  => 'INR',
                'currencySymbol'              => '₹',
                'razorpayTestingkeyId'      => '',
                'razorpayTestingSecretkey'  => '',
                'razorpayLivekeyId'         => gatewayData('razorpay','api'),
                'razorpayLiveSecretkey'     => gatewayData('razorpay','secret'),
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => [
                                                'razorpayTestingSecretkey',
                                                'razorpayLiveSecretkey'
                                            ]
            ],
            'iyzico'    => [
                'enable'                    => gatewayData('iyzico','enabled'),
                'testMode'                  => false,
                'gateway'                   => 'Iyzico',
                'conversation_id'           => 'CONVERS' . uniqid(),
                'currency'                  => 'TRY',
                'currencySymbol'              => '₺',
                'subjectType'               => 1, // credit
                'txnType'                   => 2, // renewal
                'subscriptionPlanType'      => 1, //txn status
                'iyzicoTestingApiKey'       => '',
                'iyzicoTestingSecretkey'    => '',
                'iyzicoLiveApiKey'          => gatewayData('iyzico','api'),
                'iyzicoLiveSecretkey'       => gatewayData('iyzico','secret'),
                'iyzicoSandboxModeUrl'      => 'https://sandbox-api.iyzipay.com',
                'iyzicoProductionModeUrl'   => 'https://api.iyzipay.com',
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => [
                                                'iyzicoTestingApiKey',
                                                'iyzicoTestingSecretkey',
                                                'iyzicoLiveApiKey',
                                                'iyzicoLiveSecretkey'
                                            ]
            ],
            'authorize-net'    => [
                'enable'                         => gatewayData('authorize','enabled'),
                'testMode'                       => false,
                'gateway'                        => 'Authorize.net',
                'reference_id'                   => 'REF' . uniqid(),
                'currency'                       => gatewayData('settings','currency'),
                'currencySymbol'                 => gatewayData('settings','currencySymbol'),
                'type'                           => 'individual',
                'txnType'                        => 'authCaptureTransaction',
                'authorizeNetTestApiLoginId'     => '',
                'authorizeNetTestTransactionKey' => '',
                'authorizeNetLiveApiLoginId'     => gatewayData('authorize','api'),
                'authorizeNetLiveTransactionKey' => gatewayData('authorize','secret'),
                'callbackUrl'                    => 'payment-response.php',
                'privateItems'                  => [
                                                    'authorizeNetTestApiLoginId',
                                                    'authorizeNetTestTransactionKey',
                                                    'authorizeNetLiveApiLoginId',
                                                    'authorizeNetLiveTransactionKey'
                                                ]
            ],
            'bitpay'    => [
                'enable'                        => gatewayData('bitpay','enabled'),
                'testMode'                      => false,
                'notificationEmail'             => $merchantEmail,
                'gateway'                       => 'BitPay',
                'currency'                      => gatewayData('settings','currency'),
                'currencySymbol'                => gatewayData('settings','currencySymbol'), 
                'password'                      => gatewayData('bitpay','password'),
                'pairingCode'                   => gatewayData('bitpay','pairing_code'),
                'pairinglabel'                  => gatewayData('bitpay','pairing_label'),
                'callbackUrl'                   => 'payment-response.php', 
                'privateItems'                  => ['pairingCode', 'pairinglabel', 'password']
            ],
            'mercadopago' => [
                'enable'                        => gatewayData('mercadopago','enabled'),
                'testMode'                      => false,
                'gateway'                       => 'Mercado Pago',
                'currency'                      => gatewayData('settings','currency'),
                'currencySymbol'                => gatewayData('settings','currencySymbol'),
                'testAccessToken'               => '',
                'liveAccessToken'               => gatewayData('mercadopago','token'),
                'callbackUrl'                   => 'payment-response.php',
                'privateItems'                  => ['testAccessToken', 'liveAccessToken']
            ],
            'payumoney' => [
                'enable'                        => gatewayData('payu','enabled'),
                'testMode'                      => false,
                'gateway'                       => 'PayUmoney', 
                'currency'                      => 'INR',
                'currencySymbol'                => '₹',
                'txnId'                         => "Txn" . rand(10000,99999999),
                'merchantTestKey'               => '',
                'merchantTestSalt'              => '',
                'merchantLiveKey'               => gatewayData('payu','merchant'),
                'merchantLiveSalt'              => gatewayData('payu','salt'),
                'callbackUrl'                   => 'payment-response.php',
                'checkoutColor'                 => 'e34524',
                'checkoutLogo'                  => 'http://boltiswatching.com/wp-content/uploads/2015/09/Bolt-Logo-e14421724859591.png',
                'privateItems'                  => ['merchantTestKey', 'merchantTestSalt', 'merchantLiveKey', 'merchantLiveSalt']
            ],
            'mollie' => [
                'enable'                        => gatewayData('mollie','enabled'),
                'testMode'                      => false,
                'gateway'                       => 'Mollie',
                'currency'                      => gatewayData('settings','currency'),
                'currencySymbol'                => gatewayData('settings','currencySymbol'),
                'testApiKey'                    => '',
                'liveApiKey'                    => gatewayData('mollie','enabled'),
                'callbackUrl'                   => 'payment-response.php',
                'privateItems'                  => ['testApiKey', 'liveApiKey']
            ],
            'fortumo' => [
                'enable'                        => gatewayData('fortumo','enabled'),      
                'testMode'                      => false,
                'gateway'                       => 'SMS',
                'currency'                  => gatewayData('settings','currency'),
                'currencySymbol'              => gatewayData('settings','currencySymbol'),
                'notifyIpnURl'              => 'payment-response.php',
                'cancelReturn'              => 'payment-response.php',
                'callbackUrl'               => 'payment-response.php',
                'privateItems'              => []
            ],             
        ],
    ],

];

return compact("techAppConfig");