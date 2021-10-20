<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}
header('Content-Type: application/json');
require_once('../assets/includes/core.php');
if(isset($sm['user']['id'])){
	$uid = $sm['user']['id'];
} else {
	$uid = 0;
}
$ad = 5;
$adMobA = '';
$adMobI = '';
$userApi = 'https://www.belloo.date/clients/users.php?';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	switch ($_GET['action']) {
		case 'login':
			$email = secureEncode($_GET['login_email']);	
			$password = secureEncode($_GET['login_pass']);	
			if(isset($_GET['dID'])){
				$dID = secureEncode($_GET['dID']);	
			} else {
				$dID = 0;
			}				
			$arr = array();
			$arr['error'] = 0;
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][181]['text'];	
				echo json_encode($arr);
				exit;	
			}		
			if($email == "" || $email == NULL || $password == "" || $password == NULL ){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][182]['text'];
				echo json_encode($arr);
				exit;	
			}			
			$email_check = $mysqli->query("SELECT email,id,pass,verified,name,imported FROM users WHERE email = '".$email."'");	
			if($email_check->num_rows == 0 ){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][183]['text'];
				echo json_encode($arr);
				exit;	
			} else {
				$pass = $email_check->fetch_object();

				if($pass->imported == 'quickdate'){
	                $checkPassword = password_verify($password,$pass->pass);
					if($checkPassword) {
						$_SESSION['user'] = $pass->id;
						setcookie("user", $pass->id, 2147483647);
						getUserInfo($pass->id,0);
						$mysqli->query("UPDATE users SET app_id = '".$dID."' WHERE email = '".$email."'");
						$arr['user'] = $sm['user'];	
						$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
						$age = $sm['user']['s_age'];
						$e_age = explode( ',', $age );		
						$arr['user']['sage'] = $e_age[1];	
						$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
						$arr['user']['notification'] = userNotifications($pass->id);
						$time = time();
						echo json_encode($arr);			
						exit;		
					} else {
						$arr['error'] = 1;
						$arr['error_m'] = $sm['lang'][184]['text'];
						echo json_encode($arr);
						exit;		
					}                
				} else {
					if(crypt($password, $pass->pass) == $pass->pass) {
						$_SESSION['user'] = $pass->id;
						setcookie("user", $pass->id, 2147483647);
						getUserInfo($pass->id,0);
						$mysqli->query("UPDATE users SET app_id = '".$dID."' WHERE email = '".$email."'");
						$arr['user'] = $sm['user'];	
						$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
						$age = $sm['user']['s_age'];
						$e_age = explode( ',', $age );		
						$arr['user']['sage'] = $e_age[1];	
						$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
						$arr['user']['notification'] = userNotifications($pass->id);
						$time = time();
						echo json_encode($arr);			
						exit;	
					} else {
						$arr['error'] = 1;
						$arr['error_m'] = $sm['lang'][184]['text'];
						echo json_encode($arr);
						exit;		
					}	
				}		
			}
		break;	
		case 'register':
			$email = secureEncode($_GET['reg_email']);	
			$password = secureEncode($_GET['reg_pass']);
			if($password == 'fb'){
				$password = $email;
			}
			$name = secureEncode($_GET['reg_name']);
			$gender = secureEncode($_GET['reg_gender']);
			$birthday = secureEncode($_GET['reg_birthday']);
			$looking = secureEncode($_GET['reg_looking']);		
			$photo = secureEncode($_GET['reg_photo']);
			$thumb = secureEncode($_GET['reg_thumb']);
			if(isset($_GET['dID'])){
				$dID = secureEncode($_GET['dID']);	
			} else {
				$dID = 0;
			}


			$city = secureEncode($_GET['reg_city']);
			$country = secureEncode($_GET['reg_country']);
			$lat = secureEncode($_GET['reg_lat']);
			$lng = secureEncode($_GET['reg_lng']);
			

			$username = secureEncode($_GET['reg_username']);

			$date = date('m/d/Y', time());

			$birthdayArray = explode('-', $birthday);
			$yearAge = date('Y');
			$age = $yearAge - $birthdayArray[0];
			$monthAge = date('m');
			if($monthAge < $birthdayArray[1]){
				$age = $age-1;
			}

			$birthday = date('F', mktime(0, 0, 0, $birthdayArray[1], 10)).' '.$birthdayArray[2].', '.$birthdayArray[0];

			$time = time();		
			$arr = array();
			$arr['error'] = 0;

			$ip = getUserIpAddr();
			$sage = '18,30,1';
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][181]['text'];	
				echo json_encode($arr);
				exit;	
			}		
			if($email == "" || $email == NULL || $password == "" || $password == NULL ){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][182]['text'];
				echo json_encode($arr);
				exit;	
			}		

			if(checkIfExist('blocked_ips','ip',$ip) == 1){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][656]['text'];			
				echo json_encode($arr);
				exit;							
			}

			if(checkIfExist('blocked_users','email',$email) == 1){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][656]['text'];			
				echo json_encode($arr);
				exit;							
			}	

			$bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$city." ".$country;

			//CHECK IF USER EXIST
			$email_check = $mysqli->query("SELECT email FROM users WHERE email = '".$email."'");	
			if($email_check->num_rows == 1 ){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][188]['text'];
				echo json_encode($arr);
				exit;
			} else {

				$salt = base64_encode($name.$email);
				$pswd = crypt($password,$salt);

				$lang = getData('languages','id','WHERE id = '.$_SESSION['lang']);
				if($lang == 'noData'){
					$lang = $sm['plugins']['settings']['defaultLang'];
				}

				$query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,online_day,password,ip,last_access,username,join_date_time,app_id) VALUES ('".$name."', '".$email."','".$pswd."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$lang."','".$date."','".$bio."','".$looking."','18,35,1',0,0,'".$password."','".$ip."','".time()."','".$username."','".time()."','".$dID."')";	
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

					if($photo != ''){
						$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$thumb."',1)";
						$mysqli->query($query2);
					}	

					if($sm['plugins']['email']['enabled'] == 'Yes'){
						if($sm['plugins']['settings']['forceEmailVerification'] == 'Yes'){
							welcomeMailVerification($name,$last_id,$email,$password);					
						} else {
							welcomeMailNotification($name,$email,$password);
						}							
					}

					getUserInfo($last_id,0);
					$_SESSION['user'] = $last_id;
					setcookie("user", $last_id, 2147483647);
					$arr['user'] = $sm['user'];
					$arr['user']['slike'] = getUserSuperLikes($last_id);
					$age = $sm['user']['s_age'];
					$e_age = explode( ',', $age );		
					$arr['user']['sage'] = $e_age[1];
					$arr['user']['photos'] = userAppPhotos($last_id);
					$arr['user']['notification'] = userNotifications($last_id);						
					echo json_encode($arr);				
				}							 
			}		
		break;	
		case 'logout':
			$dID = secureEncode($_GET['query']);
			$mysqli->query("UPDATE users set app_id = 0 where app_id = '".$dID."'");
			if (isset($_SESSION['user'])) {
				unset($_SESSION['user']);
			}
			setcookie("user", 0, time() - 3600);
		break;


		case 'fbconnect':
		$arr = array();
		$query = secureEncode($_GET['query']);
		$data = explode(',',$query);
		$fuid = $data[0];
		$email = $data[1];
		$name = $data[2];
		$gender = $data[3];
		$dID = $data[4];
	    $location = json_decode(file_get_contents('http://api.ipstack.com/'.$_SERVER['REMOTE_ADDR'].'?access_key='.$sm['plugins']['ipstack']['key']));
		$city = $location->city; 	
		$country = $location->country_name; 	
		$lat = $location->latitude; 	
		$lng = $location->longitude; 	
	    $check = $mysqli->query("select id from users where facebook_id = '".$fuid."'");
		$photo = "https://graph.facebook.com/".$fuid."/picture?type=large";
		$pswd = $fuid;
		$name = secureEncode($name);
		if (!empty($_SESSION['user'])){
			$id = secureEncode($_SESSION['user']);
			$query = "UPDATE users SET verified = 1 WHERE id = '".$id."'";
			$mysqli->query($query);
			$query = "UPDATE users SET facebook_id = '".$fuid."' WHERE id = '".$id."'";
			$mysqli->query($query);			
			return true;
		} 	
		if ($check->num_rows == 1){	
			$su = $check->fetch_object();
			$query = "UPDATE users SET verified = 1,app_id = '".$dID."' WHERE id = '".$su->id."'";
			$mysqli->query($query);
			$_SESSION['user'] = $su->id;
			getUserInfo($su->id);
			$arr['user'] = $sm['user'];
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);				
			echo json_encode($arr);	
		} else {
			if($gender == 'male'){
				$gender = 1;
				$looking = 2;
			} else {
				$gender = 2;
				$looking = 1;
			}
			$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,facebook_id,credits,app_id)
									VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$fuid."','".$sm['config']['free_credits']."','".$dID."')";	
			if ($mysqli->query($query) === TRUE) {
				$last_id = $mysqli->insert_id;
				$_SESSION['user'] = $last_id;
				setcookie("user", $last_id, 2147483647);	
				$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
				$free_premium = $sm['config']['free_premium'];
				$time = time();	
				$extra = 86400 * $free_premium;
				$premium = $time + $extra;
				$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
				$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
				$mysqli->query($query2);				
				$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
				$mysqli->query("INSERT INTO users_extended (uid,field1) VALUES ('".$last_id."','".$sm['lang'][224]['text']."')");	
				getUserInfo($last_id);
				$arr['user'] = $sm['user'];
				$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
				$age = $sm['user']['s_age'];
				$e_age = explode( ',', $age );		
				$arr['user']['sage'] = $e_age[1];
				$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
				$arr['user']['notification'] = userNotifications($sm['user']['id']);		
				$time = time();
				$mysqli->query("INSERT INTO users_visits (u1,u2,timeago) values ('".$sm['user']['id']."',44,'".$time."')");
				$mysqli->query("INSERT INTO users_likes (u1,u2,time,love) values (44,'".$sm['user']['id']."','".$time."',1)");			
				echo json_encode($arr);	
			}							 
		}	
		break;		
		case 'userProfile':
			$id = secureEncode($_GET['id']);			
			$arr = array();
			$device_check = $mysqli->query("SELECT id FROM users WHERE id = '".$id."'");
			if($device_check->num_rows == 0 ){
				$arr['user'] = '';
			} else {
				$pass = $device_check->fetch_object();
				getUserInfo($pass->id,0);
				$_SESSION['user'] = $pass->id;
				$arr['user'] = $sm['user'];
				$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
				$age = $sm['user']['s_age'];
				$e_age = explode( ',', $age );		
				$arr['user']['sage'] = $e_age[1];	
				$arr['user']['photos'] = userAppPhotos($pass->id);
				$sm['lang'] = siteLang($sm['user']['lang']);
				$sm['alang'] = appLang($sm['user']['lang']);	
				$arr['user']['notification'] = userNotifications($pass->id);
				echo json_encode($arr);
			}
		break;

		case 'unreadMessageCount':
			$id = secureEncode($_GET['id']);			
			$arr = array();
			$arr['unreadMessageCount'] = checkUnreadMessages($id);
			echo json_encode($arr);		
		break;

		case 'messageRead':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$time = time();
			$rid = $data[0];
			$sid = $data[1];

			$mysqli->query('UPDATE chat SET seen = 1,notification = 1 WHERE r_id = "'.$rid.'" AND s_id = "'.$sid.'"');
			echo json_encode($arr);		
		break;		

		case 'config':
			if(isset($_GET['dID'])){
				$dID = secureEncode($_GET['dID']);	
			} else {
				$dID = 0;
			}

			$arr = array();
			$lang = secureEncode($_GET['siteLang']);
			$arr['lang'] = siteLang($lang);
			$arr['alang'] = appLang($lang);			
			$device_check = $mysqli->query("SELECT id FROM users WHERE id = '".$dID."'");
			if($device_check->num_rows == 0 ){
				$arr['user'] = '';
			} else {
				$pass = $device_check->fetch_object();
				getUserInfo($pass->id,0);
				setcookie("user", $pass->id, 2147483647);
				$_SESSION['user'] = $pass->id;
				$arr['user'] = $sm['user'];
				$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
				$age = $sm['user']['s_age'];
				$e_age = explode( ',', $age );		
				$arr['user']['sage'] = $e_age[1];	
				$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
				$lang = $sm['user']['lang'];
				$sm['lang'] = siteLang($lang);
				$sm['alang'] = appLang($lang);	
				$arr['user']['notification'] = userNotifications($pass->id);
			}
			$arr['config'] = $sm['config'];
			$arr['config']['languages'] = selectLanguages();
			$arr['config']['pusher'] = $sm['plugins']['pusher']['key'];
			$arr['config']['pusher_cluster'] = $sm['plugins']['pusher']['cluster'];
			$arr['config']['wEnabled'] = siteConfig('wEnabled');
			$arr['withdrawl'] = getWithdrawPackages();
			$arr['config']['wTime'] = siteConfig('wTime');
			$arr['config']['visit_back'] = siteConfig('visit_back');
			$arr['config']['like_back'] = siteConfig('like_back');
			$arr['prices'] = $sm['price'];
			$arr['app'] = appConfigApi();
			$arr['config']['theme'] = $sm['theme'];
			$arr['config']['plugins'] = $sm['plugins'];
			$arr['config']['fb_app_id'] = siteConfig('fb_app_id');
			$arr['account_basic'] = $sm['basic'];
			$arr['account_premium'] = $sm['premium'];
	
			$arr['gifts'] = getGiftsApp();

			$arr['credits_package'] = getCreditsPriceApp();
			$arr['premium_package'] = getPremiumPriceApp();		
			$arr['config']['genders'] = siteGenders($lang);
			echo json_encode($arr);
			exit;	
		break;	
		case 'cuser':
			$id = secureEncode($_GET['uid1']);
			$me = secureEncode($_GET['uid2']);
			$myLang = getData('users','lang','WHERE id = '.$me);
			getUserInfo($id);
			$arr = array();
			$time_now = time();
			$arr['user'] = $sm['user'];
			if($sm['user']['bio'] == ''){
				$arr['user']['bio'] = $sm['alang'][125]['text'];
			}

			$data = profileQuestion($myLang,$sm['user']['gender']);
			foreach($data as $key=>$value){ 
				$data[$key]['userAnswer'] = userProfileAnswer($sm['user']['id'],$value['id'],$myLang);
				$data[$key]['answers'] = profileQuestionAnswer($value['id'],$myLang);
			}
			$arr['user']['question'] = $data;		

			$arr['user']['photos'] = userAppPhotos($id);
			$arr['user']['videos'] = userAppPhotos($id,1);
			$arr['user']['isFan'] = isFanApp($me,$id);
			$today = date('w');		
			if($sm['user']['last_access'] >= $time_now || $sm['user']['fake'] == 1 && $sm['user']['online_day'] == $today){
				$arr['user']['status'] = 'y';
			} else {	
				$arr['user']['status'] = 'n';
			}
			$arr['user']['unlocked'] = unblockedUser($me,$id);
			$arr['game'][] = array(
				  "id" => $sm['user']['id'],
				  "name" => $sm['user']['name'],
				  "status" => userFilterStatus($sm['user']['id']),
				  "distance" => '',				  
				  "age" => $sm['user']['age'],
				  "city" => $sm['user']['city'],
				  "bio" => $sm['user']['bio'],	
				  "isFan" => isFanApp($sm['user']['id'],$me),
				  "total" => getUserTotalLikers($sm['user']['id']),
				  "photo" => profilePhoto($sm['user']['id']),
				  "full" => $sm['user'],
				  "error" => 0
			);				
			echo json_encode($arr);
			exit;	
		break;
		case 'spotlight':
			$id = secureEncode($_GET['id']);
			getUserInfo($id);	
			$info = array();			
			$time = time()-86400;
			$time_now = time()-300;
			$i = 0;
			$lat = $sm['user']['lat'];
			$lng = $sm['user']['lng'];

			$limit = $sm['plugins']['spotlight']['limit'];
			$area = $sm['plugins']['spotlight']['area'];
			$autoWorldwide = $sm['plugins']['spotlight']['worldwide'];

			if($area == 'Worldwide'){			
				$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
				* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
				FROM spotlight
				ORDER BY distance
				LIMIT $limit
				");			
				if ($spotlight->num_rows > 0) { 
					while($spotl = $spotlight->fetch_object()){
						getUserInfo($spotl->u_id,1);
						$id = $spotl->u_id;
						$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
						if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
							continue;							
						}						
						$info['spotlight'][] = array(
							  "id" => $spotl->u_id,
							  "name" => $sm['profile']['name'],
							  "gender" => $sm['profile']['gender'],
							  "firstName" => $sm['profile']['first_name'],					  
							  "age" => $sm['profile']['age'],
							  "city" => $sm['profile']['city'],				  	  
							  "photo" => profilePhoto($spotl->u_id),
							  "spotPhoto" => $spotl->photo,
							  "error" => 0,
							  "status" => userFilterStatus($spotl->u_id)
						);	
						$i++;			
					}
				}
			} else {
				if($area == 'City'){
					$filter = 'WHERE city ="'.$sm['user']['city'].'"';
				} else {
					$filter = 'WHERE country ="'.$sm['user']['country'].'"';
				}
				$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
				* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
				FROM spotlight
				$filter
				ORDER BY distance
				LIMIT $limit
				");			
				if ($spotlight->num_rows > 0) { 
					while($spotl = $spotlight->fetch_object()){
						getUserInfo($spotl->u_id,1);
						$id = $spotl->u_id;
						$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
						if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
							continue;							
						}						
						$info['spotlight'][] = array(
							  "id" => $spotl->u_id,
							  "name" => $sm['profile']['name'],
							  "gender" => $sm['profile']['gender'],
							  "firstName" => $sm['profile']['first_name'],					  
							  "age" => $sm['profile']['age'],
							  "city" => $sm['profile']['city'],				  	  
							  "photo" => profilePhoto($spotl->u_id),
							  "spotPhoto" => $spotl->photo,
							  "error" => 0,
							  "status" => userFilterStatus($spotl->u_id)
						);	
						$i++;			
					}
				}

				if($autoWorldwide == 'Yes'){
					if($i < $limit){
						$diff = $limit - $i;
						$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
						* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
						FROM spotlight
						ORDER BY distance
						LIMIT $diff
						");
						if($spotlight && $spotlight->num_rows > 0){ 
							while($spotl = $spotlight->fetch_object()){	
								getUserInfo($spotl->u_id,1);
								$id = $spotl->u_id;
								$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
								if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
									continue;							
								}						
								$info['spotlight'][] = array(
									  "id" => $spotl->u_id,
									  "name" => $sm['profile']['name'],
									  "gender" => $sm['profile']['gender'],
									  "firstName" => $sm['profile']['first_name'],					  
									  "age" => $sm['profile']['age'],
									  "city" => $sm['profile']['city'],				  	  
									  "photo" => profilePhoto($spotl->u_id),
									  "spotPhoto" => $spotl->photo,
									  "error" => 0,
									  "status" => userFilterStatus($spotl->u_id)
								);	
								$i++;	
							}	
						}	
					}
				}				

			}

			if($sm['plugins']['spotlight']['autocomplete'] == 'Yes'){
				if($i < $limit){
					$diff = $limit - $i;
					$spotlight = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
					* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
					FROM users
					ORDER BY distance, popular DESC
					LIMIT $diff
					");
					if($spotlight && $spotlight->num_rows > 0){ 
						while($spotl = $spotlight->fetch_object()){	

							getUserInfo($spotl->id,1);
							$id = $spotl->id;
							$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
							if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
								continue;							
							}						
							$info['spotlight'][] = array(
								  "id" => $spotl->id,
								  "name" => $sm['profile']['name'],
								  "gender" => $sm['profile']['gender'],
								  "firstName" => $sm['profile']['first_name'],					  
								  "age" => $sm['profile']['age'],
								  "city" => $sm['profile']['city'],				  	  
								  "photo" => profilePhoto($spotl->id),
								  "spotPhoto" => $sm['profile']['profile_photo'],
								  "error" => 0,
								  "status" => userFilterStatus($spotl->id)
							);	
							$i++;

						}	
					}			
				}
			}			

			echo json_encode($info);
		break;
		case 'data':
			$arr = array();
			$q = secureEncode($_GET['query']);
			$q = str_replace("%20", " ", $q);
			$query = $mysqli->query($q);
			if($query->num_rows > 0 ){
				while($p = $query->fetch_object()){
					getUserInfo($p->id,0);
					$arr['user'] = $sm['user'];	
					$arr['user']['photos'] = userAppPhotos($p->id);
					echo json_encode($arr);
				}
			}		
			exit;	
		break;	
		case 'addToSpotlight':
			$id = secureEncode($_GET['query']);
			$time = time();
			getUserInfo($id);
			$lat = $sm['user']['lat'];
			$lng = $sm['user']['lng'];
			$photo = $sm['user']['profile_photo'];
			$lang = $sm['user']['lang'];	
			$price = $sm['price']['spotlight'];
			$query = "INSERT INTO spotlight (u_id,time,lat,lng,photo,lang,country,city)
			 VALUES ('".$id."', '".$time."', '".$lat."', '".$lng."', '".$photo."', '".$lang."', '".$sm['user']['country']."','".$sm['user']['city']."') ON DUPLICATE KEY UPDATE time = '".$time."'";
			$mysqli->query($query);	
			$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$id."'";
			$mysqli->query($query2);			
		break;

		case 'purchaseStory':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$time = time();
			$uid = $data[0];
			$sid = $data[1];
			$query = "INSERT INTO users_story_purchase (sid,uid,time) VALUES ('".$sid."', '".$uid."', '".$time."') ON DUPLICATE KEY UPDATE time = '".$time."'";
			$mysqli->query($query);			
		break;

		case 'storyPrice':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$time = time();
			$sid = $data[0];
			$credits = $data[1];
			$query = "UPDATE users_story set credits = '".$credits."' where id = '".$sid."'";
			$mysqli->query($query);			
		break;	

		case 'deleteStory':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$time = time();
			$sid = $data[0];
			$query = "UPDATE users_story set deleted = 1 where id = '".$sid."'";
			$mysqli->query($query);			
		break;

		case 'leaderboard':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$time = time();
			$uid = $data[0];
        	$leaderboard = array();
        	$leaderboardArray = getArray('chat','WHERE r_id = "'.$uid.'" and credits > 0','ID DESC');
        	foreach ($leaderboardArray as $l) {
        		$keys = array($l['s_id']);
				$data = array_fill_keys($keys, $l['credits']);
				if (array_key_exists($l['s_id'],$leaderboard)){
        			$prev = $leaderboard[$l['s_id']];
        			$leaderboard[$l['s_id']] = $prev + $l['credits'];
				} else {
					$leaderboard = $leaderboard + $data;
				}	        				
        	}
        	arsort($leaderboard);	
        	echo json_encode($leaderboard);	
		break;	

										

		case 'updateCredits':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$amount = $data[1];
			$type = $data[2];
			$reason = $data[3];
			$reward = $data[4];
			$time = time();
			if($type == 1){
				$query = "UPDATE users SET credits = credits-'".$amount."' WHERE id= '".$uid."'";
			} else {
				$query = "UPDATE users SET credits = credits+'".$amount."' WHERE id= '".$uid."'";
			}	
			$mysqli->query($query);

			if($type == 'reward'){
				$mysqli->query("INSERT INTO users_rewards (uid,reward,reward_type,reward_date,reward_amount) 
				VALUES ('".$uid."','".$reward."','credits','".$time."','".$amount."')");

				$mysqli->query("INSERT INTO users_credits (uid,credits,reason,time,type) 
				VALUES ('".$uid."','".$amount."','".$reason."','".$time."','added')");			
			}

			if($type == 1){
				$mysqli->query("INSERT INTO users_credits (uid,credits,reason,time) 
				VALUES ('".$uid."','".$amount."','".$reason."','".$time."')");
			}					
		break;

		case 'meet':
			$id = secureEncode($_GET['uid1']);
			$l = secureEncode($_GET['uid2']);
			$status = secureEncode($_GET['uid3']);		
			getUserInfo($id);
			$time = time();
			if($sm['user']['last_access'] < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");	
			}		
			$info = array();	
			$i = 0;
			$time_now = time()-300;
			$lat = $sm['user']['lat'];
			$lng = $sm['user']['lng'];
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );
			$age1 = $e_age[0];
			$age2 = $e_age[1];		
			$today = date('w');
			$looking = $sm['user']['s_gender'];
			$radius = $sm['user']['s_radius'];		

			$check2 = $sm['plugins']['meet']['searchResult'] * $l+$sm['plugins']['meet']['searchResult'];
			if($check2 == 0){
				$check2 = 20;
			}
			$all = count($sm['genders']);
			$all = $all + 1;
			if($all == $looking){
				$g = 3;
			} else {
				$g = getGenderSex($looking);
			}
			
			$storyFrom = $sm['plugins']['story']['days'];
			$time = time();	
			$extra = 86400 * $storyFrom;
			$storyFrom = $time - $extra;	

			$license = $sm['settings']['license'];
			$c = $sm['plugins']['fakeUsersGenerator']['generateCountry'];
			$time = time() - rand(1,100000);
			$check = getTotalUsersCity($sm['user']['lat'],$sm['user']['lng'],$radius,$looking,$age); 
			$today = date('w');
			$date = date('m/d/Y', time());
			$amount = $sm['plugins']['fakeUsersGenerator']['generateFakeUsers'];
			$url=$userApi.
			    'g=' . urlencode($g) .
			    '&a=' . urlencode($age) .
			    '&c=' . urlencode($c) .
			    '&amount=' . urlencode($amount) . 
			    '&pc=' . urlencode($license);

			$apiLimit = 'No';
			if($sm['settings']['fakeUserUsage'] >= $sm['settings']['fakeUserLimit']){
				$apiLimit = 'Yes';
			}
			$info['check1'] = $check;
			$info['check2'] = $check2;
			$info['apiLimit'] = $apiLimit;
			if($check < $check2 && $sm['plugins']['fakeUsersGenerator']['enabled'] == 'Yes' && $apiLimit == 'No'){
				$callApi = curl_get_contents($url);
				$api = json_decode($callApi);
				$info['apiResult'] = $api->result;
				if(!empty($api->result)){	

					//activity
					$age = explode( ',', $age );
					$age1 = $age[0];
					$age2 = $age[1];					
					$activity = 'Created '.$amount.' '.getGenderName($looking).'('.$age1.','.$age2.') profiles from '.$sm['user']['city'].' - '.$sm['user']['country'];
					activity('system',$activity,'Fake user generator');	

					$month= date("m");
					$de= date("d");
					$y= date("Y");

					foreach ($api->result as $val) {

						$FAonlineDay = rand(0,1);
						if($sm['plugins']['fakeUsersGenerator']['generateOnline'] == 'Yes'){
							$FAonlineDay = 1;
						}		

						if($FAonlineDay == 0){
							$today = $today+1;
						}	

						$year = $y-$val->age;
						$birthday = date('F', mktime(0, 0, 0, $month, 10)).' '.$de.', '.$year;									
						$email = $val->name.$val->id.'@gmail.com';
						$mysqli->query("INSERT INTO users (id,name,email,pass,age,birthday,city,country,gender,lat,lng,credits,premium,last_access,app_id,facebook_id,looking,verified,popular,lang,admin,fake,online_day,join_date,join_date_time,username)
						 VALUES ('".$val->id."', '".$val->name."', '".$email."', '', '".$val->age."', '".$birthday."','".$sm['user']['city']."', '".$sm['user']['country']."', '".$looking."', '".$sm['user']['lat']."', '".$sm['user']['lng']."', '0', '0', '".$time."', '0', '0', '1', '1', '0', '1', '0', '1', '".$today."', '".$date."', '".time()."','".$val->id."')");


						$mysqli->query('INSERT INTO users_online_day (uid,mon,tue,wed,thu,fri,sat,sun)
						 VALUES ("'.$val->id.'","'.$FAonlineDay.'","'.$FAonlineDay.'","'.$FAonlineDay.'","'.$FAonlineDay.'","'.$FAonlineDay.'","'.$FAonlineDay.'","'.$FAonlineDay.'") ON DUPLICATE KEY UPDATE mon = "'.$FAonlineDay.'",tue = "'.$FAonlineDay.'",wed = "'.$FAonlineDay.'",thu = "'.$FAonlineDay.'",fri = "'.$FAonlineDay.'",sat = "'.$FAonlineDay.'", sun ="'.$FAonlineDay.'"');

						$mysqli->query("INSERT INTO users_photos (u_id,photo,thumb,profile,fake) VALUES ('".$val->id."', '".$val->photo."', '".$val->photo."',1,1)");
						
						if($sm['plugins']['fakeUsersGenerator']['profileQuestions'] == 'Yes'){
							$arr = profileQuestion($sm['user']['lang']);
							foreach($arr as $key=>$value){ 
								$a = getRandomAnswer($value['id'],$sm['user']['lang']);
								$mysqli->query("INSERT INTO users_profile_questions (uid,qid,answer,fake) VALUES ('".$val->id."', '".$value['id']."', '".$a."',1)");
							} 
						}
					}

					if($api->usage > $sm['settings']['fakeUserLimit']){
						$api->usage = $sm['settings']['fakeUserLimit'];
					}

					$mysqli->query("UPDATE settings SET setting_val = '".$api->usage."' WHERE setting = 'fakeUserUsage'");
				}
			}

			$limit = $l * 9;
			$all = count($sm['genders']);
			$all = $all + 1;		
			if($status == 0){	
				$status_filter = "";	
			} else {
				$time_now = time()-300;
				if($looking == $all) {
					$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND id <> '".$sm['user']['id']."' AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
				} else {
					$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND id <> '".$sm['user']['id']."' AND  online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
				}
			}	
			$country_filter = '';
			if($radius < 950){
				$country_filter = "AND country = '".$sm['user']['country']."'";			
			}else {
				$radius	= 999999;
			}
			if($looking == $all) {
				$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
				* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
				FROM users
				WHERE age BETWEEN '".$age1."' AND '".$age2."'
				AND id <> '".$sm['user']['id']."'	
				$status_filter	
				$country_filter			
				HAVING distance < $radius
				ORDER BY last_access DESC , fake ASC
				LIMIT $limit, 9";	
				$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
				* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
				FROM users
				WHERE age BETWEEN '".$age1."' AND '".$age2."'
				AND id <> '".$sm['user']['id']."'	
				$status_filter	
				$country_filter		
				HAVING distance < $radius
				ORDER BY last_access";			
			} else {
				$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
				* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
				FROM users
				WHERE gender = '".$looking."'
				AND age BETWEEN '".$age1."' AND '".$age2."'
				AND id <> '".$sm['user']['id']."'	
				$status_filter	
				$country_filter		
				HAVING distance < $radius
				ORDER BY last_access DESC , fake ASC
				LIMIT $limit, 9";	
				$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
				* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
				FROM users
				WHERE gender = '".$looking."'
				AND age BETWEEN '".$age1."' AND '".$age2."'
				AND id <> '".$sm['user']['id']."'
				$status_filter	
				$country_filter		
				HAVING distance < $radius
				ORDER BY last_access";			
			}
			$result = $mysqli->query($query);
			$result2 = $mysqli->query($query2);
			$sm['meet_result'] = $result2->num_rows;
			if ($result->num_rows > 0) {
				while($row = $result->fetch_object()){
					getUserInfo($row->id,6);

					$storiesFilter = 'where uid = '.$sm['search']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';	

					$i++;				
					if ($i == 2 || $i == 5 || $i == 8 || $i == 11 || $i == 14 || $i == 17 || $i == 20 || $i == 23) {
						$margin = 'search-margin';
					} else {
						$margin = 'search-no-margin';
					}	
					if($sm['search']['city'] !== ''){
						$city = $sm['search']['city'];
					} else {
						$city = $sm['search']['country'];
					}
					if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
						$on = 1;
					} else {	
						$on = 0;
					}
					$match = 0;
					if(isFanApp($sm['user']['id'],$sm['search']['id']) && isFanApp($sm['search']['id'],$sm['user']['id'])){
						$match = 1;
					}
					$allowed = true;
					if($on != 0 && $sm['plugins']['meet']['viewOnlyPremiumOnline'] == 'Yes' && $sm['user']['premium'] == 0){
						$allowed = false;
					}	

					$info['result'][] = array(
						  "id" => $sm['search']['id'],
						  "name" => $sm['search']['name'],
						  "firstName" => $sm['search']['first_name'],					  
						  "age" => $sm['search']['age'],
						  "gender" => $sm['search']['gender'],
						  "city" => $city,				  	  
						  "photo" => profilePhoto($sm['search']['id']),
						  "photoBig" => profilePhoto($sm['search']['id'],1),
						  "error" => 0,
						  "show" => $i,
						  "status" => $on,
						  "allowed" => $allowed,
						  "blocked" => blockedUser($sm['user']['id'],$sm['search']['id']),
						  "margin" => $margin,
						  "story" => selectC('users_story',$storiesFilter),
					  	  "stories" => json_encode(getUserStories($sm['search']['name'],$sm['search']['profile_photo'],$storiesFilter,'storyTime ASC')),						  
						  "fan" => isFanApp($sm['user']['id'],$sm['search']['id']),
						  "match" => $match
						);
				}
			} else {
				$info['result'] = '';
			}
			$sm['meet_result'] = $sm['meet_result'] - $limit;
			if($sm['meet_result'] >= 1){
				$totalPages = 1;
			} else {
				$totalPages = 0;
			}
			$totalp = $totalPages-1;
			$limitp = $l+1;
			if($totalp >=0 ){
				$pages = $totalp;
				$info['pages'] = $totalp;
			} else {
				$pages = 0;
				$info['pages'] = 0;
			}	

			//populars
			$time_now = time()-300;
			$x=0;

			$filter = '';
			$limit = $sm['plugins']['populars']['searchResult'];

			if($sm['plugins']['populars']['popularSearchFilterGender'] == 'By User Criteria'){
				if($sm['user']['s_gender'] != $all) {
					$filter = " AND gender = '".$sm['user']['s_gender']."'";
				} 
			} else {
				if($sm['plugins']['populars']['popularSearchFilterGender'] != $all){
					$filter = " AND gender = '".$sm['plugins']['populars']['popularSearchFilterGender']."'";
				}
			}

			if($sm['plugins']['populars']['popularSearchFilter'] != 'Worldwide'){
				if($sm['plugins']['populars']['popularSearchFilter'] == 'Country'){
					$filter.=" AND country = '".$sm['user']['country']."'";
				} else {
					$filter.=" AND city = '".$sm['user']['city']."'";
				}
			}

			$query = $mysqli->query("SELECT id
			FROM users
			WHERE id <> '".$sm['user']['id']."'
			$filter	
			ORDER BY popular desc, last_access desc
			LIMIT $limit
			");				
			if ($query->num_rows > 0) {
				while($row = $query->fetch_object()){
					getUserInfo($row->id,6);
					$allowed = true;
					if($sm['plugins']['populars']['viewOnlyPremium'] == 'Yes' 
						&& $sm['user']['premium'] == 0){
						$allowed = false;
					}

					$storiesFilter = 'where uid = '.$sm['search']['id'].' and storyTime >'.$storyFrom.' and deleted = 0 and review = "No"';	
	
					if($sm['search']['city'] !== ''){
						$city = $sm['search']['city'];
					} else {
						$city = $sm['search']['country'];
					}
					if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
						$on = 1;
					} else {	
						$on = 0;
					}
					$match = 0;
					if(isFanApp($sm['user']['id'],$sm['search']['id']) && isFanApp($sm['search']['id'],$sm['user']['id'])){
						$match = 1;
					}					
					$info['popular'][] = array(
					  "id" => $sm['search']['id'],
					  "name" => $sm['search']['name'],
					  "firstName" => $sm['search']['first_name'],					  
					  "age" => $sm['search']['age'],
					  "gender" => $sm['search']['gender'],
					  "city" => $city,				  	  
					  "photo" => profilePhoto($sm['search']['id']),
					  "photoBig" => profilePhoto($sm['search']['id'],1),
					  "error" => 0,
					  "show" => $x,
					  "allowed" => $allowed,
					  "status" => $on,
					  "blocked" => blockedUser($sm['user']['id'],$sm['search']['id']),
					  "story" => selectC('users_story',$storiesFilter),
				  	  "stories" => json_encode(getUserStories($sm['search']['name'],$sm['search']['profile_photo'],$storiesFilter,'storyTime ASC')),						  
					  "fan" => isFanApp($sm['user']['id'],$sm['search']['id']),
					  "match" => $match
					);

					$x++;
				}
			}

			echo json_encode($info);
		break;

		case 'delete_profile':
			$uid = secureEncode($_GET['query']);

			$checkAdmin = getData('users','admin','where id = '.$uid);
			if($checkAdmin == 1){
				exit;
			}
			$mysqli->query("DELETE FROM reports WHERE reported = '".$uid."'");	
			$mysqli->query("DELETE FROM users WHERE id = '".$uid."'");
			$mysqli->query("DELETE FROM spotlight WHERE u_id = '".$uid."'");
			$mysqli->query("DELETE FROM chat WHERE s_id = '".$uid."'");	
			$mysqli->query("DELETE FROM chat WHERE r_id = '".$uid."'");
			$mysqli->query("DELETE FROM users_visits WHERE u1 = '".$uid."'");	
			$mysqli->query("DELETE FROM users_visits WHERE u2 = '".$uid."'");			
			$mysqli->query("DELETE FROM users_likes WHERE u1 = '".$uid."'");
			$mysqli->query("DELETE FROM users_likes WHERE u2 = '".$uid."'");		
			$mysqli->query("DELETE FROM users_photos WHERE u_id = '".$uid."'");
			$mysqli->query("DELETE FROM users_profile_questions WHERE uid = '".$uid."'");
			$mysqli->query("DELETE FROM users_interest WHERE u_id = '".$uid."'");
			$mysqli->query("DELETE FROM users_chats WHERE uid = '".$uid."'");
			$mysqli->query("DELETE FROM users_withdraw WHERE u_id = '".$uid."'");
			$mysqli->query("DELETE FROM users_verification WHERE uid = '".$uid."'");
			$mysqli->query("DELETE FROM users_premium WHERE uid = '".$uid."'");	
			$mysqli->query("DELETE FROM users_videocall WHERE u_id = '".$uid."'");
			$mysqli->query("DELETE FROM users_story WHERE uid = '".$uid."'");
			$mysqli->query("DELETE FROM users_notifications WHERE uid = '".$uid."'");

			if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
				$activity = 'User ID ('.$uid.') has deleted his profile';
				activity('system',$activity,'Deleted '.$uid.'');	
			}		
			if (isset($_SESSION['user'])) {
				unset($_SESSION['user']);
				setcookie("user", 0, time() - 3600);
			}
			if (isset($_SESSION['new_user'])) {
				unset($_SESSION['new_user']);
			}
		break;		

		case 'addVisit':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$id = $data[0];
			$uid1 = $data[1];
			$time = time();

			//old time
			$oldTime = getData('users_visits','timeago','where u1 = '.$uid1.' and u2 = '.$id);

			$mysqli->query("INSERT INTO users_visits (u1,u2,timeago) VALUES ('".$uid1."','".$id."','".$time."') ON DUPLICATE KEY UPDATE timeago = '".$time."'");

			$checkTime = $time;

			$timeout = $sm['plugins']['fakeUsersInteractions']['notificationTimeout']*60;
			if($oldTime != 'noData'){
				$checkTime = $oldTime + $timeout; //check 30 min	
			}
			
			if($oldTime == 'noData' || $checkTime < $time){ //SEND NOTIFICATION
			
				$name = getData('users','name','where id ='.$id);
				$photo = profilePhoto($id);
				$appId = getData('users','app_id','where id ='.$uid1);
				$nameUser = getData('users','name','where id ='.$uid1);
				$userPremium = getData('users','premium','where id ='.$uid1);
				$fake = getData('users','fake','where id ='.$uid1);

		        $noti= 'visit'.$uid1;
		        $data['id'] = $id;
		        $data['message'] = $sm['alang'][252]['text'];
		        $data['time'] = date("H:i", time());
		        $data['type'] = 4;
		        $data['icon'] = $photo;
		        $data['name'] = $name;
		        $data['action'] = 'visit';      
		        $data['photo'] = 0;
		        $data['unread'] = checkUnreadMessages($uid1);       

		        if($fake == 0){
		        	if(is_numeric($sm['plugins']['er']['id'])){ 
						$sm['push']->trigger($sm['plugins']['pusher']['key'], $noti, $data);
					}
					if($appId != 0){
						pushNotification($appId,$name,'Visit your profile','');	
					}
		        }			
	        }

		break;

		case 'getMatches':
			$id = secureEncode($_GET['id']);	
			getUserInfo($id);
			$arr = array();
			$time = time();
			if($sm['user']['last_access'] < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");	
			}
			$gift = 0;

			$storyFrom = $sm['plugins']['story']['days'];
			$time = time();	
			$extra = 86400 * $storyFrom;
			$storyFrom = $time - $extra;	

			$query = $mysqli->query("SELECT u2,time,notification from users_likes where u1 = '".$id."' and love = 1 order by time desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){
					getUserInfo($result->u2,1);

					$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';
					$arr['mylikes'][] = array(
						  "id" => $sm['profile']['id'],
						  "name" => $sm['profile']['name'],
						  "firstName" => $sm['profile']['first_name'],					  
						  "age" => $sm['profile']['age'],
						  "city" => $sm['profile']['city'],
						  "last_a" => $sm['profile']['last_access'],
						  "premium" => $sm['profile']['premium'],							  
						  "photo" => profilePhoto($sm['profile']['id']),
						  "error" => 0,
						  "story" => selectC('users_story',$storiesFilter),
					  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),							  
						  "status" => userFilterStatus($sm['profile']['id']),
						  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
						  "last_m_time" => get_time_difference_php($result->time),
						  "credits" => $sm['profile']['credits'],
						  "check_m" => $result->notification,
						  "gift" => $gift						  
					);		
				}	
			}

			$query = $mysqli->query("SELECT u1,time,notification from users_likes where u2 = '".$id."' and love = 1 order by time desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){
					getUserInfo($result->u1,1);
					$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';						
					$arr['myfans'][] = array(
						  "id" => $sm['profile']['id'],
						  "name" => $sm['profile']['name'],
						  "firstName" => $sm['profile']['first_name'],					  
						  "age" => $sm['profile']['age'],
						  "city" => $sm['profile']['city'],
						  "last_a" => $sm['profile']['last_access'],
						  "premium" => $sm['profile']['premium'],							  
						  "photo" => profilePhoto($sm['profile']['id']),
						  "error" => 0,
						  "story" => selectC('users_story',$storiesFilter),
					  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),							  
						  "status" => userFilterStatus($sm['profile']['id']),
						  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
						  "last_m_time" => get_time_difference_php($result->time),
						  "credits" => $sm['profile']['credits'],
						  "check_m" => $result->notification,
						  "gift" => $gift						  
					);
								
				}	
			}
			$query = $mysqli->query("SELECT u1,time,notification from users_likes where u2 = '".$id."' and love = 1 order by time desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){
					if(isFan($id,$result->u1) == 1){
						getUserInfo($result->u1,1);
						$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';						
						$arr['matches'][] = array(
							  "id" => $sm['profile']['id'],
							  "name" => $sm['profile']['name'],
							  "firstName" => $sm['profile']['first_name'],					  
							  "age" => $sm['profile']['age'],
							  "city" => $sm['profile']['city'],
							  "last_a" => $sm['profile']['last_access'],
							  "premium" => $sm['profile']['premium'],							  
							  "photo" => profilePhoto($sm['profile']['id']),
							  "error" => 0,
							  "story" => selectC('users_story',$storiesFilter),
						  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),							  
							  "status" => userFilterStatus($sm['profile']['id']),
							  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
							  "last_m_time" => get_time_difference_php($result->time),
							  "credits" => $sm['profile']['credits'],
							  "check_m" => $result->notification,
							  "gift" => $gift						  
						);
					}		
				}	
			}		
			$arr['result'] = 0;
			echo json_encode($arr);
		break;

		case 'getUserData':
			$arr = array();
			$id = secureEncode($_GET['query']);
			$data = explode(',', $id);
			getUserInfo($data[0],1);
			$storyFrom = $sm['plugins']['story']['days'];
			$time = time();	
			$extra = 86400 * $storyFrom;
			$storyFrom = $time - $extra;	
			$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';			
			$arr['user'][] = array(
				  "id" => $sm['profile']['id'],
				  "name" => $sm['profile']['name'],
				  "firstName" => $sm['profile']['first_name'],					  
				  "age" => $sm['profile']['age'],
				  "city" => $sm['profile']['city'],
				  "last_a" => $sm['profile']['last_access'],
				  "premium" => $sm['profile']['premium'],							  
				  "photo" => profilePhoto($sm['profile']['id']),
				  "error" => 0,
				  "story" => selectC('users_story',$storiesFilter),
			  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),							  
				  "status" => userFilterStatus($sm['profile']['id']),
				  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
				  "credits" => $sm['profile']['credits']						  
			);
			echo json_encode($arr);
		break;

		case 'getOnlineFriends':
			$id = secureEncode($_GET['id']);	
			getUserInfo($id);
			$arr = array();
			$time = time();
			$i = 0;
			if($sm['user']['last_access'] < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");	
			}
			$query = $mysqli->query("SELECT u2,time,notification from users_likes where u1 = '".$id."' and love = 1 order by time desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){
					if(isFan($result->u2,$id) == 0){
						getUserInfo($result->u2,1);
						if(userFilterStatus($sm['profile']['id']) == 1){
							$i++;
							$arr['result'][] = array(
								  "id" => $sm['profile']['id'],
								  "name" => $sm['profile']['name'],
								  "firstName" => $sm['profile']['first_name'],					  
								  "age" => $sm['profile']['age'],
								  "city" => $sm['profile']['city'],
								  "last_a" => $sm['profile']['last_access'],
								  "premium" => $sm['profile']['premium'],							  
								  "photo" => profilePhoto($sm['profile']['id']),
								  "error" => 0,
								  "status" => userFilterStatus($sm['profile']['id']),
								  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
								  "last_m_time" => get_time_difference_php($result->time),
								  "credits" => $sm['profile']['credits'],
								  "check_m" => $result->notification,
								  "gift" => $gift						  
							);
						}
					}			
				}	
			}
			$query = $mysqli->query("SELECT u1,time,notification from users_likes where u2 = '".$id."' and love = 1 order by time desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){
					if(isFan($id,$result->u1) == 0){
						getUserInfo($result->u1,1);
						if(userFilterStatus($sm['profile']['id']) == 1){
							$i++;
							$arr['result'][] = array(
								  "id" => $sm['profile']['id'],
								  "name" => $sm['profile']['name'],
								  "firstName" => $sm['profile']['first_name'],					  
								  "age" => $sm['profile']['age'],
								  "city" => $sm['profile']['city'],
								  "last_a" => $sm['profile']['last_access'],
								  "premium" => $sm['profile']['premium'],							  
								  "photo" => profilePhoto($sm['profile']['id']),
								  "error" => 0,
								  "status" => userFilterStatus($sm['profile']['id']),
								  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
								  "last_m_time" => get_time_difference_php($result->time),
								  "credits" => $sm['profile']['credits'],
								  "check_m" => $result->notification,
								  "gift" => $gift						  
							);
						}
					} else {
						getUserInfo($result->u1,1);
						if(userFilterStatus($sm['profile']['id']) == 1){
							$i++;
							$arr['result'][] = array(
								  "id" => $sm['profile']['id'],
								  "name" => $sm['profile']['name'],
								  "firstName" => $sm['profile']['first_name'],					  
								  "age" => $sm['profile']['age'],
								  "city" => $sm['profile']['city'],
								  "last_a" => $sm['profile']['last_access'],
								  "premium" => $sm['profile']['premium'],							  
								  "photo" => profilePhoto($sm['profile']['id']),
								  "error" => 0,
								  "status" => userFilterStatus($sm['profile']['id']),
								  "last_m" => $sm['profile']['city'].','.$sm['profile']['country'],
								  "last_m_time" => get_time_difference_php($result->time),
								  "credits" => $sm['profile']['credits'],
								  "check_m" => $result->notification,
								  "gift" => $gift						  
							);						
						}
					}		
				}	
			}
			$arr['total_online'] = $i;		
			echo json_encode($arr);
		break;	

		case 'getUserVideos':
			$id = secureEncode($_GET['id']);	
			$arr = array();
			$time = time();
			$query = $mysqli->query("SELECT photo,time,private from users_photos where u_id = '".$id."' and video = 1
			 order by id desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){

					$arr['result'][] = array(
						  "video" => $result->photo,
						  "time" => $result->time,
						  "private" => $result->private					  
					);
				}
			}		
			echo json_encode($arr);
		break;	


		case 'getVisitors':
			$id = secureEncode($_GET['id']);	
			getUserInfo($id);
			$arr = array();
			$arr['visitors'] = null;
			$query = $mysqli->query("SELECT u2,timeago,notification from users_visits where u1 = '".$id."' and u2 <> '".$id."' order by timeago desc");
			if ($query->num_rows > 0) { 
				while($result = $query->fetch_object()){
					getUserInfo($result->u2,1);
					if(isFan($id,$result->u2) == 1 && isFan($result->u2,$id) == 1){
						$match = 1;
					} else {
						$match = 0;
					}

					$storyFrom = $sm['plugins']['story']['days'];
					$time = time();	
					$extra = 86400 * $storyFrom;
					$storyFrom = $time - $extra;	
					$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';
					$arr['visitors'][] = array(
						  "id" => $sm['profile']['id'],
						  "name" => $sm['profile']['name'],
						  "firstName" => $sm['profile']['first_name'],					  
						  "age" => $sm['profile']['age'],
						  "city" => $sm['profile']['city'],
						  "last_a" => $sm['profile']['last_access'],
						  "premium" => $sm['profile']['premium'],							  
						  "photo" => profilePhoto($sm['profile']['id']),
						  "fan" => isFan($result->u2,$id),
						  "match" => $match,					  
						  "error" => 0,
						  "story" => selectC('users_story',$storiesFilter),
					  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),						  
						  "status" => userFilterStatus($sm['profile']['id']),
						  "last_m" => $sm['alang'][127]['text'].' '.get_time_difference_php($result->timeago).' ago',
						  "last_m_time" => get_time_difference_php($result->timeago),
						  "credits" => $sm['profile']['credits'],
						  "check_m" => $result->notification					  
					);		
				}	
			}
			echo json_encode($arr);
		break;		
		case 'del_conv':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$sid = $data[1];
			$mysqli->query("UPDATE chat set seen = 2 WHERE r_id = '".$uid."' AND s_id = '".$sid."'");
			$mysqli->query("UPDATE chat set notification = 2 WHERE s_id = '".$uid."' AND r_id = '".$sid."'");		
		break;

		case 'updateInteractionNotification':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$interaction = $data[1];

			$col = 'users_likes';
			if($interaction == 'visits'){
				$col = 'users_visits';
				$mysqli->query("UPDATE $col set notification = 1 WHERE u1 = '".$uid."'");
			} else {
				$mysqli->query("UPDATE $col set notification = 1 WHERE u2 = '".$uid."'");
			}
					
		break;		

		case 'block':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$id = $data[1];
			$reason = $data[2];

			$time = time();
			$query = "INSERT INTO users_blocks (uid1,uid2) VALUES ('".$uid."', '".$id."')";
			$mysqli->query($query);

			$query2 = "DELETE FROM chat where s_id = '".$uid."' AND r_id = '".$id."' || r_id = '".$uid."' AND s_id = '".$id."'";
			$mysqli->query($query2);

			$mysqli->query("UPDATE users_likes SET love = 0 where u1 = '$uid' and u2 = '$id'");		
			$mysqli->query("INSERT INTO users_likes (u1,u2,love,time) VALUES ('$uid','$id',0,'$time')");			

			
			$query = "INSERT INTO reports (reported,reported_by,reported_date,reason) 
			VALUES ('".$id."', '".$uid."', '".$time."', '".$reason."')";
			$mysqli->query($query);			
		break;

		case 'getChat':
		$id = secureEncode($_GET['id']);	
		getUserInfo($id);
		$arr = array();
		$arr[] = $id;
		$time = time();
		if($sm['user']['last_access'] < $time){
			$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");	
		}	
		$query2 = $mysqli->query("SELECT id,s_id,r_id,seen,notification,gif,gift,time FROM chat WHERE r_id = '".$id."' || s_id = '".$id."' order by id desc");
		if ($query2->num_rows > 0) { 
			while($result2 = $query2->fetch_object()){
				if (!in_array($result2->s_id, $arr)){
					$arr[] = $result2->s_id;			  
					getUserInfo($result2->s_id,1);
					if($result2->r_id == $id && $result2->seen == 2){				
					} else {
						$gift = 0;
						$last_m = getLastMessageMobileApp($sm['user']['id'],$sm['profile']['id']);
						if (strpos($last_m, '/gifts/') !== false) {
							$gift = 1;
						}
						if (strpos($last_m, 'giphy') !== false) {
							$gift = 1;
						}
						if (strpos($last_m, '.jpg') !== false) {
							$gift = 1;
						}
						if (strpos($last_m, '.png') !== false) {
							$gift = 1;
						}

						$last_m = secureEncode($last_m);
						$status = userFilterStatus($sm['profile']['id']);
						$unread = getLastMessageMobileSeenApp($sm['user']['id'],$sm['profile']['id']);		

						$storyFrom = $sm['plugins']['story']['days'];
						$time = time();	
						$extra = 86400 * $storyFrom;
						$storyFrom = $time - $extra;
						$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';
						$arr['matches'][] = array(
							  "id" => $sm['profile']['id'],
							  "name" => $sm['profile']['name'],
							  "firstName" => $sm['profile']['first_name'],					  
							  "age" => $sm['profile']['age'],
							  "city" => $sm['profile']['city'],				  	  
							  "photo" => profilePhoto($sm['profile']['id']),
							  "error" => 0,
							  "last_a" => $sm['profile']['last_access'],
							  "status" => userFilterStatus($sm['profile']['id']),
							  "online" => $status,
							  "unread" => $unread,						  						  
							  "premium" => $sm['profile']['premium'],
							  "story" => selectC('users_story',$storiesFilter),
						  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),							  
							  "unreadCount" => checkUnreadMessagesCount($sm['user']['id'],$sm['profile']['id']),
							  "last_m" => $last_m,
							  "last_m_time" => getLastMessageMobileTime($sm['user']['id'],$sm['profile']['id']),
							  "credits" => $sm['profile']['credits'],
							  "last_m_t" => get_time_difference_php($result2->time),
							  "check_m" => getLastMessageMobileSeenApp($sm['user']['id'],$sm['profile']['id']),
							  "gift" => $gift
						);
					}				  
				}
				if (!in_array($result2->r_id, $arr)){
					$arr[] = $result2->r_id;			  
					getUserInfo($result2->r_id,1);
					if($result2->s_id == $id && $result2->notification == 2){				
					} else {
						$gift = 0;
						$last_m = getLastMessageMobileApp($sm['user']['id'],$sm['profile']['id']);
						if (strpos($last_m, '/gifts/') !== false) {
							$gift = 1;
						}
						$status = userFilterStatus($sm['profile']['id']);
						$unread = getLastMessageMobileSeenApp($sm['user']['id'],$sm['profile']['id']);
						$last_m = secureEncode($last_m);

						$storyFrom = $sm['plugins']['story']['days'];
						$time = time();	
						$extra = 86400 * $storyFrom;
						$storyFrom = $time - $extra;
						$storiesFilter = 'where uid = '.$sm['profile']['id'].' and storyTime >'.$storyFrom.' and deleted = 0';		
						$arr['matches'][] = array(
							  "id" => $sm['profile']['id'],
							  "name" => $sm['profile']['name'],
							  "firstName" => $sm['profile']['first_name'],					  
							  "age" => $sm['profile']['age'],
							  "city" => $sm['profile']['city'],				  	  
							  "photo" => profilePhoto($sm['profile']['id']),
							  "error" => 0,
							  "last_a" => $sm['profile']['last_access'],
							  "status" => userFilterStatus($sm['profile']['id']),
							  "online" => $status,
							  "unread" => $unread,	
							  "story" => selectC('users_story',$storiesFilter),
						  	  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),
							  "premium" => $sm['profile']['premium'],
							  "last_m" => $last_m,
							  "last_m_t" => get_time_difference_php($result2->time),
							  "last_m_time" => getLastMessageMobileTime($sm['user']['id'],$sm['profile']['id']),
							  "credits" => $sm['profile']['credits'],
							  "check_m" => getLastMessageMobileSeenApp($sm['user']['id'],$sm['profile']['id']),
							  "gift" => $gift
						);
					}				  
				}			
			}	
		}
		echo json_encode($arr);
		break;	
		case 'like':
			$uid1 = secureEncode($_GET['uid1']);
			$uid2 = secureEncode($_GET['uid2']);
			$action = secureEncode($_GET['uid3']);		
			$time = time();
			$mysqli->query("UPDATE users_likes SET love = '$action' where u1 = '$uid1' and u2 = '$uid2'");		
			$mysqli->query("INSERT INTO users_likes (u1,u2,love,time) VALUES ('$uid1','$uid2','$action','$time')");
			$sm['profile_notifications'] = userNotifications($uid2);
			if($action == 1){
				if($sm['profile_notifications']['fan']['email'] == 1){
					if($sm['plugins']['email']['enabled'] == 'Yes'){
						fanMailNotification($uid2);
					}
				}
				if(isFanApp($uid2,$uid1) == 1 && $sm['profile_notifications']['match_me']['email'] == 1){
					if($sm['plugins']['email']['enabled'] == 'Yes'){
						matchMailNotification($uid2);
					}														   
				}		
			}
		break;
		case 'userInteractions':
			$uid = secureEncode($_GET['id']);		
			$time = time();
			$arr = array();
			$arr['visitsCount'] = '';
			$arr['chatCount'] = checkUnreadMessages($uid);
			echo json_encode($arr);
		break;	

		case 'checkHasStory':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$id = $data[0];		
			$arr = array();
			$storyFrom = $sm['plugins']['story']['days'];
			$time = time();	
			$extra = 86400 * $storyFrom;
			$storyFrom = $time - $extra;
			$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0';
			$arr['story'] = selectC('users_story',$storiesFilter);
			echo json_encode($arr);
		break;				

		case 'today':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$time = time();
			$date = date('m/d/Y', time());
			$mysqli->query("INSERT INTO users_chat (uid,date,count,last_chat) VALUES ('".$uid."','".$date."',1,'".$time."') 
							ON DUPLICATE KEY UPDATE count=count+1");	
		break;	

		case 'checkUsername':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$username = $data[0];
			$email = $data[1];	

			$arr = array();

			$checkUsername = checkIfExist('users','username',$username);
			$checkEmail = checkIfExist('users','email',$email);

			$arr['validUsername'] = 'Yes';
			$arr['validEmail'] = 'Yes';
			if($checkUsername == 1){
				$arr['validUsername'] = 'No';
				$arr['validUsernameMsg'] = $sm['lang'][650]['text'];					
			}

			if($checkEmail == 1){
				$arr['validEmail'] = 'No';	
				$arr['validEmailMsg'] = $sm['lang'][651]['text'];				
			}			

			if(validate_username($username) == 0){
				$arr['validUsername'] = 'No';
				$arr['validUsernameMsg'] = $sm['lang'][812]['text'];			
			}

			if(substr($username, -1) == '.'){
				$arr['validUsername'] = 'No';
				$arr['validUsernameMsg'] = $sm['lang'][812]['text'];					
			}

			echo json_encode($arr);
		break;

		case 'riseUp':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$credits = $data[1];
			$arr = array();
			$date = date('m/d/Y', time());
			$time = time();	
			$extra = 86400 * 5;
			$riseUp = $time + $extra;	
			$mysqli->query("UPDATE users set last_access = '".$riseUp."', meet = 1 where id = '".$uid."'");
			$query2 = "UPDATE users SET credits = credits-'".$credits."' WHERE id= '".$uid."'";
			$mysqli->query($query2);
			getUserInfo($uid);
			$arr['user'] = $sm['user'];
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);	
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);
		break;
		case 'discover100':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$credits = $data[1];
			$arr = array();
			$date = date('m/d/Y', time());
			$time = time();	
			$extra = 86400 * 5;
			$riseUp = $time + $extra;	
			$mysqli->query("UPDATE users set last_access = '".$riseUp."', discover = 100 where id = '".$uid."'");
			$query2 = "UPDATE users SET credits = credits-'".$credits."' WHERE id= '".$uid."'";
			$mysqli->query($query2);	
			getUserInfo($uid);
			$arr['user'] = $sm['user'];
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);	
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);
		break;	
		case 'chat_limit':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$credits = $data[1];
			$arr = array();
			$date = date('m/d/Y', time());
			$mysqli->query("DELETE FROM users_chat WHERE uid = '".$uid."' AND date = '".$date."'");	
			$mysqli->query("UPDATE users set credits = credits-'".$credits."' where id = '".$uid."'");
			getUserInfo($uid);
			$arr['user'] = $sm['user'];
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);	
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);
		break;
		case 'add_interest':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$u_id = $data[0];
			$i_id = $data[1];		
			$mysqli->query("INSERT INTO users_interest (i_id,u_id) VALUES ('".$i_id."','".$u_id."')");
		break;
		case 'del_interest':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$u_id = $data[0];
			$i_id = $data[1];	
			$mysqli->query("DELETE FROM users_interest where u_id = '".$u_id."' and i_id = '".$i_id."'");		
		break;		
		case 'slike':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$credits = $data[1];
			$slike = $data[2];		
			$arr = array();
			$mysqli->query("UPDATE users set credits = credits-'".$credits."', sexy = sexy+10 where id = '".$uid."'");
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);
		break;	

		case 'sendMessage':
			$query = secureEncode($_GET['query']);
			$data = explode('[message]',$query);
			$s_id = $data[0];
			$r_id = $data[1];
			$type = $data[3];

			if($type != 'videocall'){
				$message = secureEncode($data[2]);	
			} else {
				$message = $data[2];
			}
			
			$time = time();
			if(isset($data[4])){	
				if($data[4] == 'fast'){
					$date = date('m/d/Y', time());
					$mysqli->query("INSERT INTO users_chat (uid,date,count,last_chat) VALUES ('".$s_id."','".$date."',1,'".$time."') ON DUPLICATE KEY UPDATE count=count+1");	
				}
			}

			$storyId = 0;
			$fake = getData('users','fake','where id ='.$r_id);
			$online_day = getData('users','online_day','where id ='.$r_id);

			$fake_sender = getData('users','fake','where id ='.$s_id);

			if($fake_sender == 1){
				$mysqli->query("UPDATE users SET last_access = '".$time."' WHERE id = '".$s_id."'");
			}

			if($type == 'gift'){
				$message = $data[2];
				$price = $data[4];
				$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$s_id."'";
				$mysqli->query($query2);

				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day,gift,credits) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."',1,'".$price."')");										
			}

			if($type == 'image'){
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day,photo) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."',1)");
			}

			if($type == 'video'){
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day,photo) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."',2)");
			}			

			if($type == 'gif'){
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day,gif) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."',1)");
			}

			if($type == 'story'){
	            if(isset($data[4])){
	                $storyId = $data[4];
	                $credits = $data[5];
	            }				
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day,story,credits) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."','".$storyId."','".$credits."')");
			}			

			if($type == 'credits'){
				$message = '<b>'.$sm['lang'][583]['text'].' '.$data[4].' '.$sm['lang'][128]['text'].'!</b>
				<img src="'.$sm['config']['theme_url'].'/images/icon-coins.png" style="width:34px;">';			
				//check if not same ip
				$s_id_ip = getData('users','ip','WHERE id = '.$s_id);
				$r_id_ip = getData('users','ip','WHERE id = '.$r_id);
				if($r_id_ip != $s_id_ip){
					$mysqli->query("UPDATE users set credits = credits+'".$data[4]."' where id = '".$r_id."'");
					$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day,credits) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."','".$data[4]."')");
				}
				$mysqli->query("UPDATE users set credits = credits-'".$data[4]."' where id = '".$s_id."'");
													
			}

			if($type == 'text' || $type == 'videocall'){
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."')");		
			}			

			$reciverLastAccess = getData('users','last_access','where id ='.$r_id);

			if($fake == 0){
				$sm['profile_notifications'] = userNotifications($r_id);

				if($reciverLastAccess+300 >= time() && $sm['profile_notifications']['message']['email'] == 1){
					if($sm['plugins']['email']['enabled'] == 'Yes'){
						chatMailNotification($r_id,$message,$s_id);
					}
				} 
				if($sm['profile_notifications']['message']['push'] == 1){				
					//push notification
					$reciverApp = getData('users','app_id','where id ='.$r_id);		
					if($reciverApp != 0){
						$senderPhoto = profilePhoto($s_id);
						$senderName = getData('users','name','where id ='.$s_id);			
						pushNotification($reciverApp,$senderName,cleanMessage($message),$senderPhoto);	
					}
				}	
			}

			if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 

				$senderName = getData('users','name','where id ='.$s_id);
				$recieverName = getData('users','name','where id ='.$r_id);
				$activityTitle = $senderName.' message to '.$recieverName;

				$ac = array();
				$ac['u1']['id'] = $s_id;
				$ac['u2']['id'] = $r_id;
				$ac['u1']['name'] = $senderName; 
				$ac['u2']['name'] = $recieverName; 	
				$ac['u1']['photo'] = profilePhoto($s_id); 
				$ac['u2']['photo'] = profilePhoto($r_id); 
				$ac['message'] = cleanMessage($message);

				$adminPush= 'adminActivity';
				$pushData['message'] = $ac;	
				$ac = json_encode($ac);
				activity('message',$ac,$activityTitle,$s_id);				
			}				
							
		break;	

		case 'fixUserCredits':
			$id = secureEncode($_GET['id']);

			$check = getData('users','credits','where id = '.$id);
			if($check < 0){
				$mysqli->query("UPDATE users set credits = 0 where id = '".$id."'");		
			}
		break;

		case 'userChat':
			$uid1 = secureEncode($_GET['uid1']);
			$uid2 = secureEncode($_GET['uid2']);
			getUserInfo($uid1);
			$arr = array();
			$timestamp = '';
			$time = time();
			if($sm['user']['last_access'] < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");		
			}		
			$count = getUserTodayConv($uid1);	
			$new = getUserTotalConv($uid1,$uid2);
			$check = blockedUser($uid1,$uid2);
			if($check == 1){
				$arr['blocked'] = 1;
			} else {
				$arr['blocked'] = 0;
			}		
			if($new == 0 && $count >= $sm['basic']['chat'] && $sm['user']['premium'] == 0){
				$arr['premium'] = 1;
			} else if($new == 0 && $count >= $sm['premium']['chat'] && $sm['user']['premium'] == 1){
				$arr['premium'] = 1;
			} else {
				$arr['premium'] = 0;
			}
			$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$uid2."' and r_id = '".$uid1."'");	
			$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."'
										OR r_id = '".$uid1."' and s_id = '".$uid2."' ORDER BY id ASC");
			if ($spotlight->num_rows > 0) { 
				while($spotl = $spotlight->fetch_object()){					
					$message = $spotl->message;
					$continue = true;
					$time = $spotl->time;
					$stamp = date("M d Y", $time);		
					if($stamp != $timestamp){
						$timestamp = $stamp;
					} else {
						$timestamp = '';
					}
					$type = 'text';
					if($spotl->photo == 1){
						$type = 'image';
					}
					if($spotl->story > 0){
						$type = 'story';
					}
					
					if($spotl->gif == 1 ){
						$type = 'image';
					}
					if($spotl->gift == 1 ){
						$type = 'image';		
					} 

					if($continue == true){
						if($uid1 == $spotl->s_id) {
							$me = true;
							$p = $spotl->r_id;
						}else {
							$me = false;
							$p = $spotl->s_id;
						}
					}

					$storyData = [];

					if($spotl->story > 0){
						$filter = 'id = '.$spotl->story;
						$storyData = getDataArray('users_story',$filter);
					}
					$arr['chat'][] = array(
						  "isMe" => $me,
						  "id" => $spotl->id,
						  "seen" => $spotl->seen,					  
						  "type" => $type,
						  "body" => $message,
						  "story" => $spotl->story,	
						  "storyData" => $storyData,				  
						  "avatar" => profilePhoto($p),
						  "gif" => $spotl->gif,
						  "gift" => $spotl->gift,
						  "photo" => $spotl->photo,
						  "timestamp" => $timestamp
					);				
				}	
			}
			echo json_encode($arr);	
		break;
		case 'userCChat':
			$uid1 = secureEncode($_GET['uid1']);
			$uid2 = secureEncode($_GET['uid2']);		
			$arr = array();
			$time = time()-3;
			getUserInfo($uid1);
			if($sm['user']['last_access'] < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");		
			}		
			$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."'  and seen = 0 ORDER BY id ASC");
			$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$uid2."' and r_id = '".$uid1."'");
			if ($spotlight->num_rows > 0) { 
				while($spotl = $spotlight->fetch_object()){					
					$message = $spotl->message;
					$continue = true;
					$time = $spotl->time;
					$stamp = date("M d Y", $time);		
					if($stamp != $timestamp){
						$timestamp = $stamp;
					} else {
						$timestamp = '';
					}
					$type = 'text';
					if($spotl->photo == 1){
						$type = 'image';
					}
					if($spotl->access == 1){
					}			
					if($spotl->seen == 1){
					} else {
					}
					if($continue == true){
						if($uid1 == $spotl->s_id) {
							$me = true;
							$p = $spotl->r_id;
						}else {
							$me = false;
							$p = $spotl->s_id;
						}
					}
					$arr['chat'][] = array(
						  "isMe" => $me,
						  "id" => $spotl->id,
						  "seen" => $spotl->seen,					  
						  "type" => $type,
						  "body" => $message,					  
						  "avatar" => profilePhoto($p),
						  "timestamp" => $timestamp
					);				
				}	
			}
			echo json_encode($arr);	
		break;	



		case 'game_like':
			$uid1 = secureEncode($_GET['uid1']);
			$time = time();
			$last_access = getData('users','last_access','where id = '.$uid1);
			if($last_access < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$uid1."'");	
			}

			$id = secureEncode($_GET['uid2']);
			$action = secureEncode($_GET['uid3']);	

			//old time
			$oldTime = getData('users_likes','time','where u1 = '.$uid1.' and u2 = '.$id);

			if($action == 3){ //este es el super like
				$time = time() + 288000;
				$mysqli->query("UPDATE users set sexy = sexy-1 where id = '".$uid1."'");	
				$mysqli->query("INSERT INTO users_likes (u1,u2,love,time) VALUES ('".$uid1."','".$id."','".$action."','".$time."') ON DUPLICATE KEY update love = '".$action."',time = '".$time."'");		
			} else { //like/unlike normal
				$time = time();
				$mysqli->query("INSERT INTO users_likes (u1,u2,love,time) VALUES ('".$uid1."','".$id."','".$action."','".$time."') ON DUPLICATE KEY update love = '".$action."',time = '".$time."'");
			
			}
			
			$timeout = $sm['plugins']['fakeUsersInteractions']['notificationTimeout']*60;
			if($oldTime != 'noData'){
				$checkTime = $oldTime + $timeout; //check 10 min
				if($checkTime > $time){
					$action = 0; //DONT SEND NOTIFICATION
				}
			}
			if($action == 1){

				$name = getData('users','name','where id ='.$uid1);
				$photo = profilePhoto($uid1);
				$appId = getData('users','app_id','where id ='.$id);
				$nameUser = getData('users','name','where id ='.$id);
				$userPremium = getData('users','premium','where id ='.$id);
				$fake = getData('users','fake','where id ='.$id);
				
		        $noti= 'like'.$id;
		        $data['id'] = $uid1;
		        $data['message'] = $sm['alang'][253]['text'];
		        $data['time'] = date("H:i", time());
		        $data['type'] = 4;
		        $data['icon'] = $photo;
		        $data['name'] = $name;      
		        $data['photo'] = 0;
		        $data['unread'] = checkUnreadMessages($id);				
		        $data['action'] = 'like';

		        if($fake == 0){
			        $sm['profile_notifications'] = userNotifications($id);

			        if($sm['profile_notifications']['fan']['inapp'] == 1){
			        	if(is_numeric($sm['plugins']['pusher']['id'])){ 
							$sm['push']->trigger($sm['plugins']['pusher']['key'], $noti, $data);
						}
					}
					
					if($sm['profile_notifications']['fan']['email'] == 1){
						if($sm['plugins']['email']['enabled'] == 'Yes'){
							fanMailNotification($id);
						}
					}

					if($sm['profile_notifications']['fan']['push'] == 1){				
						//push notification
						if($userPremium == 0){
							if($appId != 0 && $sm['profile_notifications']['match_me']['push'] == 1){
								pushNotification($appId,$sm['alang'][254]['text'],$sm['alang'][255]['text'] ,'');	
							}
						} else {
							if($appId != 0 && $sm['profile_notifications']['match_me']['push'] == 1){
								pushNotification($sm['profile']['app'],$sm['user']['first_name'],$sm['alang'][256]['text'].' '.$name.' '.$sm['alang'][257]['text'] ,$photo);
							}
						}
					}

					if(isFanApp($id,$uid1) == 1 && $sm['profile_notifications']['match_me']['email'] == 1){
						if($sm['plugins']['email']['enabled'] == 'Yes'){
							matchMailNotification($id);
						}
						if($appId != 0 && $sm['profile_notifications']['match_me']['push'] == 1){
							pushNotification($appId,$name,$sm['alang'][142]['text'] ,$photo);
						}														   
					}
		        }

				//activity content
				if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
					$ac = array();
					$ac['u1']['id'] = $uid1;
					$ac['u2']['id'] = $id;
					$ac['u1']['name'] = $name; 
					$ac['u2']['name'] = $nameUser; 	
					$ac['u1']['photo'] = $photo; 
					$ac['u2']['photo'] = profilePhoto($id); 

					$adminPush= 'adminActivity';
					$pushData['like'] = $ac;	
					$ac = json_encode($ac);
					activity('like',$ac,'Profile like '.$nameUser,$uid1);
				}		        
			}			
		break;	

		case 'game':
			$id = secureEncode($_GET['id']);
			getUserInfo($id);
			$e_age = explode( ',', $sm['user']['s_age'] );
			$age1 = $e_age[0];
			$age2 = $e_age[1];
			$time = time();
			if($sm['user']['last_access'] < $time){
				$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");		
			}
			$gender = $sm['user']['s_gender'];
			$all = count($sm['genders']);
			$all = $all + 1;
			if($gender == $all){
				$u_total = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'				  
						  ORDER BY distance ASC, last_access DESC");
			}else{
				$u_total = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'
						  AND gender = '".$sm['user']['s_gender']."'					  
						  ORDER BY distance ASC, last_access DESC");			
			}
			$array1  = array();
			if ($u_total->num_rows > 0) { 
				while($u_t= $u_total->fetch_object()){
					$a = profilePhoto($u_t->id);
					if (strpos($a, 'themes') !== false) {
					} else {
						$array1[] = $u_t->id;
					}					
				}
			}
			$array1  = array_diff($array1, array($sm['user']['id']));		
			$u_total2 = $mysqli->query("SELECT u2 FROM users_likes where u1 = '".$id."'");
			$array2  = array();
			if ($u_total2->num_rows > 0) {
				while($u_t2 = $u_total2->fetch_object()) {
					$array2[] = $u_t2->u2;						
				}
			}

			array_push($array2,$id);
			$resultado2 = array_diff($array1, $array2);
			$resultado = array_slice($resultado2, 0, 30);
			$i=0;
			$info = array();
			$max = count($resultado);
			if(count($resultado) == 0){


				$info['game'] = 'error';


			} else {
				foreach($resultado as $user_g){
					$user_game = $mysqli->query("SELECT id FROM users WHERE id = '".$user_g."'");
					$sexy_game = $user_game->fetch_object();
						
					getUserInfo($sexy_game->id,1);

					$storyFrom = $sm['plugins']['story']['days'];
					$time = time();	
					$extra = 86400 * $storyFrom;
					$storyFrom = $time - $extra;
					$storiesFilter = 'where uid = '.$sexy_game->id.' and storyTime > '.$storyFrom.' and deleted = 0 and review = "No"';						
					$info['game'][] = array(
						  "id" => $sexy_game->id,
						  "name" => $sm['profile']['name'],
						  "status" => userFilterStatus($sexy_game->id),
						  "distance" => '',				  
						  "age" => $sm['profile']['age'],
						  "city" => $sm['profile']['city'],
						  "bio" => $sm['profile']['bio'],	
						  "isFan" => isFanApp($sexy_game->id,$sm['user']['id']),
						  "total" => getUserTotalLikers($sexy_game->id),
						  "photo" => profilePhoto($sexy_game->id),
						  "discoverPhoto" => profilePhoto($sexy_game->id,1),
						  "photos" => getUserPhotosAll($sexy_game->id,'discover'),
						  "full" => $sm['profile'],
						  "story" => selectC('users_story',$storiesFilter),
						  "stories" => json_encode(getUserStories($sm['profile']['name'],$sm['profile']['profile_photo'],$storiesFilter,'storyTime ASC')),	  
						  "error" => 0
					);
					
				}			
			}
			echo json_encode($info);
		break;

		case 'viewStory':
			$arr = array();
			$id = secureEncode($_GET['uid']);
			$storyFrom = $sm['plugins']['story']['days'];
			$time = time();	
			$extra = 86400 * $storyFrom;
			$storyFrom = $time - $extra;
			$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0';      	
			$name = getData('users','name','where id ='.$id);
			$first_name = explode(' ',trim($name));	
			$first_name = explode('_',trim($first_name[0]));		
			$arr['stories'] = getUserStories($first_name,profilePhoto($id),$storiesFilter,'storyTime ASC');
			echo json_encode($arr);     	  
		break;	

		case 'viewStories':
			$stories = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$lat = $data[0];	
			$lng = $data[1];
			$looking = $data[2];

			$stories = discoverStoriesMobile($lat,$lng,$looking);
			echo $stories;     	  
		break;	

		case 'updateSRadius':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$radius = $data[1];
			$mysqli->query("UPDATE users set s_radious = '".$radius."' where id = '".$uid."'");
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;

		case 'updateGender':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$gender = $data[1];
			
			$mysqli->query("UPDATE users set looking = '".$gender."',s_gender = '".$gender."' where id = '".$uid."'");

			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;	

		case 'updateUserLanguage':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$l = $data[1];
			$mysqli->query("UPDATE users set lang = '".$l."' where id = '".$uid."'");
		break;

		case 'updateVisitorLanguage':
			$l = secureEncode($_GET['query']);
			$_SESSION['lang'] = $l;
		break;			

		case 'updateUserBio':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode('[divider]',$query);
			$uid = $data[0];	
			$bio = $data[1];
			$bio = strip_tags($bio);
			$bioUrl = $data[2];
			$bUrl = explode('**message**',$bioUrl);
			
			$postUrl = $bUrl[0];
			$parsed = parse_url($postUrl);
			if (empty($parsed['scheme'])) {
			    $bUrl[0] = 'http://' . ltrim($postUrl, '/');
			}

			$bUrl[0] = checkUrlBar($bUrl[0]);	

			$validUrl = validateURL($bUrl[0]);
			if($validUrl){
				
			} else {
				
			}

			$arr['urlMessage'] = 'No';
			if($validUrl == false){
				$bUrl[0] = '';
				$bUrl[1] = '';
				$arr['url'] = 'No';
			} else {
				$arr['url'] = $bUrl[0];
				if(!empty($bUrl[1])){				
					$arr['urlMessage'] = $bUrl[1];
				} else {
					$arr['urlMessage'] = $bUrl[0];
				}
			}
			$bioUrl = implode('**message**',$bUrl);

			$mysqli->query("UPDATE users set bio = '".$bio."',bio_url = '".$bioUrl."' where id = '".$uid."'");
			echo json_encode($arr);
		break;			

		case 'updateUserGender':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$gender = $data[1];
			$mysqli->query("UPDATE users set gender = '".$gender."' where id = '".$uid."'");
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;

		case 'deletePhoto':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$pid = $data[1];
			$mysqli->query("DELETE FROM users_photos where id = '".$pid."'");		
			getUserInfo($uid);
			$arr['user'] = $sm['user'];				
			$arr['user']['photos'] = userAppPhotos($uid);			
			echo json_encode($arr);	
		break;

		case 'deleteStoryAlbum':
			$arr = array();
			$album = secureEncode($_GET['query']);
			$mysqli->query("DELETE FROM users_story_albums where id = '".$album."'");	
			echo json_encode($arr);	
		break;	

		case 'updateStoryAlbum':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(';',$query);
			$album = $data[0];
			$stories = explode(',',$data[1]);
			$name = $data[2];
			$photo = $data[3];
			$st = '';
      		for ($i=0;$i < sizeof($stories);$i++){
      			if($i+1 == sizeof($stories)){
      				$st.= $stories[$i];
      			} else {
      				$st.= $stories[$i].',';
      			}
      		} 			
			$mysqli->query("UPDATE users_story_albums SET stories = '".$st."' where id = '".$album."'");
			$result= json_encode(getAlbumStories($album,$name,$photo));
			$result= "<script>stories".$album." = ".$result.";</script>";			
			echo $result;	
		break;				

		case 'updateUserProfilePhoto':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$pid = $data[1];
			if($uid != 1409073756){
			$mysqli->query("UPDATE users_photos set profile = 0 where u_id = '".$uid."'");
			$mysqli->query("UPDATE users_photos set profile = 1 where u_id = '".$uid."' and id = '".$pid."'");
			}
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;
		case 'updateUser':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$val = $data[1];
			$col = $data[2];
			$mysqli->query("UPDATE users set $col = '".$val."' where id = '".$uid."'");
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;
		case 'updateUserExtended':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];
			$qid = $data[1];	
			$a = $data[2];
			$mysqli->query("INSERT INTO users_profile_questions (uid,qid,answer)
			VALUES ('".$uid."','".$qid."','".$a."') ON DUPLICATE KEY UPDATE answer = '".$a."'");	
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;	
		case 'updateAge':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$sage = $data[1];
			$sage2 = $data[2];
			$lol = $sage.','.$sage2.',1';
			$mysqli->query("UPDATE users set s_age = '".$lol."' where id = '".$uid."'");
		break;		
		case 'updateLocation':
			$arr = array();
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$lat = $data[1];
			$lng = $data[2];
			$city = $data[3];
			$country = $data[4];
			$mysqli->query("UPDATE users set lat = '".$lat."',lng = '".$lng."',city = '".$city."',country = '".$country."' where id = '".$uid."'");
			getUserInfo($uid);
			$arr['user'] = $sm['user'];	
			$arr['user']['slike'] = getUserSuperLikes($sm['user']['id']);
			$age = $sm['user']['s_age'];
			$e_age = explode( ',', $age );		
			$arr['user']['sage'] = $e_age[1];	
			$arr['user']['photos'] = userAppPhotos($sm['user']['id']);
			$arr['user']['notification'] = userNotifications($sm['user']['id']);
			echo json_encode($arr);		
		break;	
		case 'updatePeer':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$peer = $data[1];
			$mysqli->query("UPDATE users_videocall set peer_id = '".$peer."',status=1 where u_id = '".$uid."'");
		break;
		case 'updateNotification':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid = $data[0];	
			$col = $data[1];
			$val = $data[2];

			$val = '1,1,'.$val;
			$mysqli->query("UPDATE users_notifications set $col = '".$val."' where uid = '".$uid."'");
		break;	
		case 'check':
			$id = secureEncode($_POST['id']);
			echo isFan($id,$sm['user']['id']);
		break;
		case 'income':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$peer = $data[0];	
			$peerid = getIdPeer($peer);
			getUserInfo($peerid,5);
			$info = array(
				  "name" => $sm['videocall']['name'],
				  "id" => $sm['videocall']['id'],	  
				  "peer" => $peerid,	  
				  "photo" => profilePhoto($sm['videocall']['id']), 
			);	
			echo json_encode($info);
		break;
		case 'invideocall':
			$mysqli->query("UPDATE users_videocall set status=2 where u_id = '".$uid."'");
		break;
		case 'log':
			$min = secureEncode($_POST['min']);
			$sec = secureEncode($_POST['sec']);		
			$user = secureEncode($_POST['user']);
			$time = $min.":".$sec;
			$date = date("Y-m-d H:i:s", time());
			$mysqli->query("INSERT INTO videocall (c_id,r_id,time,date) VALUES ('".$uid."','".$user."','".$time."','".$date."')");
		break;	
		case 'recover':	
			$arr = array();
			$arr['error'] = 0;
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$email = $data[0];	
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][181]['text'];	
				echo json_encode($arr);
				exit;		
			}		
			if($email == "" || $email == NULL ){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][182]['text'];	
				echo json_encode($arr);
				exit;	
			}			
			$email_check = $mysqli->query("SELECT email,id,name FROM users WHERE email = '".$email."'");	
			if($email_check->num_rows == 0 ){
				$arr['error'] = 1;
				$arr['error_m'] = $sm['lang'][183]['text'];	
				echo json_encode($arr);
				exit;	
			} else {
				$user = $email_check->fetch_object();
				$time = time();
				$code = md5($time);
				$mysqli->query("INSERT INTO emails (type,uid,code) VALUES (1,'".$user->id."', '".$code."')");			
				$msg = " ".$sm['lang'][177]['text']." ".$user->name." ".$sm['lang'][178]['text']."<br><br><a href='".$sm['config']['site_url']."/index.php?page=recover&code=".$code."&id=".$user->id."'>".$sm['lang'][179]['text']."</a>";
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <'.$sm['config']['email'].'>' . "\r\n";			
				$subject = $sm['config']['name'].' - '.$sm['lang'][180]['text'];
				mail($email,$subject,$msg,$headers);
				echo json_encode($arr);				
			}
		break;	
		case 'getpeerid':
			$peer = secureEncode($_GET['query']);
			$peerid = getPeerId($peer);
			$status = getVideocallStatus($peer);
			getUserInfo($peer,5);	
			if ($sm['videocall']['last_access']+300 >= time() && $status == 1) {
				$status = 1;
			}					
			$info = array(
				  "name" => $sm['videocall']['name'],
				  "id" => $sm['videocall']['id'],	  
				  "peer" => $peerid,	
				  "status" => $status,		  
				  "photo" => profilePhoto($sm['videocall']['id']), 
			);	
			echo json_encode($info);
		break;
		case 'fortumo':
			$arr = array();	
			$encode = secureEncode($_GET['encode']);	
			$secret = $sm['plugins']['fortumo']['secret'];
			$result = md5($encode.$secret);
			$arr['encode'] = $result;
			echo json_encode($arr);
		break;	

		case 'unblockUser':
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$uid1 = $data[0];
			$uid2 = $data[1];
			$query = "DELETE FROM users_blocks WHERE uid1 = '".$uid1."' AND uid2 = '".$uid2."'";
			$mysqli->query($query);			
		break;					
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	switch ($_POST['action']) {

		case 'withdraw':
			$p = secureEncode($_POST['paypal']);
			$c = secureEncode($_POST['credits']);
			$m = secureEncode($_POST['money']);
			if(isset($_POST['uid'])) {		
				$uid = secureEncode($_POST['uid']);
			}
			$t = date('m/d/Y', time());

			$checkCredits = getData('users','credits','WHERE id = '.$uid);
			error_log($checkCredits);
			if($c > $checkCredits+200){
				exit;
			}
			$mysqli->query("UPDATE users set credits = credits-'".$c."',paypal = '".$p."' where id = '".$uid."'");
			$mysqli->query("INSERT INTO users_withdraw (u_id,withdraw_date,withdraw_amount) 
				VALUES ('".$uid."','".$t."','".$m."')");	   
		break;

		case 'loginFB':
			$email = secureEncode($_POST['email']);
        	$arr = array();
        	$arr['valid'] = 'No';
        	$checkEmail = getData('users','id','WHERE email = "'.$email.'"');
        	if($checkEmail != 'noData'){
        		$arr['valid'] = 'Yes';
        		$_SESSION['user'] = $checkEmail;
        	}
        	echo json_encode($arr);	
		break;

		case 'p_access':
			$id = secureEncode($_POST['id']);
			$uid = secureEncode($_POST['uid']);
			$c = secureEncode($_POST['credits']);
			$query = "INSERT INTO blocked_photos (u1,u2) VALUES ('".$uid."', '".$id."')";
			$mysqli->query($query);	
			$mysqli->query("UPDATE users set credits = credits-'".$c."' where id = '".$uid."'");		
		break;	

		case 'manage':
			$uid = secureEncode($_POST['uid']);
			$photo = secureEncode($_POST['pid']);
			$profile = secureEncode($_POST['profile']);
			$block = secureEncode($_POST['block']);
			$unblock = secureEncode($_POST['unblock']);
			$story = secureEncode($_POST['story']);
			$del = secureEncode($_POST['del']);
			if($profile == 1) {  
				$query = "UPDATE users_photos set profile = 0 where u_id = '$uid'";	
				$mysqli->query($query);
				$query2 = "UPDATE users_photos set profile = 1,blocked = 0,private = 0 where id = '$photo'";	
				$mysqli->query($query2);			
			}
			if($block == 1) {
				$query = "UPDATE users_photos set blocked = 1,private = 1  where id = '$photo'";	
				$mysqli->query($query);				
			}
			if($unblock == 1) {
				$query = "UPDATE users_photos set blocked = 0,private = 0 where id = '$photo'";	
				$mysqli->query($query);				
			}
			if($del == 1) {

				$getSrc = getData('users_photos','photo','where id = '.$photo);
				$checkStory = getData('users_story','id','where story = "'.$getSrc.'"');
				if($checkStory != 'noData'){
					$mysqli->query('DELETE FROM users_story WHERE id = '.$checkStory);
					$mysqli->query('DELETE FROM users_photos WHERE photo = "'.$getSrc.'" AND story > 0');
				}
				$query = "UPDATE users_photos set approved = 2 where id = '$photo'";	
				$mysqli->query($query);				
			}

			if($story == 1){
				$time = time();
				$filter = 'id = '.$photo;
				$arr['data'] = getDataArray('users_photos',$filter);
				$type = 'image';
				$video = 0;
				if($arr['data']['video'] == 1){
					$type = 'video';
					$video = 1;
				}
				$lat = getData('users','lat','WHERE id = '.$uid);
				$lng = getData('users','lng','WHERE id = '.$uid);

				if($sm['plugins']['story']['reviewStory'] == 'No'){
					$approved = 1;
				} else {
					$approved = 0;
				}
          		$query = "INSERT INTO users_story (uid,storyTime,story,storyType,lat,lng,review)
          			 VALUES ('".$uid."','".$time."','".$arr['data']['photo']."','".$type."','".$lat."','".$lng."','".$sm['plugins']['story']['reviewStory']."')";
          		if ($mysqli->query($query) === TRUE) {
          			$last_id = $mysqli->insert_id;
					$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,story,approved)
          			 VALUES ('".$uid."','".$time."','".$arr['data']['photo']."','".$arr['data']['photo']."','".$video."',".$last_id.",".$approved.")");	
          		} 								
			}	
		break;		

	}

}

$mysqli->close();