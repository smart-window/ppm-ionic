<?php
/* Belloo By Xohan - xohansosa@gmail.com */
require_once('assets/includes/core.php');
switch ($_GET['page']) {
	case 'fb':
		include('assets/sources/fbconnect.php');
		exit;
	break;
	case 'twitter':
		include('assets/sources/twitterconnect.php');
		exit;
	break;	
	case 'instagram':
		include('assets/sources/instaconnect.php');
		exit;
	break;	
	case 'google':
		include('assets/sources/googleconnect.php');
		exit;
	break;		
}	
$mysqli->close();