<?php
/*
    * Get Config data 
    * @return array.
    *-------------------------------------------------------- */

if (!function_exists('getConfig')) {
    function getConfig($item = null)
    {
        try {
            if (!@include_once(PAY_PAGE_CONFIG)) {
                throw new Exception('file does not exist');
            } else {
                return require PAY_PAGE_CONFIG;
            }
        } catch (\Exception $e) {
            throw new \Exception("PAY_PAGE_CONFIG - Missing config path constant", 1);
        }
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('configItem')) {
    function configItem()
    {
        $getConfig  = getConfig();
        $getItem    = $getConfig['techAppConfig'];
        return $getItem;
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('configItemData')) {
    function configItemData($key, $default = null)
    {
        $getConfig  = getConfig();
        $data    = $getConfig['techAppConfig'];
        return getArrayItem($data, $key, $default);
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getPublicConfigItem')) {
    function getPublicConfigItem()
    {
        $getConfig  = getConfig();
        $getItem    = $getConfig['techAppConfig']['payments']['gateway_configuration'];

        foreach ($getItem as $itemKey => $item) {
            if (!empty($item['privateItems'])) {
                foreach ($item['privateItems'] as $privateItem) {
                    if (isset($getItem[$itemKey][$privateItem])) {
                        unset($getItem[$itemKey][$privateItem]);
                        unset($getItem[$itemKey]['privateItems']);
                    }
                }
            }
        }
        $configItem['payments']['gateway_configuration'] = $getItem;
        return $configItem;
    }
}

/*
      * Get the paytm merchant
      *
      * @param string   $paymentData
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getPaytmMerchantForm')) {
    function getPaytmMerchantForm($paymentData)
    {
        ob_start();
        include "paytm-merchant-form.php";
        $html_content = ob_get_contents();
        ob_end_clean();
        return $html_content;
    }
}

/*
      * Get App Url
      *
      * @param string   $paymentData
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getAppUrl')) {
    function getAppUrl($item = null, $path = '')
    {
        $configData = getConfig();
        $basePath = $configData['techAppConfig']['base_url'];

        if (!empty($item)) {
            return $basePath . $path . $item;
        } else {
            return $basePath . $path;
        }
    }
}


/**
 * Redirect using post
 *
 * @param  string // https://www.codexworld.com/how-to/get-user-ip-address-php/
 * @param  array postData data to post
 *-------------------------------------------------------- */
if (!function_exists('getUserIpAddr')) {
    function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getArrayItem')) {
    function getArrayItem($array, $key, $default = null)
    {
        // @assert $key is a non-empty string
        // @assert $array is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($array)) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                // @assert $array[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $array)) {
                    return $default;
                }

                $array = $array[$innerKey];
            }

            return $array;
        }

        // @fallback returning value of $key in $array or $default value
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }
}

/**
 * Debugging function for debugging javascript side.
 *
 * @param  N numbers of params can be sent
 *-------------------------------------------------------- */
if (!function_exists('__dd')) {
    function __dd()
    {
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__dd() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        if (isset($backtrace[0])) {
            $args['debug_backtrace'] = str_replace(__DIR__, '', $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
        }
        call_user_func_array('__pr', $args);
        exit();
    }
}

/*
    * Debugging function for debugging javascript as well as PHP side, work as likely print_r but accepts unlimited parameters
    *
    * @param  N numbers of params can be sent
    * @return void
    *-------------------------------------------------------- */

if (!function_exists('__pr')) {
    function __pr()
    {
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__pr() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        // print_r($backtrace);
        // exit();

        echo "";
        // Editors Supported: "phpstorm", "vscode", "vscode-insiders","sublime", "atom"
        $editor = 'vscode';
        echo '</pre><br/><a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="' . $editor . '://file' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '">' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '</a><br/><br/>';

        echo '<pre>';

        return array_map(function ($argument) {
            print_r($argument, false);
            echo "<br/><br/>";
        }, $args);
    }
}
