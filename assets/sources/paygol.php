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

$secret_key = pluginsData('paygol','secret');  

if(isset($_GET['key'])){
	if ($secret_key != $_GET['key']) {
	    echo "Validation key error"; 
	    exit;
	}

	$message_id	= $_GET['message_id'];
	$service_id	= $_GET['service_id'];
	$shortcode	= $_GET['shortcode'];
	$keyword	= $_GET['keyword'];
	$message	= $_GET['message'];
	$sender	= $_GET['sender'];
	$operator	= $_GET['operator'];
	$country	= $_GET['country'];
	$custom	= $_GET['custom'];
	$points	= $_GET['points'];
	$price	= $_GET['price'];
	$currency	= $_GET['currency'];
	$data = explode(",", $custom);
	$uid = $data[0]; // User id
	$credits = $data[1]; // Credits
	$mysqli->query("UPDATE users SET credits = credits+'".$credits."' WHERE id = '".$uid."'");

	$actionText = $credits.' credits';

	$saledate = date('m/d/Y');
	$mysqli->query("INSERT INTO sales (u_id,amount,gateway,action,time,type,quantity,saledate) 
		VALUES ('".$uid."','".$price."','paygol','".$actionText."','".time()."','credits','".$credits."','".$saledate."')");		
} else {
    echo "Validation error"; 
    exit;	
}
	