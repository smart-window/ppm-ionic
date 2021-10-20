<?php
/* Belloo By Xohan - xohansosa@gmail.com - https://premiumdatingscript.com */
header('Content-Type: application/json');
require_once('../assets/includes/core.php');

if(!empty($sm['user']['id'])){
	$uid = $sm['user']['id'];	
}

$userApi = 'https://www.belloo.date/clients/users.php?';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	switch ($_POST['action']) {
		case 'fortumo':
			$encode = secureEncode($_POST['encode']);	
			$secret = siteConfig('fortumo_secret');
			$result = md5($encode.$secret);
			echo $result;
		break;
		case 'add_interest':
			$name = secureEncode($_POST['name']);
			$check = checkInterestExist($name);		
			$query = "INSERT INTO interest (name) VALUES ('".$name."') ON DUPLICATE KEY UPDATE count = count+1";		
			if ($mysqli->query($query) === TRUE) {
				$last_id = $mysqli->insert_id;		
				$mysqli->query("INSERT INTO users_interest (i_id,u_id,name) VALUES ('$last_id','$uid','$name')");
			}
			if($check >= 1){
				$i_id =	getIdInterest($name);
				$mysqli->query("INSERT INTO users_interest (i_id,u_id,name) VALUES ('$i_id','$uid','$name')");		
			}
		break;
		case 'del_interest':
			$id = secureEncode($_POST['id']);
			$mysqli->query("DELETE FROM users_interest where u_id = '".$uid."' and i_id = '".$id."'");		
		break;	
		case 'likephoto':
			$pid = secureEncode($_POST['pid']);
			$name = secureEncode($_POST['uname']);		
			$mysqli->query("INSERT INTO photos_likes (pid,uid,name) VALUES ('$pid','$uid','$name')");					   
		break;
		case 'photocomments':
			$pid = secureEncode($_POST['pid']);
			$comments = getPhotoCommentsAjax($pid);		
			echo $comments;
		break;
		case 'photocomment':
			$pid = secureEncode($_POST['pid']);
			$message = secureEncode($_POST['message']);		
			$mysqli->query("INSERT INTO photos (pid,cid,comment) VALUES ('$pid','$uid','$message')");
			$arr =array();
			$arr['comment'] = $message;
			echo json_encode($arr);		
		break;	
		case 'block':
			$id = secureEncode($_POST['id']);
			$query = "INSERT INTO users_blocks (uid1,uid2) VALUES ('".$uid."', '".$id."')";
			$mysqli->query($query);		
			$query2 = "DELETE FROM chat where s_id = '".$uid."' AND r_id = '".$id."' || r_id = '".$uid."' AND s_id = '".$id."'";
			$mysqli->query($query2);				
		break;	
		case 'report':
			$id = secureEncode($_POST['id']);
			$query = "INSERT INTO reports (reported,reported_by) VALUES ('".$id."', '".$uid."')";
			$mysqli->query($query);				
		break;		
		case 'photos':
			$id = secureEncode($_POST['id']);
			getUserInfo($id,1);	
			$sm['content'] = requestPage('profile/photos');
			echo $sm['content'];		
		break;

		case 'update':
			$name = secureEncode($_POST['name']);
			$username = secureEncode($_POST['username']);
			$email = secureEncode($_POST['email']);	
			$month = secureEncode($_POST['month']);
			$day = secureEncode($_POST['day']);
			$year = secureEncode($_POST['year']);			
			$birthday = date('F', mktime(0, 0, 0, $month, 10)).' '.secureEncode($_POST['day']).', '.secureEncode($_POST['year']);
			$age = date('Y') - $year;	
			$gender = secureEncode($_POST['gender']);
			$city = secureEncode($_POST['city']);
			$country = secureEncode($_POST['country']);
			$lat = secureEncode($_POST['lat']);
			$lng = secureEncode($_POST['lng']);
			$lang = secureEncode($_POST['lang']);
			$editEmail = secureEncode($_POST['editEmail']);
			$editUsername = secureEncode($_POST['editUsername']);
			$id = secureEncode($_POST['editId']);

			$adminEdit = '';
			if(isset($_POST['looking'])){
				$looking = secureEncode($_POST['looking']);
				$sage = secureEncode($_POST['age1']);
				$bio = secureEncode($_POST['bio']);
				$sage = $sage.','.secureEncode($_POST['age2']).',1';
				$adminEdit = ',s_gender = '.$looking.', s_age = "'.$sage.'", bio = "'.$bio.'"';
			}

			if($city == "" || $city == NULL){
				$city = $country;	
			}
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo 'Error'.$sm['lang'][181]['text'];	
				exit;	
			}		
			if($lat == "" || $lat == NULL){
				echo 'Error'.$sm['lang'][186]['text'];
				exit;	
			}	
			if($name == "" || $name == NULL || $email == "" || $email == NULL){
				echo 'Error'.$sm['lang'][182]['text'];
				exit;	
			}

			$checkUsername = checkIfExist('users','username',$username);
			$checkEmail = checkIfExist('users','email',$email);

			if($checkUsername == 1 && $username <> $editUsername){
				echo 'Error'.$sm['lang'][650]['text'];
				exit;					
			}

			if($checkEmail == 1 && $email <> $editEmail){
				echo 'Error'.$sm['lang'][651]['text'];
				exit;					
			}			

			if(validate_username($username) == 0){
				echo 'Error'.$sm['lang'][812]['text'];
				exit;				
			}

			if(substr($username, -1) == '.'){
				echo 'Error'.$sm['lang'][812]['text'];
				exit;					
			}
			$query = "UPDATE users SET name = '".$name."', email = '".$email."', age = '".$age."', birthday = '".$birthday."',gender = '".$gender."', city = '".$city."', country = '".$country."', lat = '".$lat."', lng = '".$lng."', lang = '".$lang."', username = '".$username."' $adminEdit
					  WHERE id = '".$id."'";
			$mysqli->query($query);
		
		break;	

		case 'register':	
			$name = secureEncode($_POST['name']);
			$email = secureEncode($_POST['email']);	
			$password = secureEncode($_POST['pass']);
		
			if(!isset($_POST['gender'])){
				echo 'Error'.$sm['lang'][182]['text'];
				exit;	
			}
			if(!isset($_POST['looking'])){
				echo 'Error'.$sm['lang'][182]['text'];
				exit;	
			}		
			if($sm['plugins']['settings']['showTermsCheckboxRegister'] == 'Yes'){
				$checkbox = secureEncode($_POST['checkbox']);
				if($checkbox != 'OK'){
					echo 'Error'.$sm['lang'][182]['text'];
					exit;					
				}
			}
			$gender = secureEncode($_POST['gender']);
			$city = secureEncode($_POST['city']);
			$country = secureEncode($_POST['country']);
			$lat = secureEncode($_POST['lat']);
			$lng = secureEncode($_POST['lng']);
			$looking = secureEncode($_POST['looking']);
			$date = date('m/d/Y', time());

			if($city == "" || $city == NULL){
				$city = $country;	
			}
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo 'Error'.$sm['lang'][181]['text'];	
				exit;	
			}		
			if (strlen($password) < 4) {
				echo 'Error'.$sm['lang'][187]['text'];
				exit;	
			}		
			if($lat == "" || $lat == NULL){
				echo 'Error'.$sm['lang'][186]['text'];
				exit;	
			}
							
			$year = secureEncode($_POST['year']);		
			if(empty($name) == true || empty($email) == true || empty($password) == true){
				echo 'Error'.$sm['lang'][182]['text'];	
				exit;	
			}			
			$month = secureEncode($_POST['month']);
			$day = secureEncode($_POST['day']);

			$photo = '';
			if(isset($_POST['photo'])){
				$photo = secureEncode($_POST['photo']);
			}
			
						
			$birthday = date('F', mktime(0, 0, 0, $month, 10)).' '.secureEncode($_POST['day']).', '.secureEncode($_POST['year']);
			$age = date('Y') - $year;	

			$bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$city." ".$country;
			
			$ip = getUserIpAddr();

			$sage = '18,30,1';
			$username = secureEncode($_POST['username']);
			$checkUsername = checkIfExist('users','username',$username);
			if($checkUsername == 1){
				echo 'Error'.$sm['lang'][650]['text'];
				exit;					
			}
			if(validate_username($username) == 0){
				echo 'Error'.$sm['lang'][812]['text'];
				exit;				
			}
			if(substr($username, -1) == '.'){
				echo 'Error'.$sm['lang'][812]['text'];
				exit;					
			}

			if(isset($_POST['age1'])){
				$looking = secureEncode($_POST['looking']);
				$sage = secureEncode($_POST['age1']);
				$sage = $sage.','.secureEncode($_POST['age2']).',1';							
			}

			if(checkIfExist('blocked_ips','ip',$ip) == 1){
				echo 'Error'.$sm['lang'][656]['text'];	
				exit;							
			}

			if(checkIfExist('blocked_users','email',$email) == 1){
				echo 'Error'.$sm['lang'][656]['text'];	
				exit;							
			}			
			//CHECK IF USER EXIST
			$email_check = $mysqli->query("SELECT email FROM users WHERE email = '".$email."'");	
			if($email_check->num_rows == 1 ){
				echo 'Error'.$sm['lang'][188]['text'];
				exit;
			} else {
				$salt = base64_encode($name.$email);
				$pswd = crypt($password,$salt);
				$lang = getData('languages','id','WHERE id = '.$_SESSION['lang']);
				if($lang == 'noData'){
					$lang = $sm['plugins']['settings']['defaultLang'];
				}

				$query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,online_day,password,ip,last_access,username,join_date_time)
										VALUES ('".$name."', '".$email."','".$pswd."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$lang."','".$date."','".$bio."','".$looking."','".$sage."',0,0,'".$password."','".$ip."','".time()."','".$username."','".time()."')";					
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

					if($photo != ''){
						$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
						$mysqli->query($query2);
					}
					$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
					$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");	
					if(isset($_POST['age1'])){			
						//admin create user
					} else {
						setcookie("user", $last_id, 2147483647);
						$_SESSION['user'] = $last_id;
					}

					if($sm['plugins']['email']['enabled'] == 'Yes'){
						if($sm['plugins']['settings']['forceEmailVerification'] == 'Yes'){
							welcomeMailVerification($name,$last_id,$email,$password);					
						} else {
							welcomeMailNotification($name,$email,$password);
						}							
					}
					echo $last_id;
				}							 
			}
		break;
		case 'changep':
			$password = secureEncode($_POST['new_password']);
			if (strlen($password) < 6) {
				echo 'Error - '.$sm['lang'][654]['text'];
				exit;	
			}
			$salt = base64_encode($_POST['new_password'].secureEncode($_POST['user']));
			$pswd = crypt($password,$salt);					
			$query = "UPDATE users SET pass = '".$pswd."',imported = '' WHERE id = '".secureEncode($_POST['user'])."'";
			$mysqli->query($query);	
		break;
		case 'spotlight':
			$id = secureEncode($_POST['s_uid']);
			$time = time();
			$lat = secureEncode($_POST['s_lat']);
			$lng = secureEncode($_POST['s_lng']);
			$photo = secureEncode($_POST['s_photo']);
			$lang = secureEncode($_POST['s_lang']);	
			$price = $sm['price']['spotlight'];
			if(!empty(siteConfig('pusher_id'))){
				$event = 'spotlight';
				$data['id'] = $id;
				$data['photo'] = $photo;
				$data['response'] = '<div onClick="goToProfile('.$id.',2)" data-show="1" class="user-in animated bounceIn" style="background-image:url('.$photo.');">'.userStatusSpotlightMobile($id).'</div>';
				$sm['push']->trigger('belloo', $event, $data);			
			}		
			$query = "INSERT INTO spotlight (u_id,time,lat,lng,photo,lang,country) VALUES ('".$id."', '".$time."', '".$lat."', '".$lng."', '".$photo."', '".$lang."', '".$sm['user']['country']."')";
			$mysqli->query($query);	
			$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$id."'";
			$mysqli->query($query2);			
		break;	
		case 'gift':
			$id = secureEncode($_POST['g_id']);
			$gift = secureEncode($_POST['g_src']);
			$price = secureEncode($_POST['g_price']);
			getUserInfo($uid,11);
			if($sm['gift']['credits'] < $price){
			 echo 'error';
			 exit;
			}
			$message = '<img src="'.$gift.'"/>';
			$query = "INSERT INTO chat (s_id,r_id,message,time) VALUES ('".$uid."', '".$id."', '".$message."','".$time."')";
			$mysqli->query($query);	
			$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$uid."'";
			$mysqli->query($query2);			
		break;	
		case 'p_access':
			$id = secureEncode($_POST['id']);
			$query = "INSERT INTO blocked_photos (u1,u2) VALUES ('".$uid."', '".$id."')";
			$mysqli->query($query);			
			$c = $sm['price']['private'];
			$mysqli->query("UPDATE users set credits = credits-'".$c."' where id = '".$uid."'");
		break;
		case 'delete_profile':
			$uid = secureEncode($_POST['uid']);
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
		case 'login':	
			$email = secureEncode($_POST['login_email']);	
			$password = secureEncode($_POST['login_pass']);	

			if(isset($_POST['login_dID'])){
				$dID = secureEncode($_POST['login_dID']);	
			} else {
				$dID = 0;
			}
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo 'Error'.$sm['lang'][181]['text'];	
				exit;	
			}		
			if($email == "" || $email == NULL || $password == "" || $password == NULL ){
				echo 'Error'.$sm['lang'][182]['text'];
				exit;	
			}			
			$email_check = $mysqli->query("SELECT email,id,pass,verified,name,verified,imported FROM users WHERE email = '".$email."'");	
			if($email_check->num_rows == 0 ){
				echo 'Error'.$sm['lang'][183]['text'];	
				exit;
			} else {
				$pass = $email_check->fetch_object();

				if($pass->imported == 'quickdate'){
	                $checkPassword = password_verify($password,$pass->pass);
					if($checkPassword) {
						if($sm['plugins']['settings']['forceEmailVerification'] == 'Yes' && $pass->verified == 0){
							welcomeMailVerification($pass->name,$pass->id,$pass->email,'*******');
						}
						$mysqli->query("UPDATE users SET app_id = '".$dID."' where email = '".$email."'");
						setcookie("user", $pass->id, 2147483647);	
						$_SESSION['user'] = $pass->id;
						exit;	
					} else {
						echo 'Error'.$sm['lang'][184]['text'];	
						exit;		
					}                
				} else {
					if(crypt($password, $pass->pass) == $pass->pass) {
						if($sm['plugins']['settings']['forceEmailVerification'] == 'Yes' && $pass->verified == 0){
							welcomeMailVerification($pass->name,$pass->id,$pass->email,'*******');
						}
						$mysqli->query("UPDATE users SET app_id = '".$dID."' where email = '".$email."'");	
						setcookie("user", $pass->id, 2147483647);
						$_SESSION['user'] = $pass->id;
						exit;	
					} else {
						echo 'Error'.$sm['lang'][184]['text'];	
						exit;		
					}	
				}		
			}
		break;	
		case 'recover':
			if (!empty($_SESSION['lang'])) {
				$sm['lang'] = siteLang($_SESSION['lang']);
			}	
			$email = secureEncode($_POST['recover_email']);	
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo 'Error'.$sm['lang'][181]['text'].'!';	
				exit;	
			}		
			if($email == "" || $email == NULL ){
				echo 'Error'.$sm['lang'][182]['text'].'!';	
				exit;	
			}			
			$email_check = $mysqli->query("SELECT email,id,name FROM users WHERE email = '".$email."'");	
			if($email_check->num_rows == 0 ){
				echo 'Error'.$sm['lang'][183]['text'];	
				exit;
			} else {
				$user = $email_check->fetch_object();
				$time = time();
				$code = md5($time);
				$mysqli->query("INSERT INTO emails (type,uid,code) VALUES (1,'".$user->id."', '".$code."')");	
				$link = $sm['config']['site_url']."/index.php?page=recover&code=".$code."&id=".$user->id;
				$name = $user->name;
				$email = $user->email;
				forgotMailNotification($name,$email,$link);
			}
		break;

		case 'p_photos':
			$id = secureEncode($_POST['id']);
			getUserInfo($id,1);	
			$sm['content'] = requestPage('profile/p_photos');
			echo $sm['content'];		
		break;

		case 'live':
			$sm['content'] = requestPage('live/live');
			echo $sm['content'];		
		break;			
		case 'photo':	
			echo getUserPhotosHeader($uid);
		break;	
		case 'wall':
			$id = secureEncode($_POST['id']);
			getUserInfo($id,1);	
			$check = blockedUser($sm['user']['id'],$sm['profile']['id']);
			if($check == 1){
				echo '<script>alert("'.$sm['profile']['name'].' '.$sm['lang'][325]['text'].'");</script>';
				getUserInfo($uid,1);	
				$sm['content'] = requestPage('profile/content');
				echo $sm['content'];				
				exit;
			}		
			$sm['content'] = requestPage('profile/content');
			echo $sm['content'];		
		break;	
		case 'chat':
			$id = secureEncode($_POST['id']);
			$lc = $id;
			$count = getUserTodayConv($uid);	
			$new = getUserTotalConv($uid,$id);
			$check = blockedUser($sm['user']['id'],$id);
			if($check == 1){
				echo '<script>alert("'.$sm['lang'][325]['text'].'");</script>';
				echo '<script>window.location.href="'.$sm['config']['site_url'].'";</script>';
				exit;
			}		
			if($new == 0 && $count >= $sm['basic']['chat'] && $sm['user']['premium'] == 0 || $new == 0 && $count >= $sm['premium']['chat']){
				$sm['content'] = requestPage('chat/premium');
			} else {
				$sm['content'] = requestPage('chat/content');
			}
			echo $sm['content'];		
		break;
		case 'chat-inchat':
			$arr = array();
			$id = secureEncode($_POST['id']);
			getUserInfo($id,1);
			$arr['chat'] = getChat($sm['user']['id'],$sm['profile']['id']);
			$arr['read'] = checkMessageRead($sm['user']['id'],$sm['profile']['id']);
			$arr['profile'] = $sm['profile'];
			$arr['chatHeader'] = requestPage('chat/chatHeader');
			$report = 'reportUser('.$sm['profile']['id'].','."'".$sm['profile']['first_name']."'".','."'".$sm['profile']['profile_photo']."'".')';
			$del = 'deleteConv('.$sm['profile']['id'].','."'".$sm['profile']['first_name']."'".','."'".$sm['profile']['profile_photo']."'".')';	

			$reportIcon = '';
			if($sm['plugins']['reportProfile']['enabled'] == 'Yes'){
				$reportIcon = '<div class="btn btn--white btn--ico box-shadow-low tooltip-no-js-wrap js-profile-header-btn" style="float:right;margin-left: 15px;" onclick="'.$report.'";>
		    <i class="icon" style="position: absolute;top: 11px;left:11px">
				<svg id="icon-stop" viewBox="0 0 16 16" width="21px" fill="'.$sm['theme']['btn_color']['val'].'"><path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16zM4 8a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1 1 1 0 0 1-1 1H5a1 1 0 0 1-1-1z" fill-rule="nonzero"></path></svg>    	
		    </i> 
		    	</div>';
			}
			$giftIcon = '';
			if($sm['plugins']['gifts']['enabled'] == 'Yes'){
				$giftIcon = '<div class="btn btn--white box-shadow-low btn--ico" style="float:right;margin-left: 15px;" onClick="showChatGifts()">
				<svg id="icon-messenger-gift-m" viewBox="0 0 22 22" width="50%" height="50%" fill="'.$sm['theme']['btn_color']['val'].'"><path d="M3 10v8c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2v-8l1 1H2l1-1zm17 .73V18A3 3 0 0 1 17 21H5a3 3 0 0 1-3-3v-7.27A2 2 0 0 1 1 9V7c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v2a2 2 0 0 1-1 1.73zM10.5 6v4h1V6h-1zm0 14h1v-9h-1v9zM11 1.34a3 3 0 1 1 0 3.32 3 3 0 1 1 0-3.32zM2 9a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v2zm8.5-6a2 2 0 1 0-4 0 2 2 0 0 0 4 0zm5 0a2 2 0 1 0-4 0 2 2 0 0 0 4 0z"></path></svg>	    	
			  
				</div>';
			}

			$arr['chatHeaderRight'] = $reportIcon.'	
		    <div class="btn btn--white btn--ico box-shadow-low tooltip-no-js-wrap js-profile-header-btn" 
		    style="float:right;margin-left: 15px;" onclick="'.$del.'">
		    <i class="icon" style="position: absolute;top: 11px;left:11px">
				<svg id="icon-trash" viewBox="0 0 16 16" width="21px" fill="'.$sm['theme']['btn_color']['val'].'"><path d="M11.02 2.22v-.89C11.02.6 10.44 0 9.72 0H6.28c-.7 0-1.29.6-1.29 1.33v.9H.23v.88h1.73v10.67A2.2 2.2 0 0 0 4.12 16h7.76c1.2 0 2.16-1 2.16-2.22V3.1h1.73v-.89h-4.75zm-5.18-.89c0-.24.2-.44.43-.44h3.46c.23 0 .43.2.43.44v.9H5.84v-.9zm7.34 12.45c0 .73-.58 1.33-1.3 1.33H4.12c-.72 0-1.3-.6-1.3-1.33V3.1h10.36v10.67zm-7.2-8.8h.86v8.2h-.86v-8.2zm3 0h.86v8.2h-.86v-8.2z" fill-rule="evenodd"></path></svg>		    	
		    </i>
			</div>'.$giftIcon;
			echo json_encode($arr);		
		break;	
		case 'chat-menu':
			$lc = getUserLC($sm['user']['id']);		
			if($lc == 0){
				$sm['profile'] = '';
			} else {
				$sm['profile'] = 'yes';
			}	
			if($sm['profile'] == ''){
				$page = 'empty';
			} else {
				$page = 'content';
			}
			$sm['content'] = requestPage('chat/'.$page);
			echo $sm['content'];		
		break;	
		case 'chat_p':
			$id = secureEncode($_POST['id']);
			getUserInfo($id,1);	
			$time = time();		
			$mysqli->query("INSERT INTO chat (s_id,r_id,time,access) VALUES ('".$sm['user']['id']."','".$id."','".$time."',1)");		
			$sm['content'] = requestPage('chat/content');
			echo $sm['content'];		
		break;	
		case 'meet':	
			$sm['content'] = requestPage('meet/content');
			echo $sm['content'];		
		break;
		case 'meet_back':	
			$sm['content'] = requestPage('meet/content_back');
			echo $sm['content'];		
		break;	
		case 'popular':	
			$sm['content'] = requestPage('popular/content');
			echo $sm['content'];		
		break;
		case 'mylikes':	
			$sm['content'] = requestPage('matches/mylikes');
			echo $sm['content'];		
		break;	
		case 'credits':	
			$sm['content'] = requestPage('profile/credits');
			echo $sm['content'];		
		break;
		case 'getCredits':	
			$sm['content'] = requestPage('profile/buyCredits');
			echo $sm['content'];		
		break;		
		case 'withdraw':	
			$sm['content'] = requestPage('profile/withdraw');
			echo $sm['content'];		
		break;
		case 'blocked':	
			$sm['content'] = requestPage('profile/blocked');
			echo $sm['content'];		
		break;			
		case 'popularity':	
			$sm['content'] = requestPage('profile/popularity');
			echo $sm['content'];		
		break;	
		case 'premium':	
			$sm['content'] = requestPage('profile/premium');
			echo $sm['content'];		
		break;			
		case 'fans':	
			$sm['content'] = requestPage('fans/content');
			echo $sm['content'];		
		break;	
		case 'visits':	
			$sm['content'] = requestPage('visits/content');
			echo $sm['content'];		
		break;		
		case 'matches':	
			$sm['content'] = requestPage('matches/content');
			echo $sm['content'];		
		break;	
		case 'settings':	
			$sm['content'] = requestPage('profile/settings');
			echo $sm['content'];		
		break;		
		case 'discover':	
			$sm['content'] = requestPage('discover/content');
			echo $sm['content'];		
		break;	
		case 'game':
			$e_age = explode( ',', $sm['user']['s_age'] );
			$age1 = $e_age[0];
			$age2 = $e_age[1];
			$gender = $sm['user']['s_gender'];
			if($gender == 3){
				$u_total = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'				  
						  AND id <> '".$uid."'
						  ORDER BY distance DESC, last_access DESC");
			} else {
				$u_total = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'
						  AND gender = '".$sm['user']['s_gender']."'					  
						  AND id <> '".$uid."'
						  ORDER BY distance DESC, last_access DESC");			
			}
			$array1  = array();
			if ($u_total->num_rows > 0) { 
				while($u_t= $u_total->fetch_object()){
					$array1[] = $u_t->id;						
				}
			}		
			$u_total2 = $mysqli->query("SELECT u2 FROM users_likes where u1 = '".$uid."'");
			$array2  = array();
			if ($u_total2->num_rows > 0) {
				while($u_t2 = $u_total2->fetch_object()) {
					$array2[] = $u_t2->u2;						
				}
			}
			$resultado = array_diff($array1, $array2);
			$resultado = array_slice($resultado, 0, 1);
			$user_g = array_shift($resultado);
			if($user_g == 0){
				if($gender == 3){
					$user_game = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
							  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
							  AS distance 
							  FROM users
							  WHERE age BETWEEN '".$age1."' AND '".$age2."'				  
							  AND id <> '".$uid."'
							  ORDER BY distance DESC, last_access DESC
							  LIMIT 1");
				}else{
					$user_game = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
							  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
							  AS distance 
							  FROM users
							  WHERE age BETWEEN '".$age1."' AND '".$age2."'
							  AND gender = '".$sm['user']['s_gender']."'					  
							  AND id <> '".$uid."'
							  ORDER BY distance DESC, last_access DESC
							  LIMIT 1");			
				}			
			} else {
				$user_game = $mysqli->query("SELECT * FROM users WHERE id = '".$user_g."'");
			}

			if($user_game->num_rows == 1) {
				$sexy_game = $user_game->fetch_object();
				$info = array(
					  "id" => $sexy_game->id,
					  "name" => $sexy_game->name,
					  "status" => userStatusIcon($sexy_game->id),
					  "distance" => distance($sm['user']['lat'],$sm['user']['lng'],$sexy_game->lat,$sexy_game->lng),				  
					  "age" => $sexy_game->age,
					  "city" => $sexy_game->city,				  
					  "photos" => getUserPhotosAll($sexy_game->id),	  
					  "total" => getUserTotalLikers($sexy_game->id),
					  "photo" => profilePhoto($sexy_game->id)
				);	
				echo json_encode($info);
			}

		break;
		case 'game_like':
			$id = secureEncode($_POST['id']);
			$action = secureEncode($_POST['like']);
			
			$time = time();		
			$mysqli->query("INSERT INTO users_likes (u1,u2,love,time) VALUES ('".$uid."','".$id."','".$action."','".$time."') ON DUPLICATE KEY update love = '".$action."'");

			$arr = array();
			$arr['match'] = 0;
			$fake = getData('users','fake','where id ='.$id);
			$name = getData('users','name','where id ='.$id);
			if($action == 1){
				$mysqli->query("UPDATE users set popular = popular+1 where id = '".$id."'");
			
				if($fake == 0){
					$sm['profile_notifications'] = userNotifications($id);
					if($sm['profile_notifications']['fan'] == 1 && isFan($id,$sm['user']['id']) == 0 && $sm['config_email']['host'] != ''){
						fanMailNotification($id);
					}
					if(isFan($id,$sm['user']['id']) == 1){
						$arr['match'] = 1;
					}
					if(isFan($id,$sm['user']['id']) == 1 && $sm['profile_notifications']['match_m'] == 1 && $sm['config_email']['host'] != ''){
						matchMailNotification($id);														   
					}
					$noti= 'like'.$id;
					$data['id'] = $sm['user']['id'];
					$data['message'] = $sm['alang'][252]['text'];
					$data['time'] = date("H:i", time());
					$data['type'] = 4;
					$data['icon'] = $sm['user']['profile_photo'];
					$data['name'] = $sm['user']['name'];      
					$data['photo'] = 0;
					$data['unread'] = checkUnreadMessages($id);       
					$sm['push']->trigger(siteConfig('pusher_key'), $noti, $data); 
				}

				//activity content
				if($sm['plugins']['logActivity']['enabled'] == 'Yes'){ 
					$ac = array();
					$ac['u1']['id'] = $sm['user']['id'];
					$ac['u2']['id'] = $id;
					$ac['u1']['name'] = $sm['user']['name']; 
					$ac['u2']['name'] = $name; 	
					$ac['u1']['photo'] = $sm['user']['profile_photo']; 
					$ac['u2']['photo'] = profilePhoto($id); 

					$adminPush= 'adminActivity';
					$pushData['like'] = $ac;	
					$ac = json_encode($ac);
					activity('like',$ac,'Profile like',$sm['user']['id']);	       
				}

			}
			
			echo json_encode($arr);
		break;
		case 'instagram':
			$insta = secureEncode($_POST['insta']);
			$url = "https://www.instagram.com/".$insta."/media/";
			$inst_stream = callInstagram($url);
			$results = json_decode($inst_stream, true);
			$i=0;
			foreach($results['items'] as $item){
				$image_link = $item['images']['standard_resolution']['url'];
				if($i == 0){
					$u_foto = $mysqli->query("SELECT * FROM users_photos where u_id = '".$uid."' and profile = 1");		
					if ($u_foto->num_rows == 0) {		
						$mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,profile,approved,private) 
																   VALUES('$uid','$image_link', '$image_link',1,'".$sm['config']['photo_review']."','$image_link')");			
					} else {
					$mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,approved,private)
																   VALUES ('$uid','$image_link', '$image_link','".$sm['config']['photo_review']."','$image_link')");	
					}
				} else {
				$mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,approved,private)
															   VALUES ('$uid','$image_link', '$image_link','".$sm['config']['photo_review']."','$image_link')");	
				}
			}
			$i++;
		break;	
		case 'user_notifications':
			$val = secureEncode($_POST['val']);
			$col = secureEncode($_POST['col']);		
			$mysqli->query("UPDATE users_notifications set $col = '$val' where uid = '".$sm['user']['id']."'");		
		break;	
		case 'meet_filter':
			$sm['filter']['age'] = secureEncode($_POST['age']);
			$sm['filter']['gender'] = secureEncode($_POST['gender']);
			$sm['filter']['radius'] = secureEncode($_POST['radius']);
			$sm['filter']['online'] = secureEncode($_POST['online']);
			$sm['filter']['limit'] = secureEncode($_POST['limit']);
			$sm['filter']['username'] = secureEncode($_POST['username']);	
			$check2 = $sm['plugins']['meet']['searchResult'] * $sm['filter']['limit']+$sm['plugins']['meet']['searchResult'];
			if($check2 == 0){
				$check2 = 20;
			}
			$all = count($sm['genders']);
			$all = $all + 1;
			if($all == $sm['filter']['gender']){
				$g = 3;
			} else {
				$g = getGenderSex($sm['filter']['gender']);
			}

			$license = $sm['settings']['license'];
			$c = $sm['plugins']['fakeUsersGenerator']['generateCountry'];
			$time = time() - rand(1,100000);
			$check = getTotalUsersCity($sm['user']['lat'],$sm['user']['lng'],$sm['filter']['radius'],$sm['filter']['gender'],$sm['filter']['age'],$sm['user']['city']); 
			$today = date('w');
			$date = date('m/d/Y', time());
			$amount = $sm['plugins']['fakeUsersGenerator']['generateFakeUsers'];
			$url=$userApi.
			    'g=' . urlencode($g) .
			    '&a=' . urlencode($sm['filter']['age']) .
			    '&c=' . urlencode($c) .
			    '&amount=' . urlencode($amount) . 
			    '&pc=' . urlencode($license);

			$apiLimit = 'No';
			if($sm['settings']['fakeUserUsage'] >= $sm['settings']['fakeUserLimit']){
				$apiLimit = 'Yes';
			}
			if($check < $check2 && $sm['plugins']['fakeUsersGenerator']['enabled'] == 'Yes' && $apiLimit == 'No'){
				$callApi = curl_get_contents($url);
				$api = json_decode($callApi);
				if(!empty($api->result)){	

					//activity
					$age = explode( ',', $sm['filter']['age'] );
					$age1 = $age[0];
					$age2 = $age[1];					
					$activity = 'Created '.$amount.' '.getGenderName($sm['filter']['gender']).'('.$age1.','.$age2.') profiles from '.$sm['user']['city'].' - '.$sm['user']['country'];
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
						 VALUES ('".$val->id."', '".$val->name."', '".$email."', '', '".$val->age."', '".$birthday."','".$sm['user']['city']."', '".$sm['user']['country']."', '".$sm['filter']['gender']."', '".$sm['user']['lat']."', '".$sm['user']['lng']."', '0', '0', '".$time."', '0', '0', '1', '1', '0', '1', '0', '1', '".$today."', '".$date."', '".time()."','".$val->id."')");


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

			echo meetFilter($sm['user']['id'],$sm['user']['lang'],$sm['filter']['gender'],$sm['filter']['age'],$sm['filter']['radius'],$sm['filter']['online'],$sm['filter']['limit'],$sm['filter']['username']);		
		break;	
		case 'meet_filter_mobile':
			$sm['filter']['age'] = secureEncode($_POST['age']);
			$sm['filter']['gender'] = secureEncode($_POST['gender']);
			$sm['filter']['radius'] = secureEncode($_POST['radius']);
			$sm['filter']['online'] = secureEncode($_POST['online']);
			$sm['filter']['limit'] = secureEncode($_POST['limit']);		
			echo meetFilterMobile($sm['user']['id'],$sm['user']['lang'],$sm['filter']['gender'],$sm['filter']['age'],$sm['filter']['radius'],$sm['filter']['online'],$sm['filter']['limit']);		
		break;	
		case 'like':
			$id = secureEncode($_POST['id']);
			$time = time();
			$mysqli->query("insert into users_likes (u1,u2,love,time) values ('".$uid."','".$id."',1,'".$time."')");	
		break;		
		case 'delete':
			$photo = secureEncode($_POST['photo']);
			$query = "DELETE FROM users_photos where photo = '$photo'";
			$query2 = "DELETE FROM users_profile_photo where photo = '$photo'";		
			$mysqli->query($query);	
			$mysqli->query($query2);			
		break;
		case 'manage':
			$photo = secureEncode($_POST['pid']);
			$profile = secureEncode($_POST['profile']);
			$block = secureEncode($_POST['block']);
			$unblock = secureEncode($_POST['unblock']);
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
				$query = "DELETE FROM users_photos where id = '$photo'";	
				$mysqli->query($query);				
			}		
		break;	
		case 'search':
			$data = secureEncode($_POST['data']);
			$arr =array();
			$arr['result'] = searchFriends($uid,$data);
			echo json_encode($arr);			
		break;	
		case 'lang':
			$lang = secureEncode($_POST['lang']);
			$query = "UPDATE users SET lang = '$lang' WHERE id = '$uid'";		
			$mysqli->query($query);			
		break;	
		case 'block':
			$u1 = secureEncode($_POST['u1']);
			$u2 = secureEncode($_POST['u2']);		
			$mysqli->query("INSERT INTO black_list (u1,u2) VALUES ('$u1','$u2')");
			$mysqli->query("DELETE FROM users_friends WHERE u_id ='$u1' AND friend_id = '$u2'");	
			$mysqli->query("DELETE FROM chat WHERE s_id ='$u1' AND r_id = '$u2' OR r_id ='$u1' AND s_id = '$u2'");

		break;		
	}
}
$mysqli->close();