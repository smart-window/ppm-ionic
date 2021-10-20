<?php
if (isset($_GET['code']) || strpos($_SERVER["HTTP_REFERER"],'facebook') !== false) {
	require_once("../includes/core.php");
	require_once("../social/Facebook/autoload.php");
	} else {
	require_once("assets/includes/core.php");
	require_once("assets/social/Facebook/autoload.php");
}
$fb = new Facebook\Facebook([
  'app_id' => siteConfig('fb_app_id'),
  'app_secret' => siteConfig('fb_app_secret'),
  'default_graph_version' => 'v2.3',
  ]);
$helper = $fb->getRedirectLoginHelper();
try {
	$accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	echo $e->getMessage();
	exit;
}
if (isset($accessToken)) {
	$request = $fb->get('/me?fields=id,name,email,gender', $accessToken);
	$graphNode = $request->getGraphNode(); 	
	$location = json_decode(file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']));		
  	$fbid = $graphNode['id'];         
	$name = $graphNode['name']; 
	$email = $graphNode['email'];  
	$gender = $graphNode['gender']; 	
	fbconnect($fbid,$name,$email,$gender,$location);
	header("Location: ".$sm['config']['site_url']);  
} else {
	$permissions = ['email']; // optional
	$callback = $sm['config']['site_url'].'assets/sources/fbconnect.php';
	$loginUrl = $helper->getLoginUrl($callback,$permissions);
	header("Location: ".$loginUrl);
}