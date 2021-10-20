<?php
require('../includes/config.php');
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}

function pluginsData($p,$val) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = '".$p."' and setting = '".$val."'");
    $result = $config->fetch_object();
    return $result->setting_val;
}

$secret = pluginsData('fortumo','secret');

if(isset($_GET['amount'])){
	$amount = $_GET['amount'];
	$cuid = $_GET['cuid'];
	$price = $_GET['price'];
	$status = $_GET['status'];
	if($status == 'completed'){
		$mysqli->query("UPDATE users SET credits = credits+'".$amount."' WHERE id = '".$cuid."'");

		$credits = $amount;
		$uid = $cuid;
		$actionText = $credits.' credits';
		$saledate = date('m/d/Y');
		$mysqli->query("INSERT INTO sales (u_id,amount,gateway,action,time,type,quantity,saledate) 
			VALUES ('".$uid."','".$price."','fortumo','".$actionText."','".time()."','credits','".$credits."','".$saledate."')");		
	}
} else {
    echo "Validation error"; 
    exit;	
}

function check_signature($params_array, $secret) {
	ksort($params_array);
	$str = '';
	foreach ($params_array as $k=>$v) {
	  if($k != 'sig') {
		$str .= "$k=$v";
	  }
	}
	$str .= $secret;
	$signature = md5($str);
	return ($params_array['sig'] == $signature);
}