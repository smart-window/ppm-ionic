<?php
/* Belloo By Xohan - xohansosa@gmail.com - https://www.premiumdatingscript.com/*/
if(!file_exists("assets/includes/config.php")){
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	$cb = substr($actual_link, -1);

	if($cb == '/'){
		header('Location: '.$actual_link.'install/');	
	} else {
		header('Location: '.$actual_link.'/install/');	
	}	
	exit;
}
require_once('assets/includes/core.php');

if (!isset($_GET['page']) && !isset($_GET['social'])) {
    $_GET['page'] = 'index';
}


if($sm['plugins']['autoRegister']['enabled'] == 'Yes' && $logged == false){
	$ip = getUserIpAddr();
	$checkIp = getData('users','id','WHERE ip = "'.$ip.'"');
	if($checkIp != 'noData'){
		$_SESSION['user'] = $checkIp;
		getUserInfo($checkIp,0);
		checkUserPremium($checkIp);
		$sm['user_notifications'] = userNotifications($checkIp);
		
		$sm['lang'] = siteLang($sm['user']['lang']);
		$sm['alang'] = appLang($sm['user']['lang']);
		$sm['elang'] = emailLang($sm['user']['lang']);
		$sm['seoLang'] = seoLang($sm['user']['lang']);
		$sm['landingLang'] = landingLang($sm['user']['lang'],$landingTheme,$_SESSION['landingPreset']);
		$sm['genders'] = siteGenders($sm['user']['lang']);		

	    $modPermission = array();
	    if($sm['user']['admin'] >= 1){
		    $moderationList = getArray('moderation_list','','moderation ASC');
		    foreach ($moderationList as $mod) {  
		        if($sm['user']['admin'] == 1){
		            $modPermission[$mod['moderation']] = 'Yes';
		        } else {
		            $modVal = getData('moderators_permission','setting_val','WHERE setting = "'.$mod['moderation'].'" AND id = "'.$sm['user']['moderator'].'"');
		            $modPermission[$mod['moderation']] = $modVal;
		        }
		    }      
	    }
	    $sm['moderator'] = $modPermission;

		$time = time();
		$logged = true;	
		$ip = getUserIpAddr();
		if($sm['user']['ip'] != $ip){
			$mysqli->query("UPDATE users set ip = '".$ip."' where id = '".$checkIp."'");
		}
		if($sm['user']['last_access'] < $time || $sm['user']['last_access'] == 0){	
			$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$checkIp."'");	
		}
	} else {

		
		$rand = rand(0,1012451);
		$rand2 = rand(0,1012451);
		$rand3 = rand(0,1012451);
		$salt = base64_encode($rand);
		$pswd = crypt($sm['plugins']['autoRegister']['guestDefaultPswd'],$salt);
		$lang = getData('languages','id','WHERE id = '.$_SESSION['lang']);
		if($lang == 'noData'){
			$lang = $sm['plugins']['settings']['defaultLang'];
		}

		$name = $sm['plugins']['autoRegister']['guestDefaultName'].' '.$rand;
		$username = $sm['plugins']['autoRegister']['guestDefaultName'].$rand.$rand2;
		$username = str_replace(' ', '', $username);

		$siteEmail = explode('@',$sm['plugins']['settings']['siteEmail']);
		$email = $username.'@'.$siteEmail[1];
		$age = 29;
		$birthday = date('F', mktime(0, 0, 0, 06, 10)).' 15, 1990';

		if($_GET['page'] == $sm['plugins']['autoRegister']['guestCustomOneUrl']){
			$gender = $sm['plugins']['autoRegister']['guestCustomOneGender'];
			$looking = $sm['plugins']['autoRegister']['guestCustomOneLooking'];
		} else if($_GET['page'] == $sm['plugins']['autoRegister']['guestCustomTwoUrl']){
			$gender = $sm['plugins']['autoRegister']['guestCustomTwoGender'];
			$looking = $sm['plugins']['autoRegister']['guestCustomTwoLooking'];			
		} else {
			$gender = $sm['plugins']['autoRegister']['guestDefaultGender'];
			$looking = $sm['plugins']['autoRegister']['guestDefaultLooking'];
		}

		$ip = getUserIpAddr();

		if(!empty($sm['plugins']['ipstack']['key'])){
		    $location = json_decode(file_get_contents('http://api.ipstack.com/'.$ip.'?access_key='.$sm['plugins']['ipstack']['key']));
			$city = $location->city; 	
			$country = $location->country_name; 	
			$lat = $location->latitude; 	
			$lng = $location->longitude;
		} else {
			$city = 'Los Angeles';
			$country = 'United States';
			$lat = '-56.1250444';
			$lng = '-34.8872424';
		}

		
		$date = date('m/d/Y', time());
		
		$dID = 0;
		$bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$city." ".$country;

		$query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,online_day,password,ip,last_access,username,join_date_time,app_id) VALUES ('".$name."', '".$email."','".$pswd."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$lang."','".$date."','".$bio."','".$looking."','18,29,1',0,0,'".$sm['plugins']['autoRegister']['guestDefaultPswd']."','".$ip."','".time()."','".$username."','".time()."','".$dID."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	

			//free premium
			$free_premium = 0;
			$allG = count(siteGenders($lang));
			$allG = $allG + 1;					
			if($sm['plugins']['rewards']['freePremiumGender'] == $gender || $sm['plugins']['rewards']['freePremiumGender'] == $allG){
				$free_premium = $sm['plugins']['rewards']['freePremium'];
			}
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");

			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid,field1) VALUES ('".$last_id."','".$sm['lang'][224]['text']."')");


			$_SESSION['user'] = $last_id;
			getUserInfo($last_id);
			checkUserPremium($_SESSION['user']);
			$sm['user_notifications'] = userNotifications($_SESSION['user']);
			
			$sm['lang'] = siteLang($sm['user']['lang']);
			$sm['alang'] = appLang($sm['user']['lang']);
			$sm['elang'] = emailLang($sm['user']['lang']);
			$sm['seoLang'] = seoLang($sm['user']['lang']);
			$sm['landingLang'] = landingLang($sm['user']['lang'],$landingTheme,$_SESSION['landingPreset']);
			$sm['genders'] = siteGenders($sm['user']['lang']);		

		    $modPermission = array();
		    if($sm['user']['admin'] >= 1){
			    $moderationList = getArray('moderation_list','','moderation ASC');
			    foreach ($moderationList as $mod) {  
			        if($sm['user']['admin'] == 1){
			            $modPermission[$mod['moderation']] = 'Yes';
			        } else {
			            $modVal = getData('moderators_permission','setting_val','WHERE setting = "'.$mod['moderation'].'" AND id = "'.$sm['user']['moderator'].'"');
			            $modPermission[$mod['moderation']] = $modVal;
			        }
			    }      
		    }
		    $sm['moderator'] = $modPermission;

			$time = time();
			$logged = true;		
			

			if($sm['user']['ip'] != $ip){
				$mysqli->query("UPDATE users set ip = '".$ip."' where id = '".$_SESSION['user']."'");
			}
			if($sm['user']['last_access'] < $time || $sm['user']['last_access'] == 0){	
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$_SESSION['user']."'");	
			}
		}			
	}	
}


