<?php
/* Mobile core by Premium Dating Script - xohansosa@gmail.com */
function getAdminLangEditApp($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM app_lang where lang_id = '$id' ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$lang.= '<div class="col-md-2" style="height:50px;"><input class="form-control" value="'.$result->text.'"  data-langid-a="'.$result->lang_id.'" data-alid="'.$result->id.'" /></div>';
		}	
	}
	return $lang;
}
function appConfig($val) {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM config_app");
	$result = $config->fetch_object();
	return $result->$val;
}
function appConfigApi() {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM config_app");
	$result = $config->fetch_object();
	return $result;
}
function getLastMessageMobileApp($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			if($spotl->photo == 1 ){
				if($spotl->s_id == $uid1){
					$message = $sm['alang'][130]['text'];
				} else {
					$message = $sm['alang'][131]['text'];
				}
			}
			else if($spotl->access == 1 ){
				$message = $sm['lang'][174]['text'];		
			} else if($spotl->gif == 1 ){
				if($spotl->s_id == $uid1){
					$message = $sm['alang'][128]['text'];
				} else {
					$message = $sm['alang'][129]['text'];
				}		
			} else if($spotl->gift == 1 ){
				$message = $sm['alang'][267]['text'];		
			} else if($spotl->credits > 0 && $spotl->story == 0 && $spotl->gift == 0){
				if($spotl->s_id == $uid1){
					$message = '<b class="black-text">'.$sm['alang'][268]['text'].' '.$spotl->credits.' '.$sm['alang'][48]['text'].'</b>';
				} else {
					$message = '<b class="black-text">'.getData('users','name','where id = '.$spotl->s_id).' '.$sm['alang'][269]['text'].' '.$spotl->credits.' '.$sm['alang'][48]['text'].'</b>';
				}		
			} else {
				$message = $spotl->message;			
			}
		}	
	}
	return $message;
}
function pushNotification($r_id,$title,$message,$photo){
	$content = array(
	  "en" => $message
	  );
	$headings = array(
	  "en" => $title
	  );	
	$fields = array(
	  'app_id' => "04f22177-366a-40dc-99cf-6d1342c4e1f5",
	  'include_player_ids' => array($r_id),
	  'contents' => $content,
	  'headings' => $headings,
	  'large_icon' => $photo
	);
	$fields = json_encode($fields);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://gamethrive.com/api/v1/notifications");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
						   'Authorization: Basic OTA1ZGVjYjEtMTllMi00NzlmLTgwMWQtYjYxY2UzOThkOGNj'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($ch);
	curl_close($ch);
}
function isFanApp($uid1,$uid2) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u1 = '".$uid1."' and u2 = '".$uid2."' and love = 1");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}
function getLastMessageMobileSeenApp($uid1,$uid2){	
	global $mysqli,$sm;
	$message = 0;
	$spotlight = $mysqli->query("SELECT seen,s_id FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			if($spotl->s_id == $uid1 && $spotl->seen == 0){
				$message = 1;
			}
			if($spotl->s_id == $uid1 && $spotl->seen == 1){
				$message = 2;
			}
			if($spotl->s_id != $uid1 && $spotl->seen == 0){
				$message = 3;
			}
			if($spotl->s_id != $uid1 && $spotl->seen == 1){
				$message = 4;
			}			
		}	
	}
	return $message;
}
function getUserSuperLikes($uid){	
	global $mysqli,$sm;
	$result = 0;
	$query = $mysqli->query("SELECT superlike FROM users WHERE id = '".$uid."'"); 
	if ($query->num_rows > 0) {
		$sl = $query->fetch_object();
		$result = $sl->superlike;
	}
	return $result;
}
function getGiftsApp() {
	global $mysqli,$sm;
	$gifts = array();
	$gift = $mysqli->query("SELECT * FROM gifts order by price ASC limit 100");
	if ($gift->num_rows > 0) { 
		while($gi = $gift->fetch_object()){
			$gifts[] = array(
				"price" => $gi->price, 
				"icon" => $gi->icon
			);		
		}
	}
	return $gifts;			
}
function getCreditsPriceApp() {
	global $mysqli,$sm;
	$credits = array();
	$query = $mysqli->query("SELECT * FROM config_credits order by credits ASC limit 100");
	if ($query->num_rows > 0) { 
		while($q = $query->fetch_object()){
			$credits[] = array(
				"price" => $q->price,
				"quantity" => $q->credits,
			);		
		}
	}
	return $credits;			
}
function totalAppUsers(){
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users where app_id <> 0 ");
	$total = $query->fetch_assoc();
	return $total['total'];	
}
function appUsers($title,$body,$image){
	global $mysqli;
	$result = 0;
	$user = '';
	$query = $mysqli->query("SELECT app_id FROM users where app_id <> 0 ");
	if ($query->num_rows > 0) { 
		while($q = $query->fetch_object()){
			pushNotification($q->app_id,$title,$body,$image);
		}
	}
	return $user;
}
function getPremiumPriceApp() {
	global $mysqli,$sm;
	$premium = array();
	$query = $mysqli->query("SELECT * FROM config_premium order by price ASC limit 100");
	if ($query->num_rows > 0) { 
		while($q = $query->fetch_object()){
			$premium[] = array(
				"price" => $q->price,
				"quantity" => $q->days,
			);		
		}
	}
	return $premium;			
}
function userAppPhotos($id,$video=0) {
	global $mysqli,$sm;
	$result=array();
	$config = $mysqli->query("SELECT * FROM users_photos where approved <= 1 and video = $video and story = 0 and u_id = '".$id."' order by profile desc");
	if($config->num_rows > 0 ){
		while($row = $config->fetch_assoc()){

			if($video == 1){
				$result[] = array(
					"id" => $row['id'], 
					"video" => $row['photo'],
					"blocked" => $row['blocked'],
					"approved" => $row['approved']
				);
			} else {
				$result[] = array(
					"id" => $row['id'], 
					"thumb" => $row['thumb'],
					"photo" => $row['photo'],
					"approved" => $row['approved'],
					"profile" => $row['profile'],				
					"blocked" => $row['blocked']
				);
			}
		}
	}
	return $result;	
}
function get_time_difference_php($str){
		global $sm;

        $today = strtotime(date('Y-m-d H:i:s'));
        // It returns the time difference in Seconds...
        $time_differnce = $today-$str;
        // To Calculate the time difference in Years...
        $years = 60*60*24*365;
        // To Calculate the time difference in Months...
        $months = 60*60*24*30;
        // To Calculate the time difference in Days...
        $days = 60*60*24;
        // To Calculate the time difference in Hours...
        $hours = 60*60;
        // To Calculate the time difference in Minutes...
        $minutes = 60;
        if(intval($time_differnce/$years) > 1)
        {
            return intval($time_differnce/$years)." ".$sm['alang'][141]['text'];
        }else if(intval($time_differnce/$years) > 0)
        {
            return intval($time_differnce/$years)." ".$sm['alang'][140]['text'];
        }else if(intval($time_differnce/$months) > 1)
        {
            return intval($time_differnce/$months)." ".$sm['alang'][139]['text'];
        }else if(intval(($time_differnce/$months)) > 0)
        {
            return intval(($time_differnce/$months))." ".$sm['alang'][138]['text'];
        }else if(intval(($time_differnce/$days)) > 1)
        {
            return intval(($time_differnce/$days))." ".$sm['alang'][137]['text'];
        }else if (intval(($time_differnce/$days)) > 0) 
        {
            return intval(($time_differnce/$days))." ".$sm['alang'][136]['text'];
        }else if (intval(($time_differnce/$hours)) > 1) 
        {
            return intval(($time_differnce/$hours))." ".$sm['alang'][135]['text'];
        }else if (intval(($time_differnce/$hours)) > 0) 
        {
            return intval(($time_differnce/$hours))." ".$sm['alang'][134]['text'];
        }else if (intval(($time_differnce/$minutes)) > 1) 
        {
            return intval(($time_differnce/$minutes))." mins";
        }else if (intval(($time_differnce/$minutes)) > 0) 
        {
            return intval(($time_differnce/$minutes))." min";
        }else if (intval(($time_differnce)) > 1) 
        {
            return intval(($time_differnce))." ".$sm['alang'][133]['text'];
        }else
        {
            return $sm['alang'][132]['text'];
        }
  }