if($logged == true){
	if(strpos($_SESSION['preset'], 'landing') !== false && !isset($_GET['landing'])){
		$_SESSION['preset'] = $sm['settings']['desktopThemePreset'];
		header('Location:'.$sm['config']['site_url']);
	}
}

if(isset($_GET['page'])){
	switch ($_GET['page']) {
		case 'index':
			if(isset($_GET['landing'])){
				showLandingPage($_GET['landing']);
				exit;				
			} else {
				if ($logged !== true ) {		
					showLandingPage();
					exit;
				} else {
					$_GET['page'] = 'meet';
					$folder = 'meet';
					$page = 'content';
					include('assets/sources/pages.php');		
				}
			}
		break;
		case 'fb':
			if($_SESSION['new_user'] == 1){
				showLandingPage();
				exit;
			} else {
				showLandingPage();
				exit;				
			}
		break;	
		case 'profile':
			if ($logged === false || isset($_GET['view'])) {
				$pid = secureEncode($_GET['id']);
				getUserInfo($pid,1);
				$container = getPage('profileLanding');	
				if($sm['plugins']['htmlsecurity']['enabled'] == 'Yes'){
					$container = preg_replace('/\r|\n/','',$container);
				}
				echo $container;
				exit;
			} else {	
				$pid = secureEncode($_GET['id']);
				$checkUsername = checkIfExist('users','username',$pid);
				$checkId = checkIfExist('users','id',$pid);
				if($checkUsername == 0 && $checkId == 0){
					header('Location:'.$sm['config']['site_url']);
					exit;					
				}				
				$new = getUserTotalConv($sm['user']['id'],$_GET['id']);
				$folder = 'profile';
				$page = 'content';
				include('assets/sources/pages.php');
			}
		break;	
		case 'verification':
			if ($logged === false ) {
				showLandingPage();
				exit;
			}else{	
				$ussid = secureEncode($_GET['uid']);
				$mysqli->query('UPDATE users set verified = 1 where id = "'.$ussid.'"');
				$pass = secureEncode($_GET['b']);
				welcomeMailNotification($sm['user']['name'],$sm['user']['email'],$pass);
				header('Location:'.$sm['config']['site_url']);
				exit;			
			}
		break;	
		case 'meet':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}	
			$folder = 'meet';
			$page = 'content';
			include('assets/sources/pages.php');
		break;
		case 'live':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}	
			$folder = 'live';
			$page = 'live';
			include('assets/sources/pages.php');
		break;	
		case 'popular':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'popular';
			$page = 'content';
			include('assets/sources/pages.php');
		break;	
		case 'recover':
			if($_GET['id'] != '' && $_GET['code'] != ''){
			$check = checkRecoverCode($_GET['id'],$_GET['code']);
				if($check > 0){
					$_SESSION['user'] = $_GET['id'];
					header('Location:'.$sm['config']['site_url']);
				} else {
					showLandingPage();
					exit;						
				}
			} else {
				showLandingPage();
				exit;			
			}
		break;		
		case 'fans':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'fans';
			$page = 'content';
			include('assets/sources/pages.php');
		break;
		case 'blocked':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'blocked';
			include('assets/sources/pages.php');
		break;			
		case 'groups':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'groups';
			$page = 'content';
			include('assets/sources/pages.php');
		break;			
		case 'visits':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'visits';
			$page = 'content';
			include('assets/sources/pages.php');
		break;		
		case 'credits-ok':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'credits';
			include('assets/sources/pages.php');
		break;	
		case 'credits':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'credits';
			include('assets/sources/pages.php');
		break;
		case 'getcredits':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'buyCredits';
			include('assets/sources/pages.php');
		break;		
		case 'withdraw':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'withdraw';
			include('assets/sources/pages.php');
		break;		
		case 'popularity':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'popularity';
			include('assets/sources/pages.php');
		break;	
		case 'premium':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'profile';
			$page = 'premium';
			include('assets/sources/pages.php');
		break;			
		case 'matches':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'matches';
			$page = 'content';
			include('assets/sources/pages.php');
		break;	
		case 'mylikes':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'matches';
			$page = 'mylikes';
			include('assets/sources/pages.php');
		break;	

		case 'terms':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'terms';
			$page = 'terms';
			include('assets/sources/pages.php');
		break;
		case 'tac':
			echo getLandingPage('index/tac');
			exit;	
		break;
		case 'pp':
			echo getLandingPage('index/pp');
			exit;	
		break;	
		case 'privacy':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}		
			$folder = 'terms';
			$page = 'privacy';
			include('assets/sources/pages.php');
		break;
		case 'cookies':
			echo getLandingPage('index/cookies');
			exit;	
		break;			
		case 'discover':
			if ($logged !== true ) {
				showLandingPage();
				exit;
			}	
			$folder = 'discover';
			$page = 'content';
			include('assets/sources/pages.php');
		break;
		case 'connect':	
			header('Location:'.$sm['config']['site_url']);
		break;			
		case 'chat':
			if ($logged !== true ) {
				header('Location:'.$sm['config']['site_url']);		
				exit;
			}
			
			$folder = 'chat';
			$lc = getUserLC($sm['user']['id']);
			if(!isset($_GET['id'])){		
				if($lc == 0){
					$sm['profile'] = '';
				} else {
					$sm['profile'] = 'yes';
					$count = getUserTodayConv($sm['user']['id']);
					$new = getUserTotalConv($sm['user']['id'],$lc);					
					if($new == 0 && $count >= $sm['basic']['chat'] && $sm['user']['premium'] == 0 || $new == 0 && $count >= $sm['premium']['chat']){
						$page = 'premium';
					} else {
						$page = 'content';
					}						
				}
			} else {
				$lc = secureEncode($_GET['id']);
				$sm['profile'] = 'yes';
				$count = getUserTodayConv($sm['user']['id']);
				$new = getUserTotalConv($sm['user']['id'],$_GET['id']);
				if($new == 0 && $count >= $sm['basic']['chat'] && $sm['user']['premium'] == 0 || $new == 0 && $count >= $sm['premium']['chat']){
					$page = 'premium';
				} else {
					$page = 'content';
				}	
			}	
			if($sm['profile'] == ''){
				$page = 'empty';
			} 		
			include('assets/sources/pages.php');
		break;
		case 'admin':
			if ($logged !== true || $sm['user']['admin'] == 0) {
				echo getAdministratorPage('login');
				exit;
			}
			$p = '';
			if(isset($_GET['p'])){
				$p = secureEncode($_GET['p']);
			}
			if($p == ''){
				$sm['content'] = getAdministratorPage('main_dashboard');
			} else {
				$sm['content'] = getAdministratorPage($p);	
			}
			echo getAdministratorPage('index');
			exit;
		break;

		case 'cp':
			if ($logged !== true || $sm['user']['admin'] == 0) {
				echo getAdministratorPage('login');
				exit;
			}
			$p = '';
			if(isset($_GET['p'])){
				$p = secureEncode($_GET['p']);
			}
			if($p == ''){
				$sm['content'] = getAdministratorPage('main_dashboard');
			} else {
				$sm['content'] = getAdministratorPage($p);	
			}
			echo getAdministratorPage('index');
			exit;
		break;

		case 'logout':
			include('assets/sources/logout.php');
		break;
		case 'debug':
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			$folder = $_GET['debug_folder'];
			$page = $_GET['debug_page'];
			include('assets/sources/pages.php');			
		break;
		case 'checkPHPinfo':
			phpinfo();
			exit;		
		break;	

		case 'profileLanding':
			$container = getPage('profileLanding');	
			if($sm['plugins']['htmlsecurity']['enabled'] == 'Yes'){
				$container = preg_replace('/\r|\n/','',$container);
			}
			echo $container;
			exit;
		break;	
		default:
			if ($logged !== true ) {		
				header('Location:'.$sm['config']['site_url']);
				exit;
			} else {
				$folder = 'meet';
				$page = 'content';
				include('assets/sources/pages.php');		
			}
		break;	
	}

}

if(isset($_GET['social'])){
	switch ($_GET['social']) {
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
}

function showLandingPage(){
	global $sm;
	$landing = getLandingPage('index/content');	
	if($sm['plugins']['htmlsecurity']['enabled'] == 'Yes'){
		$landing = preg_replace('/\r|\n/','',$landing);
	}
	echo $landing;	
}

$container = getPage('container');	
if($sm['plugins']['htmlsecurity']['enabled'] == 'Yes'){
	$container = preg_replace('/\r|\n/','',$container);
}


echo $container;




$mysqli->close();