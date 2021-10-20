<?php
/* Belloo By Xohan - xohansosa@gmail.com - https://premiumdatingscript.com */
header('Content-Type: text/html; charset=UTF-8');
require_once('connect.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function getMinMax($table,$col,$order){
	global $mysqli;
	$name = secureEncode($name);
	$q = $mysqli->query("SELECT $col FROM $table ORDER BY $col $order");
	$r = $q->fetch_object();
	return 	$r->$col;	
}



function checkIfExist($table,$col,$val){
    global $mysqli;
	$query = $mysqli->query("SELECT * FROM $table where $col = '".$val."' LIMIT 1");
    if(isset($query->num_rows) && !empty($query->num_rows)){
        return 1;
    } else {
    	return 0;
    }  	
}

function checkIfExistFilter($table,$filter){
    global $mysqli;
	$query = $mysqli->query("SELECT * FROM $table where $filter LIMIT 1");
    if(isset($query->num_rows) && !empty($query->num_rows)){
        return 1;
    } else {
    	return 0;
    }  	
}

function cronUpdateOnlineDay($dayNumber){
	global $mysqli;
	$result = array();
	$day = 'mon';
	switch ($dayNumber) {
		case 'day0':
			$dayNumber = 0;		
			$day = 'sun';
		break;		
		case 'day1':
			$dayNumber = 1;		
			$day = 'mon';
		break;
		case 'day2':
			$dayNumber = 2;		
			$day = 'tue';
		break;
		case 'day3':
			$dayNumber = 3;		
			$day = 'wed';
		break;
		case 'day4':
			$dayNumber = 4;		
			$day = 'thu';
		break;
		case 'day5':
			$dayNumber = 5;
			$day = 'fri';
		break;
		case 'day6':
			$dayNumber = 6;
			$day = 'sat';
		break;												
	}

	updateData('users','online_day',0,'WHERE fake = 1');
	$query = $mysqli->query("SELECT uid FROM users_online_day WHERE $day = 1");
	
	if($query->num_rows >= 1) {
		while($row = $query->fetch_object()){
			$uid = $row->uid;
			$filter = 'WHERE id ='.$uid;
			updateData('users','online_day',$dayNumber,$filter);

			$time = rand(0,5000)+time();
			$time = rand(0,5000)-$time;
			updateData('users','last_access',$time,$filter);
		}		
	}
	return $result;	
}

function updateData($table,$col,$val,$filter=''){
	global $mysqli;
	$mysqli->query("UPDATE $table SET $col = $val $filter");
}


function selectD($table,$col,$filter='') {
	global $mysqli;
	$result=array();
	$query= $mysqli->query("SELECT DISTINCT $col FROM $table $filter");
	while($row = $query->fetch_assoc()){
		if(!empty($row[$col])){
			$result[] = array(
				$col => $row[$col],		
			);
		}
	}	
	return $result;
}

function selectC($table,$filter='') {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM $table $filter");
	if(isset($query->num_rows) && !empty($query->num_rows)){
		$total = $query->fetch_assoc();
		$result = $total['total'];
	}
	return $result;
}

function selectSum($table,$col,$filter='') {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT sum($col) as total FROM $table $filter");
	$total = $query->fetch_assoc();
	if($total['total'] > 0){
		$result = $total['total'];
	}
	return $result;
}

function getData($table,$col,$filter=''){
	global $mysqli;
	$q = $mysqli->query("SELECT $col FROM $table $filter");
	$result = 'noData';
	if($q->num_rows >= 1) {
		$r = $q->fetch_object();
		$result = $r->$col;
	}
	return $result;
}


function getUserData($id,$col){
	global $mysqli;
	$q = $mysqli->query("SELECT $col FROM users where id = '".$id."'");
	$result = 'noData';
	if($q->num_rows >= 1) {
		$r = $q->fetch_object();
		$result = $r->$col;
	}
	return $result;
}


function getArray($table,$filter='',$order,$limit=''){
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT * FROM $table $filter ORDER BY $order $limit");
	if(isset($query->num_rows) && !empty($query->num_rows)){
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}		
	}
	return $result;	
}

function getStreamsArrayByGeo($table,$lat,$lng,$filter='',$limit='',$radius=10000){
	global $mysqli,$sm;
	$arr = array();
	$query = "SELECT uid,start_time,viewers,credits,in_private,private_price,( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
	* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
	FROM $table
	WHERE $filter
	HAVING distance < $radius
	ORDER BY distance
	$limit";
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			$arr[] = $row;	
		}
	}

	return $arr;		 	
}

function getStringsBetween($str, $start='[', $end=']', $with_from_to=false){
	$arr = [];
	$last_pos = 0;
	$last_pos = strpos($str, $start, $last_pos);
	while ($last_pos !== false) {
	    $t = strpos($str, $end, $last_pos);
	    $arr[] = ($with_from_to ? $start : '').substr($str, $last_pos + 1, $t - $last_pos - 1).($with_from_to ? $end : '');
	    $last_pos = strpos($str, $start, $last_pos+1);
	}
	return $arr; 
}


function getArrayMultiple($mul,$table,$filter='',$order,$limit=''){
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT $mul FROM $table $filter ORDER BY $order $limit");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}		
	}
	return $result;	
}

function getSelectedArray($col,$table,$filter='',$order,$limit=''){
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT $col FROM $table $filter ORDER BY $order $limit");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}		
	}
	return $result;	
}

function getArrayD($table,$filter=''){
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT DISTINCT * FROM $table $filter");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}		
	}
	return $result;	
}

function getArrayDSelected($col,$table,$filter=''){
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT DISTINCT $col FROM $table $filter");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}		
	}
	return $result;	
}

function getArrayDSelectedVal($col,$table,$filter=''){
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT DISTINCT $col FROM $table $filter");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			$result[] = $row[$col];
		}		
	}
	return $result;	
}

function getCols($table) {
	global $mysqli;
	$result = [];
	$query = $mysqli->query("SHOW COLUMNS FROM $table");
	while($row = mysqli_fetch_array($query)){
	    $result[] = $row['Field'];
	}
	return $result;
}



function getDataArray($table,$filter) {
	global $mysqli;
	$result = [];
	$query = $mysqli->query("SELECT * FROM $table WHERE $filter");
	$result = $query->fetch_assoc();
	return $result;
}

function getDataArraySelectedCol($col,$table) {
	global $mysqli;
	$result = [];
	$query = $mysqli->query("SELECT $col FROM $table");
	$result = $query->fetch_assoc();
	return $result;
}


function getDataArrayFull($table,$filter) {
	global $mysqli;
	$result = array();
	$query = $mysqli->query("SELECT * FROM $table $filter");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}		
	}
	return $result;	
}


function getMapData($filter) {
	global $mysqli;
	$result = array();
	$i = 0;
	$query = $mysqli->query("SELECT id,lat,lng FROM users $filter");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_object()){
			$result[$i]['profileImage'] = profilePhoto($row->id);
			$result[$i]['pos'][0] = $row->lat;
			$result[$i]['pos'][1] = $row->lng;	
			$i++;	
		}		
	}
	return $result;	
}



function getUserStories($name,$photo,$filter='',$order,$limit=''){
	global $mysqli,$sm;
	$result = array();
	$i = 0;
	$query = $mysqli->query("SELECT * FROM users_story $filter ORDER BY $order $limit");
	if($query->num_rows >= 1) {
		while($row = $query->fetch_assoc()){
			if(isset($sm['user'])){
				$purchased = isStoryPurchased($row['id'],$sm['user']['id']);
			} else {
				$purchased = 0;
			}
			if($row['review'] == 'Yes' && $sm['plugins']['story']['showStoryInReview'] == 'No'){ 	
				if(isset($sm['user'])){
					if($sm['user']['id'] != $row['uid']){
						continue;
					}
				} else {
					continue;
				}
			}			
			$result[$i]['id'] = 'story'.$row['id'];
			$result[$i]['src'] = $row['story'];		
			$result[$i]['sid'] = $row['id'];
			$result[$i]['uid'] = $row['uid'];
			$result[$i]['title'] = $name;
			$result[$i]['stype'] = $row['storyType'];
			$result[$i]['url'] = $row['story'];
			$result[$i]['credits'] = $row['credits'];
			$result[$i]['review'] = $row['review'];
			$result[$i]['icon'] = $photo;
			$result[$i]['duration'] = $sm['plugins']['story']['duration']*1000;
			$result[$i]['date'] = time_elapsed_string($row['storyTime']);
			$result[$i]['purchased'] = $purchased;
			$i++;
		}		
	}
	return $result;	
}

function getAlbumStories($album,$name,$photo){
	global $mysqli,$sm;
	$result = array();
	$i = 0;
	$query = $mysqli->query("SELECT * FROM users_story_albums WHERE id = '".$album."'");
	if($query->num_rows == 1) {
		$album = $query->fetch_object();
		$stories = explode(',',$album->stories);
		foreach ($stories as $st) {		
			$result[$i] = getStoryData($st,$name,$photo);
			$i++;
		}
	}		
	return $result;	
}

function getStoryData($sid,$name,$icon){
	global $mysqli,$sm;
	$result = array();
	$query = $mysqli->query("SELECT * FROM users_story where id = '".$sid."' limit 1");
	if($query->num_rows >= 1) {
		$row = $query->fetch_object();
		if(isset($sm['user'])){
			$purchased = isStoryPurchased($row->id,$sm['user']['id']);
		} else {
			$purchased = 0;
		}			
		$result['id'] = 'story'.$row->id;
		$result['src'] = $row->story;						
		$result['sid'] = $row->id;
		$result['uid'] = $row->uid;
		$result['title'] = $name;
		$result['stype'] = $row->storyType;
		$result['url'] = $row->story;
		$result['credits'] = $row->credits;
		$result['review'] = $row->review;
		$result['icon'] = $icon;
		$result['duration'] = $sm['plugins']['story']['duration']*1000;
		$result['date'] = time_elapsed_string($row->storyTime);
		$result['purchased'] = $purchased;				
	}
	return $result;	
}

function unicode2html($str){
    setlocale(LC_ALL, 'en_US.UTF-8');
    $str = preg_replace("/u([0-9a-fA-F]{4})/", "&#x\\1;", $str);
    return iconv("UTF-8", "ISO-8859-1//TRANSLIT", $str);
}

function activity($type,$message,$title,$uid=0){
	global $mysqli;
	$time = time();
	$mysqli->query("INSERT INTO activity (a_type,message,title,time,uid) VALUES ('".$type."','".$message."','".$title."','".$time."','".$uid."')");	
}

function getIdInterest($name) {
	global $mysqli,$sm;
	$name = secureEncode($name);
	$user = $mysqli->query("SELECT id FROM interest WHERE name = '".$name."'");
	$u = $user->fetch_object();
	return 	$u->id;
}
function getDomain($url){
	$url = str_replace('www.','', $url);
	return $url;
}


function apiCall($action,$query){
	global $sm;	
    $url = $sm['config']['site_url'].'requests/api.php?action='.$action.'&query='.$query.'&token='.$sm['settings']['apiToken'];
    $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => TRUE,
    );
    $ch = curl_init();
    curl_setopt_array( $ch, $curl_options );
    $output = curl_exec( $ch );
    curl_close($ch);
    $call = json_decode($output,true);
    return $call; 	
}

function curl_get_contents($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json'
	    ));  
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);  
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function curlGetAdmin($action){
  global $sm;
  $ch = curl_init();
  $curl = $sm['config']['site_url'].'requests/admin.php';
  curl_setopt($ch, CURLOPT_URL, $curl);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json'
	    ));  
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,"action=".$action);

  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function checkInterestExist($name) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM interest WHERE name = '".$name."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}
function profilePhoto($uid,$big=0) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$photo = $mysqli->query("SELECT photo,thumb FROM users_photos where u_id = '".$uid."' and profile = 1 order by id asc LIMIT 1");
	if($photo->num_rows == 1) {
		$profile = $photo->fetch_object();	
		if($big == 1){
			$profile_photo = $profile->photo;
		} else {
			$profile_photo = $profile->thumb;
		}
		
	} else {
		$profile_photo = $sm['config']['theme_url']."/images/no_user.png";
	}
	return $profile_photo;
}
function randomPhoto($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$photos = array();
	$photo = $mysqli->query("SELECT photo,thumb FROM users_photos where u_id = '".$uid."' and approved = 1 and video = 0 ORDER BY rand() LIMIT 1");
	if ($photo->num_rows > 0) {
		while($profile = $photo->fetch_object()){	
			if (file_exists($profile->thumb)) {	
				$photos[] = $profile->thumb;
			} else {
				$photos[] = $profile->photo;
			}
		}
	} else {
		$photos = $sm['config']['theme_url']."/images/no_user.png";
	}
	return $photos[0];
}

function getRandomAnswer($q,$lang){
	global $mysqli;
	$a = '';
	$query = $mysqli->query("SELECT answer FROM config_profile_answers where qid = '".$q."' and lang_id = '".$lang."' order by rand() LIMIT 1");
	if ($query->num_rows > 0) {
		$r = $query->fetch_object();
		$a = $r->answer;
	}
	return $a;	
}

function randomPhotoUser($g) {
	global $mysqli,$sm;
	$photos = '';
	$all = count($sm['genders']);
	$all = $all + 1;
	if($g == $all){
		$photo = $mysqli->query("SELECT id FROM users where age < 30 ORDER BY rand() LIMIT 1");
	} else {
		$photo = $mysqli->query("SELECT id FROM users where gender = '".$g."' and age < 30 ORDER BY rand() LIMIT 1");		
	}
	if ($photo->num_rows > 0) {
		$u = $photo->fetch_object();
		$photos = profilePhoto($u->id);
	}
	return $photos;
}
function profilePhotoThumb($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$photo = $mysqli->query("SELECT thumb FROM users_photos where u_id = '".$uid."' and approved = 1 and profile = 1 LIMIT 1");
	if($photo->num_rows == 1) {
		$profile = $photo->fetch_object();
		$profile_photo = $profile->thumb;
	} else {
		$profile_photo = $sm['config']['theme_url']."/images/no_user.png";
	}
	return $profile_photo;
}
function userStatus($uid) {
	global $mysqli,$sm;
	$today = date('w');	
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access,fake,online_day FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time() || $st->fake == 1 && $st->online_day == $today){
		$s = "y";
	} else {
		$s = "n";
	}
	return $s;
}
function distance($lat1, $lon1, $lat2, $lon2){
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $kilometer = $miles * 1.609344;
  $kilometer = round($kilometer);
  return $kilometer; 
}
function userStatusIcon($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access,fake,online_day FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	$today = date('w');
	if($st->last_access+300 >= time() || $st->fake == 1 && $st->online_day == $today){
		$s = '<i class="mdi-image-brightness-1" style="color:#17d425"></i>';
	} else {
		$s = '';
	}
	return $s;
}
function userStatusSpotlight($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = '<i class="mdi-image-brightness-1 online" ></i>';
	} else {
		$s = '';
	}
	return $s;
}
function userStatusSpotlightMobile($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = '<div class="online"></div>';
	} else {
		$s = '';
	}
	return $s;
}
function userStatusMessagesMobile($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = '<div style="	position:absolute;top:16px;left:48px;width:12px;height:12px;border-radius:50%;background:#2acf2a;border: 2px solid #fff;z-index:99999;"></div>';
	} else {
		$s = '';
	}
	return $s;
}
function userFilterStatus($uid) {
	global $mysqli,$sm;
	$today = date('w');
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access,fake,online_day FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time() || $st->fake == 1 && $st->online_day == $today){
		$s = 1;
	} else {
		$s = 0;
	}
	return $s;
}
function searchUser($q) {
	global $mysqli,$sm;
	$search = '';
	$query = $mysqli->query("SELECT id FROM users where id = '".$q."' OR name LIKE '%$q%' OR email LIKE '%$q%' LIMIT 10");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->id,6);
			$search .= ' <tr>
							  <td class="man-photos"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'"></td>						
							  <td>'.$sm['search']['id'].'</td>				  
							  <td>'.$sm['search']['name'].' , '.$sm['search']['age'].'
							  '; if($sm['search']['last_access'] >= $time_now) {
							  	$search .= ' <i class="fa fa-circle text-success" style="font-size:8px;"></i>';
							  }
							  $search .= '
							  </td>						  
							  <td>'.$sm['search']['email'].'</td>
							  <td>'.$sm['search']['city'].'</td>
							  <td>'.$sm['search']['country'].'</td>					  
							  <td>'.$sm['search']['credits'].'</td>
							  <td>'.$sm['search']['total_photos'].'</td>
							  <td>'.$sm['search']['join_date'].'</td>					  
							  <td><a href="index.php?page=profile&id='.$sm['search']['id'].'" target="_blank" class="label label-info">View</a> 
							  <a href="index.php?page=admin&p=user&id='.$sm['search']['id'].'" target="_blank" class="label label-primary">Edit</a></td>					
							</tr>';
		}
	}
	return $search;			
}
function getOnlineUsers() {
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT id FROM users WHERE last_access >='".$time_now."' LIMIT 10");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->id,6);
			$search.='<li style="width:100px;height:100px;">
                      <div class="profile-photo" style="border-radius:50%;" data-src="'.$sm['search']['profile_photo'].'" ></div>
                      <a class="users-list-name" href="index.php?page=admin&p=user&id='.$sm['search']['id'].'">'.$sm['search']['first_name'].'</a>
                    </li>';
		}
	}
	return $search;
}
function getLatestUsersProfilePhoto() {
	global $mysqli,$sm;
	$search = '';
	$i=0;
	$query = $mysqli->query("SELECT thumb,u_id FROM users_photos where profile = 1 ORDER BY id DESC LIMIT 6");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->u_id,6);
			$search.='<div class="photo" style="background-image: url('.$sm['search']['profile_photo'].')"></div>';
		}
	}
	return $search;
}
function getLatestUsers() {
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT id FROM users ORDER BY id DESC LIMIT 10");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->id,6);
			$search.='<li style="width:100px;height:100px;">
                      <div class="profile-photo" style="border-radius:50%;" data-src="'.$sm['search']['profile_photo'].'" ></div>
                      <a class="users-list-name" href="index.php?page=admin&p=user&id='.$sm['search']['id'].'">'.$sm['search']['first_name'].'</a>
                    </li>';
		}
	}
	return $search;
}
function getTotalUsers($value) {
	global $mysqli;
	if($value == 1) {
		$add = "";
	}
	if($value == 2) {
		$add = "WHERE last_access >=1";
	}
	if($value == 3) {
		$time_now = time()-300;
		$add = "WHERE last_access >=".$time_now;
	}	
	$query = $mysqli->query("SELECT count(*) as total FROM users $add");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getTotalPhotos($value) {
	global $mysqli;
	if($value == 1) {
		$add = "";
	}
	if($value == 2) {
		$add = "WHERE aprovada = 0";
	}	
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos $add");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getTotalPhotosReview() {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where approved = 0");
	if($query->num_rows > 0){
	$total = $query->fetch_assoc();
	} else {
		$total['total'] = 0;	
	}
	return $total['total'];
}
function getTotalPhotosPrivate() {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where blocked = 1");
	if($query->num_rows > 0){
	$total = $query->fetch_assoc();
	} else {
		$total['total'] = 0;	
	}
	return $total['total'];
}
function getPhotosReview() {
	global $mysqli;
	$r = '';
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved = 0 ORDER BY id DESC LIMIT 20");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<li style="width:10%;height:100px;">
		   			  <div class="profile-photo" data-src="'.$result->thumb.'" data-psrc="'.$result->photo.'" data-review="'.$result->id.'"></div>
                    </li>';	
		}
	}
	return $r;
}
function getPhotosUser($uid) {
	global $mysqli;
	$r = '';
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved = 1 and u_id = '".$uid."' ORDER BY id DESC LIMIT 20");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<li style="width:20%;height:100px;">
		   			  <div class="profile-photo" data-src="'.$result->thumb.'" data-psrc="'.$result->photo.'" data-review="'.$result->id.'"></div>
                    </li>';	
		}
	}
	return $r;
}
function getAdminPhotos($f,$p) {
	global $mysqli;
	$r = '';
	if($f == 1){
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved <> 2 ORDER BY id DESC LIMIT $p,50");
	}
	if($f == 2){
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved = 0 ORDER BY id DESC LIMIT 50");
	}
	if($f == 3){
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where blocked = 1 and approved <> 2 ORDER BY id DESC LIMIT 50");
	}	
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<li style="width:20%;height:200px;">
		   			  <div class="profile-photo" data-src="'.$result->thumb.'" data-psrc="'.$result->photo.'" data-review="'.$result->id.'"></div>
                    </li>';	
		}
	}
	return $r;
}
function updateThemeSetting($theme) {
	global $mysqli,$sm;
	$result=array();
	$query = $mysqli->query("SELECT * FROM theme_settings where theme = '".$theme."'");
	if($query->num_rows > 0 ){
		while($row = $query->fetch_assoc()){
			$result[] = $row;
		}
	}
	return $result;
}
function getAdminThemesLanding() {
	global $mysqli,$sm;
	$r = '';
	$current = siteConfig('theme_landing');
	$themeSetting = updateThemeSetting($current);
	$query = $mysqli->query("SELECT * FROM config_themes where type = 2");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$sm['config']['site_url'].$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme_landing">Select</button>';
				  }
				  if($result->has_settings == 1 && $result->folder == $current){
                    $r.='<button class="btn btn-primary" data-toggle="modal" data-target="#modal-'.$result->folder.'" style="float:right">Theme Settings</button>';
				  }					  
				  $r.='
                  </div>			
              </div>';
				  if($result->has_settings == 1 && $result->folder == $current){
				  	$j = "updateThemeSettings('".$result->folder."')";
                    $r.='<div class="modal fade" id="modal-'.$result->folder.'" style="display: none;">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title">'.$result->name.' - Settings</h4>
						      </div>
						      <div class="modal-body">';
						      foreach ($themeSetting as $key) {
						      	$r.='
				                    <div class="form-group">
				                      <label>'.$key['title'].'</label>
				                      <p style="color:#999">'.$key['info'].'</p>
				                      <input type="text" class="form-control" style="width:250px;"  value="'.$key['setting_val'].'" data-update-setting="'.$key['setting'].'"/>
				                    </div>
						      	';
						      }
						      $r.='</div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						        <button type="button" class="btn btn-primary" onClick="'.$j.'">Save changes</button>
						      </div>
						    </div>
						  </div>
						</div>';
				  }	              	
		}
	}
	return $r;
}
function getAdminThemesFrontEnd() {
	global $mysqli,$sm;
	$r = '';
	$current = siteConfig('theme');
	$themeSetting = updateThemeSetting($current);	
	$query = $mysqli->query("SELECT * FROM config_themes where type = 1 order by id asc");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$sm['config']['site_url'].$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme">Select</button>';
				  }
				  if($result->has_settings == 1 && $result->folder == $current){
                    $r.='<button class="btn btn-primary" data-toggle="modal" data-target="#modal-'.$result->folder.'" style="float:right">Theme Settings</button>';
				  }					  
				  $r.='
                  </div>			
              </div>';	
				  if($result->has_settings == 1 && $result->folder == $current){
				  	$j = "updateThemeSettings('".$result->folder."')";
                    $r.='<div class="modal fade" id="modal-'.$result->folder.'" style="display: none;">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title">'.$result->name.' - Settings</h4>
						      </div>
						      <div class="modal-body">';
						      foreach ($themeSetting as $key) {
						      	$r.='
				                    <div class="form-group">
				                      <label>'.$key['title'].'</label>';
				                      if($key['setting'] == 'card_design'){
				                      	$r.='<p style="color:#999">'.$key['info'].' <br> Current card design selected: <b>'.$key['setting_val'].'</b></p><select class="form-control" style="width:200px;" data-update-setting="'.$key['setting'].'"><option value="card1">Card 1 </option><option value="card2">Card 2 </option><option value="card3">Card 3 </option></select>';
				                      }else {
				                      	$r.='<p style="color:#999">'.$key['info'].'</p><input type="text" class="form-control" style="width:200px;"  value="'.$key['setting_val'].'" data-update-setting="'.$key['setting'].'"/>';
				                      }
				                    $r.='</div>
						      	';
						      }
						      $r.='</div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						        <button type="button" class="btn btn-primary" onClick="'.$j.'">Save changes</button>
						      </div>
						    </div>
						  </div>
						</div>';
				  }	              
		}
	}
	return $r;
}
function getAdminThemesMobile() {
	global $mysqli,$sm;
	$r = '';
	$current = siteConfig('theme_mobile');
	$query = $mysqli->query("SELECT * FROM config_themes where type = 3");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$sm['config']['site_url'].$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme_mobile">Select</button>';
				  }
				  if($result->has_settings == 1){
                    $r.='<button class="btn btn-primary" style="float:right">Theme Settings</button>';
				  }			  
				  $r.='
                  </div>			
              </div>';	
		}
	}
	return $r;
}
function getAdminThemesEmail() {
	global $mysqli;
	$r = '';
	$current = siteConfig('theme_email');
	$query = $mysqli->query("SELECT * FROM config_themes where type = 4");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme_email">Select</button>';
				  }
				  $r.='
                  </div>			
              </div>';	
		}
	}
	return $r;
}
function getTotalVideocalls($value) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM videocall");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getActiveVideocalls() {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_videocall where status = 2 ");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function checkMessageRead($uid1,$uid2) {
	global $mysqli,$sm;
	$query = $mysqli->query("SELECT seen FROM chat where s_id = '".$uid1."' and r_id = '".$uid2."' order by id desc LIMIT 1");
	if($query->num_rows == 0){
		$r = 0;
	} else {
		$read = $query->fetch_object();
		$r = $read->seen;
	}
	return $r;
}
function checkUnreadMessages($uid){
	global $mysqli;
	$query = $mysqli->query("SELECT count(id) as total FROM chat WHERE r_id = '".$uid."' AND seen = 0");	
	$total = $query->fetch_assoc();
	return $total['total'];	
}
function checkUnreadMessagesCount($rid,$sid){
	global $mysqli;
	$query = $mysqli->query("SELECT count(id) as total FROM chat WHERE r_id = '".$rid."' AND s_id = '".$sid."' AND seen = 0");	
	$total = $query->fetch_assoc();
	return $total['total'];	
}
function getLangName($lid) {
	global $mysqli,$sm;
	$query = $mysqli->query("SELECT name FROM languages where id = '".$lid."'");
	$read = $query->fetch_object();
	return $read->name;
}
function checkUserLang($lang){
	global $mysqli,$sm;
	$query = $mysqli->query("SELECT id FROM languages where prefix = '".$lang."'");
	if($query->num_rows >= 1) {
		$result = $query->fetch_object();
		$ret = $result->id;
	} else {
		$ret = $sm['config']['lang'];	
	}
	return $ret;	
}
function getOldLang($lang){
	global $mysqli,$sm;
	$query = $mysqli->query("SELECT name FROM languages where id = '".$lang."'");
	if($query->num_rows >= 1) {
		$result = $query->fetch_object();
		$ret = $result->name;
	} else {
		$ret = 'error';	
	}
	return $ret;	
}
function getSiteLangs($no){
	global $mysqli,$sm;
	$ret = '';
	$query = $mysqli->query("SELECT * FROM languages WHERE id <> '$no' and visible = 1 ORDER BY id ASC");
	if($query->num_rows >= 1) {
		while($result = $query->fetch_object()){
			$ret.='<option value="'.$result->id.'"> '.$result->name.' </option>';
		}
	}
	return $ret;	
}
function getSiteGenders(){
	global $mysqli,$sm;
	$ret = array();
	$query = $mysqli->query("SELECT * FROM config_genders ORDER BY id DESC");
	if($query->num_rows >= 1) {
		while($result = $query->fetch_object()){
			$ret[] = array(
				"id" => $result->id, 
				"name" =>$result->name,
				"lang" =>$result->lang_id,
				"sex" =>$result->sex
			);
		}
	}
	return $ret;	
}
function getGenderSex($g) {
	global $mysqli,$sm;
	$r = 2;
	$query = $mysqli->query("SELECT sex FROM config_genders where id = '".$g."' LIMIT 1");
	if($query->num_rows == 1) {
		$gender = $query->fetch_object();	
		$r = $gender->sex;
	}
	return $r;
}
function getGenderName($g) {
	global $mysqli,$sm;
	$r = '';
	$query = $mysqli->query("SELECT name FROM config_genders where id = '".$g."' LIMIT 1");
	if($query->num_rows == 1) {
		$gender = $query->fetch_object();	
		$r = $gender->name;
	}
	return $r;
}
function getSiteLangsIndex($start,$finish){
	global $mysqli,$sm;
	$ret = '';
	$query = $mysqli->query("SELECT * FROM languages where visible = 1 ORDER BY id ASC LIMIT $start,$finish");
	if($query->num_rows >= 1) {
		while($result = $query->fetch_object()){
		$ret.= '<td data-lang="'.$result->id.'">'.$result->name.'</td>';
		}
	}
	return $ret;	
}
function getUserId($tid) {
	global $mysqli,$sm;
	$query = $mysqli->query("SELECT id FROM users where twitter_id = '".$tid."'");
	$user = $query->fetch_object();
	return $user->id;
}
function getCurrentLang($id) {
	global $mysqli,$sm;
	$query = $mysqli->query("SELECT name FROM languages where id = '".$id."'");
	$lang = $query->fetch_object();
	return $lang->name;
}
function getUserInfo($uid,$value=0) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT * FROM users WHERE id = '".$uid."' OR username = '".$uid."'");
	$u = $user->fetch_object();	
	$first_name = explode(' ',trim($u->name));	
	$first_name = explode('_',trim($first_name[0]));
	
	$storyFrom = $sm['plugins']['story']['days'];
	$time = time();	
	$extra = 86400 * $storyFrom;
	$storyFrom = $time - $extra;

	$storiesFilter = 'where uid = '.$u->id.' and storyTime >'.$storyFrom.' and deleted = 0 and review = "No"';	

	if(isset($sm['user']['id'])){
		if($sm['user']['id'] == $uid){
			$storiesFilter = 'where uid = '.$u->id.' and storyTime >'.$storyFrom.' and deleted = 0';	
		}
	}

	if($u->fake == 0 && $value == 0){
		$arr = profileQuestion($u->lang,$u->gender);
		foreach($arr as $key=>$value){ 
			$arr[$key]['userAnswer'] = userProfileAnswer($u->id,$value['id']);
			$arr[$key]['answers'] = profileQuestionAnswer($value['id'],$u->lang);
		}
		$current_user['question'] = $arr;		
	}


	$creditsPercentage = getPercentage($sm['creditsPackages'][2]['credits'],$u->credits);
	$payout = ($sm['creditsPackages'][2]['price'] / 100) * $creditsPercentage;

	$percent = '0.'.$sm['plugins']['withdrawal']['rate'];
	$percent = (double)$percent;
	$payout = round($payout * $percent);

	if($value == 0){
		$sm['user']['id'] = $u->id;
	}

	$current_user['blockedProfiles'] = getArray('reports','WHERE reported_by = '.$u->id,'id DESC');
	$current_user['id'] = $u->id;
	
	$current_user['email'] = $u->email;
	$current_user['payout'] = $payout;	
	$current_user['pendingPayout'] = checkWithdrawExist($u->id);
	$current_user['gender'] = $u->gender;
	$current_user['app'] = $u->app_id;	
	$current_user['superlike'] = $u->superlike;		
	$current_user['guest'] = $u->guest;
	$current_user['bio_url'] = $u->bio_url;	
	$current_user['moderator'] = $u->moderator;
	$current_user['subscribe'] = $u->subscribe;		
	$current_user['facebook_id'] = $u->facebook_id;	

	if($sm['plugins']['settings']['onlyUsername'] == 'Yes'){
		if(empty($u->username)){
			$current_user['first_name'] = $u->id;	
			$current_user['name'] = $u->id;
		} else {
			$current_user['first_name'] = $u->username;	
			$current_user['name'] = $u->username;
		}
	} else {
		$current_user['first_name'] = $first_name[0];
		$current_user['name'] = $u->name;
	}
	$current_user['profile_photo'] = profilePhoto($u->id);
	$current_user['profile_photo_big'] = profilePhoto($u->id,1);
	$current_user['random_photo'] = randomPhoto($u->id);	
	
	$current_user['unreadMessagesCount'] = checkUnreadMessages($u->id);
	$current_user['story'] = selectC('users_story',$storiesFilter);
	$current_user['stories'] = json_encode(getUserStories($current_user['first_name'],$current_user['profile_photo'],$storiesFilter,'storyTime ASC'));
	$current_user['total_photos'] = getUserTotalPhotos($u->id);	
	$current_user['total_photos_public'] = getUserTotalPhotosPublic($u->id);		
	$current_user['total_photos_private'] = getUserTotalPhotosPrivate($u->id);		
	$current_user['total_likers'] = getUserTotalLikers($u->id);
	$current_user['total_nolikers'] = getUserTotalNoLikers($u->id);	
	$current_user['mylikes'] = totalLikes($u->id);	
	$current_user['totalLikes'] = $current_user['total_likers'] + $current_user['total_nolikers'];	
	$current_user['likes_percentage'] = getUserLikePercent($current_user['total_likers'],$current_user['totalLikes']);
	$current_user['galleria'] = getUserPhotosAllProfile($u->id);
	$current_user['total_likes'] = getUserTotalLikes($u->id);	
	$current_user['extended'] = userExtended($u->id);
	$current_user['interest'] = userInterest($u->id);	
	$current_user['status_info'] = userFilterStatus($u->id);
	$current_user['status'] = userStatus($u->id);	
	$current_user['city'] = $u->city;
	$current_user['email_verified'] = $u->verified;	
	$current_user['country'] = $u->country;	
	$current_user['age'] = $u->age;
	$current_user['paypal'] = $u->paypal;	
	$current_user['lat'] = $u->lat;
	$current_user['lng'] = $u->lng;	
	$current_user['birthday'] = $u->birthday;
	$current_user['registerReward'] = getData('users_rewards','reward','WHERE uid = '.$u->id.' AND reward = "newAccountFreeCredit"');	
	$current_user['last_access'] = $u->last_access;	
	$current_user['admin'] = $u->admin;
	if(empty($u->username)){
		$current_user['username'] = $u->id;	
	} else {
		$current_user['username'] = $u->username;	
	}	
	$current_user['lang'] = $u->lang;	
	$current_user['language'] = getLangName($u->lang);
	$current_user['looking'] = $u->looking;	
	$current_user['premium'] = $u->premium;

	$current_user['newFans'] = selectC('users_likes','where u2 = '.$u->id.' and notification = 0');
	$current_user['newVisits'] = selectC('users_visits','where u1 = '.$u->id.' and notification = 0');
	$current_user['totalVisits'] = selectC('users_visits','where u1 = '.$u->id);
	$current_user['totalMyLikes'] = selectC('users_likes','where u1 = '.$u->id.' and love = 1');
	$current_user['totalFans'] = selectC('users_likes','where u2 = '.$u->id.' and love = 1');
	$current_user['totalMatches'] = userMatchesCount($u->id);
	$current_user['ip'] = $u->ip;	
	$current_user['premium_check'] = adminCheckUserPremium($uid);		
	$current_user['verified'] = $u->verified;	
	$current_user['popular'] = $u->popular;
	$current_user['credits'] = $u->credits;	
	$clean = clean($first_name[0]);
	if($clean == ''){
		$clean = 'user';
	}
	$current_user['link'] = $clean;		
	$current_user['status'] = userStatus($u->id);
	$current_user['online'] = userStatusIcon($u->id);	
	$current_user['fake'] = $u->fake;
	$current_user['join_date'] = $u->join_date;	
	$current_user['bio'] = $u->bio;
	$current_user['meet'] = $u->meet;	
	$current_user['discover'] = $u->discover;	
	$current_user['s_gender'] = $u->s_gender;	
	$current_user['s_radius'] = $u->s_radious;
	$current_user['s_age'] = $u->s_age;	
	$current_user['twitter_id'] = $u->twitter_id;	
	$current_user['google_id'] = $u->google_id;
	$current_user['instagram_id'] = $u->instagram_id;		
	$current_user['facebook_id'] = $u->facebook_id;		
	$current_user['online_day'] = $u->online_day;			
	if($value == 1){
		$sm['profile'] = $current_user; 
	} else if($value == 2){
		$sm['meet'] = $current_user;	
	} else if($value == 3){
		$sm['chat'] = $current_user;	
	}	else if($value == 4){
		$sm['friend'] = $current_user;	
	} else if($value == 5){
		$sm['videocall'] = $current_user;	
	} else if($value == 6){
		$sm['search'] = $current_user;	
	} else if($value == 7){
		$sm['manage'] = $current_user;	
	} else if($value == 8){
		$sm['edit'] = $current_user;	
	} else if($value == 9){
		$sm['comment'] = $current_user;	
	} else if($value == 10){
		$sm['suggest'] = $current_user;	
	}  else if($value == 11){
		$sm['gift'] = $current_user;	
	}  else if($value == 12){
		$sm['mail'] = $current_user;	
	} else{
		$sm['user'] = $current_user;	
	}
}


function testMailNotification(){
	global $mysqli,$sm,$lang;
	$message = getMailPage('test');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}		
	$mail = new PHPMailer(true);
	try {	
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 

		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );	

		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($sm['user']['email'],$sm['user']['name']);
		$mail->Subject = "Test smtp email";
		$mail->msgHTML($message);

		$mail->send();
		return 'Test Email sended to '. $sm['user']['email'];
	} catch (Exception $e) {
	    return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}	
}
function chatMailNotification($user_id,$email_message,$s_id=0){
	global $mysqli,$sm;
	getUserInfo($user_id,12);
	$senderName = getData('users','name','where id ='.$s_id);
	$senderPhoto = profilePhoto($s_id);
	$message = getMailPage('message');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}
	$message = str_replace('$profile_name', $senderName, $message);
	$message = str_replace('$profile_photo', $senderPhoto, $message);
	$message = str_replace('$user_name', $sm['mail']['name'], $message);
	$message = str_replace('$site_open', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['config']['email'], $message);	
	$message = str_replace('$site_logo', $sm['config']['logo'], $message);	
	$message = str_replace('$email_theme_url', $sm['config']['site_url'].'themes/email/img/', $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}	

	$mail = new PHPMailer(true);
	try {
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';	
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                             
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
		$mail->Subject = $sm['lang'][353]['text']." ".$senderName;
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Chat mail notification to '.$sm['mail']['email'].' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Chat mail notification to '.$sm['mail']['email'].' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}

}
function fanMailNotification($sexy_id){
	global $mysqli,$sm;
	getUserInfo($sexy_id,12);
	$message = getMailPage('crush');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}		
	$message = str_replace('$profile_name', $sm['user']['name'], $message);
	$message = str_replace('$profile_photo', $sm['user']['profile_photo'], $message);
	$message = str_replace('$site_open', $sm['config']['site_url'].'fans', $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_logo', $sm['config']['logo'], $message);	
	$message = str_replace('$email_theme_url', $sm['config']['site_url'].'themes/email/img/', $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}

	$mail = new PHPMailer(true);
	try {	
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                              
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
		$mail->Subject = $sm['user']['name']." ".$sm['lang'][659]['text'];
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Like mail notification to '.$sm['mail']['email'].' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Like mail notification to '.$sm['mail']['email'].' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}

}
function forgotMailNotification($name,$email,$link){
	global $mysqli,$sm;
	$message = getMailPage('forgot');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}		
	$message = str_replace('$site_open', $link, $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_logo', $sm['config']['logo'], $message);	
	$message = str_replace('$email_theme_url', $sm['config']['site_url'].'themes/email/img/', $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}

	$mail = new PHPMailer(true);
	try {	
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                             
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($email,$name);
		$mail->Subject = $sm['config']['name'];
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Recover password email to '.$email.' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Recover password email to '.$email.' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}

}
function matchMailNotification($sexy_id){
	global $mysqli,$sm;
	getUserInfo($sexy_id,12);
	$message = getMailPage('match');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}		
	$message = str_replace('$profile_name', $sm['user']['name'], $message);
	$message = str_replace('$profile_photo', $sm['user']['profile_photo'], $message);
	$message = str_replace('$site_open', $sm['config']['site_url'].'matches', $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_logo', $sm['config']['logo'], $message);	
	$message = str_replace('$email_theme_url', $sm['config']['site_url'].'/themes/email/img/', $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer(true);
	try {	
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                             
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
		$mail->Subject = $sm['user']['name'].' - '.$sm['lang'][350]['text'];
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Match mail notification to '.$sm['mail']['email'].' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Match mail notification to '.$sm['mail']['email'].' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}

}
function nearMailNotification($sexy_id,$photo){
	global $mysqli,$sm,$lang;
	getUserInfo($sexy_id,12);
	$sm['lang'] = siteLang($sm['mail']['lang']);	
	$message = getMailPage('near');
	$message = str_replace('$sexy_name', $sm['mail']['name'], $message);
	$message = str_replace('$sexy_email', $sm['mail']['email'], $message);
	$message = str_replace('$user_name', $sm['user']['name'], $message);
	$message = str_replace('$user_age', $sm['user']['age'], $message);
	$message = str_replace('$user_photo', $photo, $message);	
	$message = str_replace('$user_url', $sm['config']['site_url']."index.php?page=profile&id=".$sm['user']['id'], $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['plugins']['settings']['siteEmail'], $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer(true);
	try {
		$mail->CharSet = "UTF-8";	
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                              
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
		$mail->Subject = $sm['user']['name']." ".$sm['elang'][44]['text'];
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Near mail notification to '.$sm['mail']['email'].' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Near mail notification to '.$sm['mail']['email'].' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}
}
function welcomeMailNotification($user_name,$user_email,$user_password){
	global $mysqli,$sm,$lang;
	$message = getMailPage('welcome');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}	
	$message = str_replace('$user_name', $user_name, $message);
	$message = str_replace('$user_email', $user_email, $message);
	$message = str_replace('$user_pass', $user_password, $message);
	$message = str_replace('$site_logo', $sm['config']['logo'], $message);
	$message = str_replace('$randomPhoto1', randomPhotoUser($sm['user']['s_gender']), $message);
	$message = str_replace('$randomPhoto2', randomPhotoUser($sm['user']['s_gender']), $message);
	$message = str_replace('$randomPhoto3', randomPhotoUser($sm['user']['s_gender']), $message);		
	$message = str_replace('$site_open', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['plugins']['settings']['siteEmail'], $message);
	$message = str_replace('$email_theme_url', $sm['config']['site_url'].'themes/email/img/', $message);		
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer(true);
	try {	
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );		                           
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($user_email,$user_name);
		$mail->Subject = $sm['lang'][380]['text']." ".$sm['config']['name'];
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Welcome mail notification to '.$user_email.' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Welcome mail notification to '.$user_email.' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}

}
function welcomeMailVerification($user_name,$user_id,$user_email,$user_password){
	global $mysqli,$sm,$lang;
	$message = getMailPage('verification');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}
	$md1 = md5($user_name);
	$md2 = md5($user_email);
	$md3 = md5($user_id);
	$md4 = md5($user_password);
	$md5 = $md1.$md2.$md3.$md4;		
	$message = str_replace('$user_name', $user_name, $message);
	$message = str_replace('$site_logo', $sm['config']['logo'], $message);	
	$message = str_replace('$user_id', $user_id, $message);	
	$message = str_replace('$user_email', $user_email, $message);
	$message = str_replace('$md5', $md5, $message);	
	$message = str_replace('$user_password', $user_password, $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);
	$message = str_replace('$email_theme_url', $sm['config']['site_url'].'themes/email/img/', $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['plugins']['settings']['siteEmail'], $message);
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['elang'][$parsed]['text'], $message);	
	}
	$mail = new PHPMailer(true);
	try {	
		$mail->CharSet = "UTF-8";
		$mail->Encoding = 'base64';
		$mail->IsSMTP();
		$mail->Host = $sm['plugins']['email']['host']; 
		$mail->Port = $sm['plugins']['email']['port']; 
		if($sm['plugins']['email']['protection'] == 'TLS'){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;	
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;		
		}
		$mail->SMTPKeepAlive = true;
		$mail->SMTPAuth = true;     
        $mail->SMTPOptions   = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );                             
		$mail->Username = $sm['plugins']['email']['email'];                 
		$mail->Password = $sm['plugins']['email']['password'];                         	
		$mail->setFrom($sm['plugins']['settings']['siteEmail'], $sm['plugins']['settings']['siteName']);
		$mail->addAddress($user_email,$user_name);
		$mail->Subject = $sm['config']['name'];
		$mail->msgHTML($message);
		$mail->send();

		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Welcome mail verification notification to '.$user_email.' successfully sent';
			activity('system',$activity,'Email Notification Sent');	
		}	
	} catch (Exception $e) {
		if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
			$activity = 'Welcome mail verification notification to '.$user_email.' could not be sent, Error: {$mail->ErrorInfo}';
			activity('system',$activity,'Email could not be sent');
		}
	}	
}
function sitePrices() {
	global $mysqli;
	$result=array();
	$config = $mysqli->query("SELECT * FROM config_prices");
	if($config->num_rows > 0 ){
		while($row = $config->fetch_assoc()){
			$result[$row['feature']] = $row['price'];
		}
	}
	return $result;
}
function userExtended($id) {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM users_extended where uid = '".$id."'");
	$result = $config->fetch_assoc();
	return $result;
}
function userInterest($id) {
	global $mysqli,$sm;
	$result=array();
	$config = $mysqli->query("SELECT * FROM users_interest where u_id = '".$id."'");
	if($config->num_rows > 0 ){
		while($row = $config->fetch_assoc()){
			$result[$row['i_id']] = array(
				"id" => $row['i_id'],
				"name" => getData('interest','name','where id ='.$row['i_id']),
				"icon" => getData('interest','icon','where id ='.$row['i_id'])
			);
		}
	}
	return $result;	
}
function availableLanguages() {
	global $mysqli,$sm;
	$result=array();
	$config = $mysqli->query("SELECT * FROM languages where visible = 1");
	if($config->num_rows > 0 ){
		while($row = $config->fetch_assoc()){
			$result[] = $row['prefix'];
		}
	}
	return $result;	
}
function selectLanguages() {
	global $mysqli,$sm;
	$result=array();
	$config = $mysqli->query("SELECT * FROM languages where visible = 1");
	if($config->num_rows > 0 ){
		$i=0;
		while($row = $config->fetch_assoc()){
			$result[$i]['prefix'] = $row['prefix'];
			$result[$i]['id'] = $row['id'];
			$result[$i]['name'] = $row['name'];
			$result[$i]['text'] = $row['name'];
			$i++;
		}
	}
	return $result;	
}
function configEmail() {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM config_email");
	$result = $config->fetch_assoc();
	return $result;
}
function userNotifications($uid) {
	global $mysqli,$sm;
	$result = array();
	$query = $mysqli->query("SELECT * FROM users_notifications where uid = '".$uid."'");
	while($row = $query->fetch_assoc()){

		$likes = explode(',', $row['fan']);
		$result['fan'] = array(
			"email" => $likes[0], 
			"push" => $likes[1], 
			"inapp" => $likes[2]			
		);

		$match = explode(',', $row['match_me']);
		$result['match_me'] = array(
			"email" => $match[0], 
			"push" => $match[1], 
			"inapp" => $match[2]			
		);

		$near = explode(',', $row['near_me']);
		$result['near_me'] = array(
			"email" => $near[0], 
			"push" => $near[1], 
			"inapp" => $near[2]			
		);

		$message = explode(',', $row['message']);
		$result['message'] = array(
			"email" => $message[0], 
			"push" => $message[1], 
			"inapp" => $message[2]			
		);								
	}
	return $result;
}
function siteAccountsBasic() {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM config_accounts where type = 1");
	$result = $config->fetch_assoc();
	return $result;
}
function userData($val,$id) {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM users where id = $id");
	$result = $config->fetch_object();
	return $result->$val;
}
function siteAccountsPremium() {
	global $mysqli,$sm;
	$config = $mysqli->query("SELECT * FROM config_accounts where type = 2");
	$result = $config->fetch_assoc();
	return $result;
}
function siteGenders($lang) {
	global $mysqli,$sm;
	$result=array();
	$query= $mysqli->query("SELECT * FROM config_genders where lang_id = '".$lang."' order by id asc");
	while($row = $query->fetch_assoc()){
		$result[] = array(
			"id" => $row['id'], 
			"text" => $row['name']
		);
	}	
	return $result;
}
function profileQuestion($lang,$gender=0) {
	global $mysqli,$sm;
	$result=array();

	$filter = 'lang_id = '.$lang;
	if($gender != 0){
		$filter = 'lang_id = '.$lang.' AND gender = '.$gender.' || lang_id = '.$lang.' AND gender = 0';
	}
	$query= $mysqli->query("SELECT * FROM config_profile_questions where $filter order by q_order asc");
	if(isset($query->num_rows) && !empty($query->num_rows)){	
		while($row = $query->fetch_assoc()){
			$result[] = array(
				"id" => $row['id'], 
				"question" => $row['question'], 
				"method" => $row['method'],
				"gender" => $row['gender'],
				"q_order" => $row['q_order']			
			);
		}	
	}
	return $result;
}
function userProfileAnswer($uid,$qid,$lang=0) {
	global $mysqli,$sm;
	$r = '';
	$profileLang = getData('users','lang','WHERE id = '.$uid);
	$config = $mysqli->query("SELECT answer,qid FROM users_profile_questions where uid = '".$uid."' AND qid = '".$qid."'");
	if($config->num_rows > 0 ){
		$result = $config->fetch_object();
		$r = $result->answer;

		if($lang != 0 && $profileLang != $lang){
			$a = getData('config_profile_answers','id','WHERE answer = "'.$r.'"');
			$r = getData('config_profile_answers','answer','WHERE id = '.$a.' AND lang_id = '.$lang);	
		}
		
	}
	return $r;
}
function profileQuestionAnswer($q,$lang) {
	global $mysqli,$sm;
	$result=array();
	$query= $mysqli->query("SELECT * FROM config_profile_answers where lang_id = '".$lang."' and qid = '".$q."' order by id asc");
	while($row = $query->fetch_assoc()){
		$result[] = array(
			"id" => $row['id'], 
			"answer" => $row['answer'],
			"text" => $row['answer']					
		);
	}	
	return $result;
}
function updateUserFilter($looking,$age,$radius,$uid){
	global $mysqli;	
$mysqli->query("UPDATE users SET s_gender = '".$looking."', s_age = '".$age."', s_radious = '".$radius."' where id = '".$uid."'");	
}
function siteLang($lang) {
	global $mysqli,$sm;
	$result=array();
	$lang = secureEncode($lang);	
	$query = $mysqli->query("SELECT * FROM site_lang where lang_id = '".$lang."' ORDER BY id ASC");
	while($row = $query->fetch_assoc()){
		if($row['id'] == 814){
			$row['text'] = str_replace('[site.name]', $sm['config']['name'], $row['text']);
		}
		$result[$row['id']] = array(
			"id" => $row['id'], 
			"text" => $row['text']
		);
	}	
	return $result;
}
function totalFans($uid1,$n=1) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u2 = '".$uid1."' and love = 1 AND notification <= '".$n."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}
function totalMatches($uid1,$n=1) {
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$i= 0;
	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$sm['user']['id']."' AND love = 1");
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			getUserInfo($row->u1,6);
			if(isFan($sm['user']['id'],$sm['search']['id']) == 1){
				$i++;	
			}
		}
	}
	return $i;
}
function totalUsersCity($city) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users where city = '".$city."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}
function totalLikes($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u1 = '".$uid."' and love = 1");
	$total = $query->fetch_assoc();
	return $total['total'];
}

function totalVisits($uid1,$n=1) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_visits where u1 = '".$uid1."' AND notification < '".$n."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}

function getUserVisitors($uid) {
	global $mysqli,$sm;
	$search = '';

	$time_now = time()-300;
	
	$query2 = $mysqli->query("SELECT u2,timeago,notification FROM users_visits where u1 = '$uid' and u2 <> '$uid' order by timeago desc");
	if($query2->num_rows > 0){
		while($result2 = $query2->fetch_object()){
			$time = $result2->timeago;
			$viewed = $result2->notification;
			$data = $time.','.$viewed;
			$search.= meetCards($result2->u2,'visit',$data);
		}
	} else {
		$search.= '
			<center data-chat-result="0" style="margin-bottom: 50px;padding-top: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['discover_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['default_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].'">'.$sm['lang'][301]['text'].'</h5>
				</strong>
				<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].';opacity: .5">
					'.$sm['lang'][597]['text'].'
				</h5>
			</center>';
	}
	$mysqli->query("UPDATE users_visits set notification = 1 where u1 = '$uid' and u2 <> '$uid'");
	return $search;
}

function getUserVisitorsCount($uid) {
	global $mysqli,$sm;

	$i= 0;	
	$query2 = $mysqli->query("SELECT u2,timeago,notification FROM users_visits where u1 = '$uid' and u2 <> '$uid' order by timeago desc");
	if($query2->num_rows > 0){
		$i = $query2->num_rows;
	}
	return $i;
}

function userMatches(){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$i= 0;
	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$sm['user']['id']."' AND love = 1");			
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			
			getUserInfo($row->u1,6);
			if(isFan($sm['user']['id'],$row->u1) == 1){
				$i++;	
				$search.= meetCards($row->u1);
												
			}
		}
	}
	if($i == 0){
		$search.= '
			<center data-chat-result="0" style="margin-bottom: 50px;padding-top: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['discover_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['default_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].'">'.$sm['lang'][157]['text'].'</h5>
				</strong>
			</center>';
	}
	return $search;
}


function userMatchesCount($id){
	global $mysqli,$sm;

	$i= 0;
	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$id."' AND love = 1");			
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			if(isFan($id,$row->u1) == 1){
				$i++;									
			}
		}
	}
	return $i;
}

function checkUserPremium($uid){
	global $mysqli,$sm;
	$time = time();
	$query = $mysqli->query("SELECT premium FROM users_premium WHERE uid = '".$uid."'");
	if($query->num_rows> 0){
		$user = $query->fetch_object();
		if($user->premium > $time){
			$mysqli->query("UPDATE users SET premium = 1 where id = '".$uid."'");
			return $user->premium;
		} else {
			$mysqli->query("UPDATE users SET premium = 0 where id = '".$uid."'");
			return 0;
		}
	} else {
		return 0;
	}
}

function adminCheckUserPremium($uid){
	global $mysqli,$sm;
	$time = time();
	$query = $mysqli->query("SELECT premium FROM users_premium WHERE uid = '".$uid."'");
	if($query->num_rows > 0){
		$user = $query->fetch_object();
		if($user->premium > $time){
			$mysqli->query("UPDATE users SET premium = 1 where id = '".$uid."'");
			return $user->premium;
		} else {
			$mysqli->query("UPDATE users SET premium = 0 where id = '".$uid."'");
			return 0;
		}
	} else {
		return 0;
	}
}
function adminCheckDaysLeft($premium){
	return $date2 = date("Y-m-d",$premium);	
}
function likeSuggest($uid,$lang,$looking,$limit){
	global $mysqli,$sm;	
	$suggest = '';
	$i = 0;
	$query = $mysqli->query("SELECT id
	FROM users
	WHERE gender = '".$looking."'
	AND id <> '".$sm['user']['id']."'
	ORDER BY RAND()
	LIMIT $limit");
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			getUserInfo($row->id,10);
			$time = time();
			$i++;
			if($i > 3 ) {
			$suggest.= '<div class="follow-suggest" style="display:none;"><div class="profile-photo" data-src="'.$sm['suggest']['profile_photo'].'"></div> <h2 data-uid="'.$sm['suggest']['id'].'" data-ssurl="profile" style="cursor:pointer">'.$sm['suggest']['name'].'</h2><span><b>'.$sm['suggest']['total_likers'].'</b> '.$sm['lang'][278]['text'].'</span><a class="waves-effect  btn-flat" id="sl'.$time.'" data-uid="'.$sm['suggest']['id'].'" data-action="like" style="padding-left:10px; padding-right:10px;"><i class="mdi-action-favorite"></i></a></div>';				
			} else {
			$suggest.= '<div class="follow-suggest"><div class="profile-photo" data-src="'.$sm['suggest']['profile_photo'].'"></div> <h2 data-uid="'.$sm['suggest']['id'].'" data-ssurl="profile" style="cursor:pointer">'.$sm['suggest']['name'].'</h2><span><b>'.$sm['suggest']['total_likers'].'</b> '.$sm['lang'][278]['text'].'</span><a class="waves-effect  btn-flat" href="javascript:;" id="sl'.$time.'" data-uid="'.$sm['suggest']['id'].'" data-action="like" style="padding-left:10px; padding-right:10px;"><i class="mdi-action-favorite" ></i></a></div>';				
			}
		}	
	}
	return $suggest;
}
function blockedUser($u1,$u2){
	global $mysqli,$sm;
	$u1 = secureEncode($u1);
	$u2 = secureEncode($u2);
	$return = 0;
	$check = $mysqli->query("SELECT * FROM users_blocks where uid1 = '".$u1."' AND uid2 = '".$u2."' || uid2 = '".$u1."' AND uid1 = '".$u2."'");
	if($check->num_rows >= 1){
		$return = 1;
	}	
	return $return;
}

function visit($u1,$u2,$photo="",$name=""){
	global $mysqli,$sm;
	$time = time();		
	$mysqli->query("INSERT INTO users_visits (u2,u1,timeago) VALUES ('".$u1."','".$u2."','".$time."') ON DUPLICATE KEY UPDATE timeago = '".$time."'");

    $noti= 'visit'.$u2;
    $data['id'] = $u1;
    $data['message'] = $sm['alang'][252]['text'];
    $data['time'] = date("H:i", time());
    $data['type'] = 4;
    $data['icon'] = $sm['user']['profile_photo'];
    $data['name'] = $sm['user']['first_name'];      
    $data['photo'] = 0;
    $data['action'] = 'visit';
    $data['unread'] = checkUnreadMessages($u2);     
    if(is_numeric($sm['plugins']['pusher']['id'])){
    	$sm['push']->trigger($sm['plugins']['pusher']['key'], $noti, $data);	
    }  
    
	//activity content 
	if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
		$ac = array();
		$ac['u1']['id'] = $u1; 
		$ac['u2']['id'] = $u2; 
		$ac['u1']['name'] = $sm['user']['first_name']; 
		$ac['u2']['name'] = $name; 	
		$ac['u1']['photo'] = $sm['user']['profile_photo']; 
		$ac['u2']['photo'] = $photo;

		$adminPush= 'adminActivity';
		$pushData['visit'] = $ac;	
		$ac = json_encode($ac);
		activity('visit',$ac,'Profile visit '.$name,$u1);	 
		if(is_numeric($sm['plugins']['pusher']['id'])){      
	    	$sm['push']->trigger($sm['plugins']['pusher']['key'], $adminPush, $pushData);
		}
	}		    	
}
function meetFilter($uid,$lang,$looking,$age,$radius,$status=0,$l=0,$username=''){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$lat = $sm['user']['lat'];
	$lng = $sm['user']['lng'];
	$today = date('w');
	$limit = $l * $sm['plugins']['meet']['searchResult'];
	$searchResults = $sm['plugins']['meet']['searchResult'];
	$ages = "18,30";
	if($age == 1){$age1 = 18; $age2 = 23; $ages= "18,23,1";}
	if($age == 2){$age1 = 24; $age2 = 30; $ages= "24,30,2";}
	if($age == 3){$age1 = 31; $age2 = 50; $ages= "31,50,3";}
	if($age == 4){$age1 = 51; $age2 = 100; $ages= "51,100,4";}
	$e_age = explode( ',', $age );
	$age1 = $e_age[0];
	$age2 = $e_age[1];
	if($radius > 4500){
		$radius = 1000000;
	}
	$all = count($sm['genders']);
	$all = $all + 1;
	if($status == 0){
		$status_filter = "";	
	} else {
		$time_now = time()-300;
		if($looking == $all) {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
		} else {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
		}
	}	
	if($looking == $all) {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc
		LIMIT ".$limit.", $searchResults";	
		$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc";			
	} else {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc
		LIMIT ".$limit.", $searchResults";	
		$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc";			
	}

	if(!empty($username)){
		$query = "SELECT id
		FROM users
		WHERE name LIKE '%$username%' || username LIKE '%$username%' || email LIKE '%$username%'
		ORDER BY last_access desc
		LIMIT $limit, $searchResults";	
		$query2 = "SELECT id
		FROM users
		WHERE name LIKE '%$username%' || username LIKE '%$username%' || email LIKE '%$username%'
		ORDER BY last_access desc";
	}

	$result = $mysqli->query($query);
	$result2 = $mysqli->query($query2);
	$sm['meet_result'] = $result2->num_rows;

	
	$ad = array(12, 24, 36, 48, 62, 74);
	$delay = 0;
	if ($result->num_rows > 0) {
		$a = 0;
		while($row = $result->fetch_object()){
			$checkIfBlockedFilter = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$row->id;
			$checkIfBlocked = checkIfExistFilter('users_blocks',$checkIfBlockedFilter);				
			if($sm['user']['id'] != $row->id && $checkIfBlocked == 0){
				$delay++;
				$blur = 0;
				if($sm['plugins']['meet']['viewOnlyPremium'] == 'Yes' && $sm['user']['premium'] == 0){
					$blur = 1;
				}
				if($status != 0 && $sm['plugins']['meet']['viewOnlyPremiumOnline'] == 'Yes' && $sm['user']['premium'] == 0){
					$blur = 1;
				}				
				$search.= meetCards($row->id,'','',$delay,$blur);
			} else {
				$a--;
				$sm['meet_result'] = $sm['meet_result'] -1;
			}

			$a++;
			if (in_array($a, $ad)) {
				if($sm['plugins']['adsWeb']['enabled'] == 'Yes' && $sm['plugins']['adsWeb']['enable_728x90_meet'] == "Yes"){ 
					echo '<div class="ad-zone">'.$sm['plugins']['adsWeb']['728x90'].'</div>';
				}
			}			
		}
	} 
	if($sm['meet_result'] == 0) {
		$search.= '
			<center data-meet-result="0" style="margin-bottom: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['meet_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['meet_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['search_card_wall_color']['val'].'">'.$sm['lang'][153]['text'].'</h5>
				</strong>
				<h5 onclick="openFilterSearch()" style="color:'.$sm['theme']['search_card_wall_color']['val'].';opacity: .5;cursor:pointer">
					'.$sm['lang'][597]['text'].'
				</h5>
			</center>
		<center>';
	}
	$totalPages = ceil($sm['meet_result'] / $searchResults);
	$totalp = $totalPages-1;
	$search.= '<script>meet_pages = '.$totalp.'</script>';
	$search.= '<br><br><center><div class="pagination js-search-pager">
	';	
	for ($i=$l; $i<=$totalp; $i++) { 
		$d = $i-$l;
		$x = $i-1;
		if($d >9){
			break;	
		}
		$b = $i+1;
		if($l == $i){
			if($l >= 1){
				$search.= '<div class="btn btn--sm btn--white" data-meet="'.$x.'"> <span class="btn-txt">Prev.</span> </div>';				
			}
			$search.= '<div class="btn btn--sm btn--white btn--page active"><span class="btn-txt">'.$b.'</span></div>';				
		} else {
			$search.= '<div data-meet="'.$i.'"  class="btn btn--sm btn--white btn--page"><span class="btn-txt">'.$b.'</span></div>';		
		}
	};	
	updateUserFilter($looking,$age,$radius,$uid);
	$search.= '</div></center></div></div>';	
	return $search;
}

function meetCards($id,$section='',$sectionData='',$delay=0,$blur=0){
	global $sm,$mysqli;
	getUserInfo($id,6);
	$search = '';
	$time_now = time()-300;
	$today = date('w');	
	$sectionData = explode(',', $sectionData);
	$likeGame = 1;
	$delay = $delay * 50;


	$showGameBtn = '';
	$likedIcon ='<div class="like box-shadow vivify popIn" data-badge-like style="display:none" data-you-like="'.$sm['search']['id'].'" data-tooltip="'.$sm['lang'][613]['text'].'"><i class="mdi-action-favorite"></i></div>';

	$likeBtn = '<svg fill="auto" viewBox="0 0 32 32" width="100%" height="100%"><path fill="'.$sm['theme']['icon_like_color_discover']['val'].'" d="M16 12.06a3.85 3.85 0 0 0-3.12-1.61 3.94 3.94 0 0 0-3.88 4c0 3.6 4.69 8.13 7 8.13 2.31 0 7-4.53 7-8.14 0-2.2-1.74-3.99-3.88-3.99-1.24 0-2.4.6-3.12 1.61z"></path></svg>';

	$likeBtnBg = $sm['theme']['icon_like_bg_discover']['val'];
	if(isFan($sm['user']['id'],$sm['search']['id']) == 1){
		$likedIcon ='<div class="like box-shadow vivify popIn" data-badge-like data-you-like="'.$sm['search']['id'].'" data-tooltip="'.$sm['lang'][613]['text'].'">
		<i class="mdi-action-favorite"></i></div>';

		$likeGame = 0;
		$likeBtnBg = $sm['theme']['icon_nolike_bg_discover']['val'];
		$likeBtn = '<svg viewBox="0 0 60 60" style="width: 40px;height: 40px;">
            <polygon fill="'.$sm['theme']['icon_nolike_color_discover']['val'].'" points="45.82 16.12 43.7 14 29.91 27.79 16.12 14 14 16.12 27.79 29.91 14 43.7 16.12 45.82 29.91 32.03 43.7 45.82 45.82 43.7 32.03 29.91"></polygon>
        </svg>';						
	}

	$blured = '';
	$goToProfile = 'goToProfile('.$sm['search']['id'].')';
	$href = $sm['config']['site_url'].'@'.$sm['search']['username'];
	if($blur == 1){
		if($sm['plugins']['meet']['viewOnlyPremiumBlur'] == 'Yes' && $section != 'populars'){
			$blured = 'blured';
		}
		
		$goToProfile = "meetPremiumNotification(1);goTo('premium')";
		$showGameBtn = 'display:none';
		$href = $sm['config']['site_url'].'premium';
	}

	$city = $sm['search']['city'];
	if(empty($city)){
		$city = $sm['search']['country'];
	}

	$loc = '<div class="loc">
			<i class="mdi-maps-place"></i>
			<span style="color:'.$sm['theme']['search_card_color']['val'].'">'.$city.'</span>
			</div>';

	if($section == 'visit' || $section == 'fans' || $section == 'matches'){
		$loc = '<div class="loc">';
			if($sectionData[1] == 0){
				$loc.='<span style="color:'.$sm['theme']['btn_color']['val'].'">NEW</span> ';
			}		
			$loc.='<span style="color:'.$sm['theme']['search_card_color']['val'].'">
					'.time_elapsed_string($sectionData[0]);					
				$loc.='</span>
			</div>';
	}

	if($section == 'blocked'){
		$goToProfile = 'unblockUser('.$sm['search']['id'].')';
		$loc = '<div class="loc" style="margin-top:5px">';
			$loc.='<span onclick="unblockUser('.$sm['search']['id'].')" data-unblock-user="'.$sm['search']['id'].'" style="color:'.$sm['theme']['btn_color']['val'].';cursor:pointer">'.$sm['lang'][708]['text'].'</span>
		</div>';
	}	

	if($sm['theme']['search_card_gradient']['val'] == 'Yes'){
		$search.= '<div class="search  '.$sm['theme']['search_card_bg']['val'].'"  data-search-user-card="'.$sm['search']['id'].'" data-meet-user="meetLikeBtn'.$sm['search']['id'].'" style="cursor:pointer;"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div>';
		$search.= '
		<div class="profile-photo  '.$blured.' vivify fadeIn delay-'.$delay.'" onclick="event.preventDefault();'.$goToProfile.'" data-search-profile-link="'.$sm['search']['id'].'" data-src="'.$sm['search']['profile_photo'].'" ></div>';		
	} else {
		$search.= '<div class="search vivify fadeIn delay-'.$delay.'" style="background:'.$sm['theme']['search_card_bg']['val'].'" data-search-user-card="'.$sm['search']['id'].'" data-meet-user="meetLikeBtn'.$sm['search']['id'].'" style="cursor:pointer;"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div>';
		$search.= '
		<div class="profile-photo '.$blured.' vivify fadeIn delay-'.$delay.'" onclick="event.preventDefault();'.$goToProfile.'" data-search-profile-link="'.$sm['search']['id'].'" data-src="'.$sm['search']['profile_photo'].'" ></div>';		
	}

	if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
		$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="'.$href.'" data-meet-profile-link onclick="event.preventDefault();'.$goToProfile.'" data-search-profile-link="'.$sm['search']['id'].'" style="color:'.$sm['theme']['search_card_color']['val'].'">'.$sm['search']['first_name'].', '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1>'.$loc.'</div>';
	} else {
		$search.= '<div class="name"><h1><a style="color:'.$sm['theme']['search_card_color']['val'].';font-weight:700" href="'.$href.'" data-meet-profile-link onclick="event.preventDefault();'.$goToProfile.'" data-search-profile-link="'.$sm['search']['id'].'">'.$sm['search']['first_name'].', '.$sm['search']['age'].'</a></h1>'.$loc.'</div>';
	}
	$search.= '<div class="footer" style="'.$showGameBtn.'">'.$likedIcon;
	if($sm['search']['premium'] == 1){	
		$search.= '<div class="premium box-shadow" data-badge-premium data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
	}
	if($sm['search']['verified'] == 1){	
		$search.= '<div class="verified box-shadow" data-badge-verified data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
	}
	if($sm['search']['popular'] > 3000){	
		$search.= '<div class="popular box-shadow" data-badge-popular data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
	}			
	$search.= '</div><div style="'.$showGameBtn.'"><button data-meet-button="meetLikeBtn'.$sm['search']['id'].'" class="button-circular vivify popIn '.$likeBtnBg.'" data-like-game="'.$likeGame.'" data-id="'.$sm['search']['id'].'" style="opacity: 1; transform: translateY(0px);background: '.$likeBtnBg.';position:absolute;right: 32%;top: 25%;display:none;z-index:9">'.$likeBtn.'
			    </button></div>
			    </div>';
	echo $search;	
}

function engageUser($lat,$lng,$looking,$age){
	global $mysqli,$sm;
	$today = date('w');
	$ages = "18,30";
	if($age == 1){$age1 = 18; $age2 = 23; $ages= "18,23,1";}
	if($age == 2){$age1 = 24; $age2 = 30; $ages= "24,30,2";}
	if($age == 3){$age1 = 31; $age2 = 50; $ages= "31,50,3";}
	if($age == 4){$age1 = 51; $age2 = 100; $ages= "51,100,4";}
	$e_age = explode( ',', $age );
	$age1 = $e_age[0];
	$age2 = $e_age[1];
	$radius = 500;
	$all = count($sm['genders']);
	$all = $all + 1;
	if($status == 0){
		$status_filter = "";	
	} else {
		$time_now = time()-300;
		if($looking == $all) {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
		} else {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
		}
	}	
	if($looking == $all) {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY RAND()
		LIMIT 1";			
	} else {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY RAND()
		LIMIT 1";			
	}
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_object()){
			getUserInfo($row->id,1);
		}
	} 	
}
function onlineUsersRight($uid,$lang,$looking,$age,$radius,$status=0,$l=0){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$lat = $sm['user']['lat'];
	$lng = $sm['user']['lng'];
	$today = date('w');
	$limit = $l * 20;
	$ages = "18,30";
	if($age == 1){$age1 = 18; $age2 = 23; $ages= "18,23,1";}
	if($age == 2){$age1 = 24; $age2 = 30; $ages= "24,30,2";}
	if($age == 3){$age1 = 31; $age2 = 50; $ages= "31,50,3";}
	if($age == 4){$age1 = 51; $age2 = 100; $ages= "51,100,4";}
	$e_age = explode( ',', $age );
	$age1 = $e_age[0];
	$age2 = $e_age[1];
	if($radius > 4500){
		$radius = 1000000;
	}
	if($status == 0){
		$status_filter = "";	
	} else {
		$time_now = time()-300;
		if($looking == 3) {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
		} else {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
		}
	}	
	if($looking == 3) {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc
		LIMIT ".$limit.", 20";	
		$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc";			
	} else {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc
		LIMIT ".$limit.", 20";	
		$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access desc";			
	}
	$result = $mysqli->query($query);
	$result2 = $mysqli->query($query2);
	$sm['meet_result'] = $result2->num_rows;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_object()){
			if($sm['user']['id'] != $row->id){
				getUserInfo($row->id,6);
				$search.= '<div class="search" onclick="goToProfile('.$sm['search']['id'].')" style="cursor:pointer;"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
				<div class="profile-photo animated fadeIn" data-src="'.$sm['search']['profile_photo'].'" ></div>';
				if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
					$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="'.$sm['config']['site_url'].'profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
				} else {
					$search.= '<div class="name" ><h1><a href="'.$sm['config']['site_url'].'profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'"><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
				}
				$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
				if($sm['search']['premium'] == 1){	
					$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
				}
				if($sm['search']['verified'] == 1){	
					$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
				}
				if($sm['search']['popular'] > siteConfig('popular_user')){	
					$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
				}			
				$search.= '</div></div>';
			} else {
				$sm['meet_result'] = $sm['meet_result'] -1;
			}
		}
	} 
	if($sm['meet_result'] == 0) {
		$search.= '<center>
		<br><br><img src="'.$sm['config']['theme_url'].'/images/nothing_found.png">
		<h5 style="cursor:pointer;padding-top:65px;color:#666666;">'.$sm['lang'][153]['text'].'</h5>
		</center>';
	}
	$totalPages = ceil($sm['meet_result'] / 20);
	$totalp = $totalPages-1;
	$search.= '<script>meet_pages = '.$totalp.'</script>';
	$search.= '<br><br><center><div class="pagination js-search-pager">
	';	
	for ($i=$l; $i<=$totalp; $i++) { 
		$d = $i-$l;
		$x = $i-1;
		if($d >9){
			break;	
		}
		$b = $i+1;
		if($l == $i){
			if($l >= 1){
				$search.= '<div class="btn btn--sm btn--white" data-meet="'.$x.'"> <span class="btn-txt">Prev.</span> </div>';				
			}
			$search.= '<div class="btn btn--sm btn--blue btn--page active"><span class="btn-txt">'.$b.'</span></div>';				
		} else {
			$search.= '<div data-meet="'.$i.'"  class="btn btn--sm btn--white btn--page"><span class="btn-txt">'.$b.'</span></div>';		
		}
	};	
	updateUserFilter($looking,$age,$radius,$uid);
	$search.= '</div></center></div></div>';	
	return $search;
}
function meetFilterMobile($uid,$lang,$looking,$age,$radius,$status=0,$limi=0){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$lat = $sm['user']['lat'];
	$lng = $sm['user']['lng'];
	$today = date('w');
	$limit = $limi * 15;
	$x=0;
	$e_age = explode( ',', $age );
	$age1 = $e_age[0];
	$age2 = $e_age[1];
	$world = 0;
	$age = $age.',1';
	if($radius > 10000){
		$world = 1;
	}
	updateUserFilter($looking,$age,$radius,$uid);
	if($status == 0){
		$status_filter = "";	
	} else {
		$time_now = time()-300;
		if($looking == 3) {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
		} else {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
		}
	}	
	$country_filter = '';
	if($radius < 3000){
		$country_filter = "AND country = '".$sm['user']['country']."'";			
	}
	if($looking == 3) {
		if($world == 1){
			$query = "SELECT id
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'
			$country_filter			
			$status_filter	
			ORDER BY last_access
			LIMIT ".$limit.", 20";	
			$query2 = "SELECT id
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			ORDER BY last_access";			
		} else {
			$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access
			LIMIT ".$limit.", 20";	
			$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access";			
		}
	} else {
		if($world == 1){
			$query = "SELECT id
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			ORDER BY last_access
			LIMIT ".$limit.", 20";	
			$query2 = "SELECT id
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'
			$country_filter			
			$status_filter	
			ORDER BY last_access";			
		} else {
			$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'
			$country_filter			
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access
			LIMIT ".$limit.", 20";	
			$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access";			
		}
	}
	$result = $mysqli->query($query);
	$result2 = $mysqli->query($query2);
	$sm['meet_result'] = $result2->num_rows;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_object()){
			getUserInfo($row->id,6);
			$x++;
			if($sm['user']['premium'] == 0 && $sm['basic']['mobile_ads'] == 1 && $x==7){
				$search.= '<center><div style="width:100%;background:#f3f3f3;color:#488ad8;padding:10px;">'.siteConfig('ads2').'<br><span style="cursor:pointer" data-premium="1">Upgrade premium for remove ads</span></div></center>';
			}
			if($sm['user']['premium'] == 0 && $sm['basic']['mobile_ads'] == 1 && $x==13){
				$search.= '<center><div style="width:100%;background:#f3f3f3;color:#488ad8;padding:10px;">'.siteConfig('ads2').'<br><span style="cursor:pointer" data-premium="1">Upgrade premium for remove ads</span></div></center>';
			}			
			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a href="mobile.php?page=profile&id='.$sm['search']['id'].'" ><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="mobile.php?page=profile&id='.$sm['search']['id'].'" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i> <span>'.$sm['search']['city'].'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a href="'.$sm['config']['site_url'].'profile&id='.$sm['search']['id'].'"><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i> <span>'.$sm['search']['city'].' </span></div></div>';
			}
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}
			if($sm['search']['popular'] > siteConfig('popular_user')){	
				$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
			}			
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-social-public" style="font-size:166px;"></i><br><br>'.$sm['lang'][153]['text'].'</h5></center>';
	}
	$totalPages = ceil($sm['meet_result'] / 20);
	$totalp = $totalPages-1;
	$limi = $limi + 1;
	if($totalp > 0 ){
		$search.='<a href="mobile.php?page=meet&status='.$status.'&l='.$limi.'"><div
		style="width:80%;height:45px;margin-left:10%;margin-right:10%;margin-bottom:20px;background:#fff;color:#488ad8;border:1px solid #488ad8;padding:10px;font-size:14px;line-height:18px;text-align:center;border-radius:25px;">Load more</div></a>';	
	}
	$search.= '<script>meet_pages = '.$totalp.'</script>';
	return $search;
}

function popularUsers(){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$x=0;
	$all = count($sm['genders']);
	$all = $all + 1;

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
			if($sm['plugins']['populars']['viewOnlyPremium'] == 'Yes' && $sm['user']['premium'] == 0){
				meetCards($row->id,'populars','',0,1);
			} else {
				meetCards($row->id);	
			}
		}
	} else {
		$search.= '
			<center data-chat-result="0" style="margin-bottom: 50px;padding-top: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['discover_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['default_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].'">'.$sm['lang'][155]['text'].'</h5>
				</strong>
			</center>';
	}
	return $search;
}
function userFans($a){
	global $mysqli,$sm;
	$search = '';
	$blur = '';
	$time_now = time()-300;
	if($a == 2){
		$blur = 'blured';
	}
	$mysqli->query("UPDATE users_likes set notification = 1 where u2 = '".$sm['user']['id']."' and u1 <> '".$sm['user']['id']."'");
	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$sm['user']['id']."' AND love = 1");			
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			if($a == 2){
			} else {
				$check = getData('users','id','WHERE id = "'.$row->u1.'"');
				if ($check != 'noData'){	
					$search.= meetCards($row->u1);
				}			
			}
		}
	} else {
		$search.= '
			<center data-chat-result="0" style="margin-bottom: 50px;padding-top: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['discover_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['default_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].'">'.$sm['lang'][301]['text'].'</h5>
				</strong>
				<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].';opacity: .5">
					'.$sm['lang'][597]['text'].'
				</h5>
			</center>';
	}
	return $search;
}
function myLikes(){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT u2 FROM users_likes WHERE u1 = '".$sm['user']['id']."' AND love = 1");			
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			$search.= meetCards($row->u2);
		}
	} else {
		$search.= '
			<center data-chat-result="0" style="margin-bottom: 50px;padding-top: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['discover_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['default_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].'">'.$sm['lang'][301]['text'].'</h5>
				</strong>
				<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].';opacity: .5">
					'.$sm['lang'][597]['text'].'
				</h5>
			</center>';
	}
	return $search;
}

function myBlocks(){
	global $mysqli,$sm;
	$search = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT uid2 FROM users_blocks WHERE uid1 = '".$sm['user']['id']."'");			
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			$search.= meetCards($row->uid2,'blocked');
		}
	} else {
		$search.= '
			<center data-chat-result="0" style="margin-bottom: 50px;padding-top: 50px">
				<br>
		        <div class="brick  brick--xxlg vivify" style="width: 240px;height: 240px;border-radius:'.$sm['theme']['discover_no_result_border']['val'].'px">
		            <div class="brick-img profile-photo box-shadow-credits" data-src="'.$sm['theme']['default_no_result']['val'].'"></div>
		        </div>
				<br><br>
				<strong>
					<h5 style="color:'.$sm['theme']['wall_font_color_default']['val'].'">'.$sm['lang'][710]['text'].'</h5>
				</strong>
			</center>';
	}
	return $search;
}

function getWithdrawPackages() {
	global $mysqli,$sm;
	$ret = array();
	$query = $mysqli->query("SELECT * FROM config_withdraw order by credits ASC limit 100");
	if($query->num_rows >= 1) {
		while($result = $query->fetch_object()){
			$ret[] = array(
				"price" => $result->price, 
				"credits" =>$result->credits,
				"id" =>$result->id
			);
		}
	}
	return $ret;	
}
function checkWithdrawExist($uid) {
	global $mysqli,$sm;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_withdraw WHERE u_id = '".$uid."' AND status = 'Pending'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}
function getAdminWithdraws($limit=0) {
	global $mysqli,$sm;
	$return = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT * FROM users_withdraw order by id DESC");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				getUserInfo($cre->u_id,6);
				$return .= ' <tr>					
							  <td>'.$cre->id.'</td>				  
							  <td>'.$sm['search']['name'].', '.$sm['search']['age'].', '.$sm['search']['city'].' </td>						  
							  <td>'.$sm['search']['paypal'].'</td>
							  <td>$'.$cre->withdraw_amount.'</td>
							  <td>'.$cre->status.'</td>				  
							  ';
							  if($cre->status == "Pending"){
							  	$return .= '
							  <td><a href="index.php?page=admin&p=withdraw&completed='.$cre->id.'" id="withdrawComplete" class="label label-success">Mark as completed</a></td>
							  	</tr>';
							  } else {
							  	$return .= '<td></td></tr>';
							  }
		}
	}
	return $return;			
}

function date_dropdown($d=0,$m=0,$y=0){
	global $sm;
	$html_output = '';
	$year_limit = $sm['settings']['yearLimit'];
	if($d==1){
		for ($day = 1; $day <= 31; $day++) {
			$html_output .='<li class="option" data-value="' . $day . '" data-name="'.$day.'" data-action="day"> <span class="option__txt">
			'.$day.'</span> </li>'."\n";
		}
	}
	if($m==1){	
		$months = array("", $sm['lang'][158]['text'],$sm['lang'][159]['text'],$sm['lang'][160]['text'],$sm['lang'][161]['text'],$sm['lang'][162]['text'],$sm['lang'][163]['text'],$sm['lang'][164]['text'],$sm['lang'][165]['text'],$sm['lang'][166]['text'],$sm['lang'][167]['text'],$sm['lang'][168]['text'],$sm['lang'][169]['text']);
		for ($month = 1; $month <= 12; $month++) {
			$html_output .='<li class="option" data-value="' . $month . '" data-name="'.$months[$month].'" data-action="month"> <span class="option__txt">
			'.$months[$month].'</span> </li>'."\n";
		}
	}
	if($y==1){
		for ($year = 1925; $year <= (date("Y") - $year_limit); $year++) {
			$html_output .='<li class="option" data-value="' . $year . '" data-name="'.$year.'" data-action="year"> <span class="option__txt">
			'.$year.'</span> </li>'."\n";
		}
		}
    return $html_output;
}
function date_dropdown_landing($d=0,$m=0,$y=0){
	global $sm;
	$html_output = '';
	$year_limit = $sm['plugins']['settings']['minRegisterAge'];
	if($d==1){
		for ($day = 1; $day <= 31; $day++) {
			$html_output .= '<option style="color:#000;" value="' . $day . '">' . $day . '</option>'."\n";
		}
	}
	if($m==1){	
		$months = array("", $sm['lang'][158]['text'],$sm['lang'][159]['text'],$sm['lang'][160]['text'],$sm['lang'][161]['text'],$sm['lang'][162]['text'],$sm['lang'][163]['text'],$sm['lang'][164]['text'],$sm['lang'][165]['text'],$sm['lang'][166]['text'],$sm['lang'][167]['text'],$sm['lang'][168]['text'],$sm['lang'][169]['text']);
		for ($month = 1; $month <= 12; $month++) {
			$html_output .= '<option value="' . $month . '" style="color:#000;">' . $months[$month] . '</option>'."\n";
		}
	}
	if($y==1){
		for ($year = date("Y")-$year_limit; $year >= (1925); $year--) {
			$html_output .= '<option style="color:#000;" value="' . $year . '">' . $year . '</option>'."\n";
		}
		}
    return $html_output;
}
function fbconnect($fuid,$name,$email,$gender,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
    $check = $mysqli->query("select id from users where facebook_id = '".$fuid."'");
	$photo = "https://graph.facebook.com/".$fuid."/picture?type=large";
	$pswd = $fuid;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}
		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,facebook_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$fuid."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");	
		}							 
	}
}
function getTotalUsersCity($lat,$lng,$radius,$val2,$age,$city='') {
	global $mysqli;
	$e_age = explode( ',', $age );
	$age1 = $e_age[0];
	$age2 = $e_age[1];
	
	
	$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
	* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
	FROM users
	WHERE gender = '".$val2."'
	AND age BETWEEN '".$age1."' AND '".$age2."'	
	HAVING distance < $radius";		
	
	$total = $mysqli->query($query);
	$total = $total->num_rows;
	return $total;
}
function instaconnect($id,$name,$email,$gender,$photo,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
	$return = 1;
    $check = $mysqli->query("select id from users where instagram_id = '".$id."'");
	$pswd = $id;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
		return $return;
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}
		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,instagram_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$id."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$result = $last_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");
			return $result;
		}							 
	}
}
function googleconnect($id,$name,$email,$gender,$photo,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
    $check = $mysqli->query("select id from users where google_id = '".$id."'");
	$pswd = $id;
	$name = secureEncode($name);
	$check = getData('users','id','WHERE email = "'.$email.'"');
	if ($check != 'noData'){	
		$_SESSION['user'] = $check;	
	} else {
		$gender = 1;
		$looking = 2;		
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		}
		if($gender == 'female'){
			$gender = 2;
			$looking = 1;
		}

		$bio = $sm['lang'][322]['text']." ".$name.", ".$sm['lang'][323]['text']." ".$city." ".$country;
		
		$ip = getUserIpAddr();

		$sage = '18,30,1';

		$explodeEmail = explode('@', $email);
		$username = $explodeEmail[0].rand(0,10421);
		$username = str_replace('.', '', $username);
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
		$lang = getData('languages','id','WHERE id = '.$_SESSION['lang']);
		if($lang == 'noData'){
			$lang = $sm['plugins']['settings']['defaultLang'];
		}

		if(checkIfExist('blocked_ips','ip',$ip) == 1){
			echo 'Error'.$sm['lang'][656]['text'];	
			exit;							
		}

		if(checkIfExist('blocked_users','email',$email) == 1){
			echo 'Error'.$sm['lang'][656]['text'];	
			exit;							
		}	

		$date = date('m/d/Y', time());

		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,online_day,password,ip,last_access,username,join_date_time)
								VALUES ('".$name."', '".$email."','".$pswd."','23','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$lang."','".$date."','".$bio."','".$looking."','".$sage."',0,0,'googleconnect','".$ip."','".time()."','".$username."','".time()."')";					
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
				$_SESSION['user'] = $last_id;
			}

			if($sm['plugins']['email']['enabled'] == 'Yes'){
				if($sm['plugins']['settings']['forceEmailVerification'] == 'Yes'){
					welcomeMailVerification($name,$last_id,$email,$password);					
				} else {
					welcomeMailNotification($name,$email,$password);
				}							
			}
		}							 
	}
}
function twitterconnect($id,$name,$email,$gender,$photo,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
	$return = 1;
    $check = $mysqli->query("select id from users where twitter_id = '".$id."'");
	$pswd = $id;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
		return $return;
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}
		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,twitter_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$id."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$result = $last_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");
			return $result;
		}							 
	}
}
function quitar_tildes($cadena) {
$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
$texto = str_replace($no_permitidas, $permitidas ,$cadena);
return $texto;
}
function getimg($url) {         
    $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg, image/png';              
    $headers[] = 'Connection: Keep-Alive';         
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';         
    $user_agent = 'php';         
    $process = curl_init($url);         
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);         
    curl_setopt($process, CURLOPT_HEADER, 0);         
    curl_setopt($process, CURLOPT_USERAGENT, $useragent);         
    curl_setopt($process, CURLOPT_TIMEOUT, 30);         
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);         
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);         
    $return = curl_exec($process);         
    curl_close($process);         
    return $return;     
}
/* Check Functions */
function isLogged() {
    global $mysqli;
    if (!empty($_SESSION['user']) && is_numeric($_SESSION['user']) && $_SESSION['user'] > 0) {
        $user_id = secureEncode($_SESSION['user']);
        $query = "SELECT COUNT(id) AS count FROM usuarios WHERE id=$user_id";
        $sql_query = $mysqli->query($query);
        $sql_fetch = mysqli_fetch_assoc($sql_query);
        return $sql_fetch['count'];
    }
}
function getPage($page_url='') {
    global $sm, $lang;
    $page = './themes/' . $sm['config']['theme'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getJS($page_url='') {
    global $sm, $lang;
    $page = './themes/' . $sm['config']['theme'] . '/js/' . $page_url . '.js';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getAbsolutePage($page_url='') {
    global $sm, $lang;
    $page = __DIR__.'/../../themes/' . $sm['config']['theme'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getLandingAbsolutePage($page_url='') {
    global $sm, $lang;
    $page = __DIR__.'/../../themes/' . $sm['config']['landing'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getAbsolutePageAdmin($page_url='') {
    global $sm, $lang;
    $page = __DIR__.'/../../administrator/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getLandingPage($page_url='',$customLanding='') {
    global $sm, $lang;
    if($customLanding != ''){
		$page = './themes/' . $customLanding . '/layout/' . $page_url . '.phtml';
    } else {
    	$page = './themes/' . $sm['config']['theme_landing'] . '/layout/' . $page_url . '.phtml';	
    }
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getConnectPage($page_url='') {
    global $sm, $lang;
    $page = './themes/connect/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function requestPage($page_url='') {
    global $sm, $lang;
    $page = '../themes/' . $sm['config']['theme'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}


function getMailPage($page_url='') {
    global $sm, $lang;
    $page = '../themes/' . $sm['config']['theme_email'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getMobilePage($page_url='') {
    global $sm, $lang;
    $page = './themes/' . $sm['config']['theme_mobile'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getAdminPage($page_url='') {
    global $sm, $lang;
    $page = './themes/default/layout/admin/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function getAdministratorPage($page_url='') {
    global $sm, $lang;
    $page = './administrator/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function requestAdministratorPage($page_url='') {
    global $sm, $lang;
    $page = '../administrator/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function requestAdminPage($page_url='') {
    global $sm, $lang;
    $page = '../themes/default/layout/admin/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}
function pixelateS3($image, $output, $pixelate_x = 55, $pixelate_y = 55){
	global $sm;
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg" || $ext == "jpeg")
        $img = imagecreatefromjpeg($image);
    elseif($ext == "png")
        $img = imagecreatefrompng($image);
    elseif($ext == "gif")
        $img = imagecreatefromgif($image);
    else
        echo 'Unsupported file extension';
    $size = getimagesize($image);
    $height = $size[1];
    $width = $size[0];
    for($y = 0;$y < $height;$y += $pixelate_y+1)
    {
        for($x = 0;$x < $width;$x += $pixelate_x+1)
        {
            $rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));
            $color = imagecolorclosest($img, $rgb['red'], $rgb['green'], $rgb['blue']);
            imagefilledrectangle($img, $x, $y, $x+$pixelate_x, $y+$pixelate_y, $color);
        }       
    }
    $output_name = $sm['config']['site_url'].'/assets/sources/uploads/'.$output .'.jpg';
    $output_src = 'uploads/'.$output .'.jpg';	
    imagejpeg($img, $output_src);
    imagedestroy($img); 
	return $output_src;
}
function pixelate($image, $output, $pixelate_x = 55, $pixelate_y = 55){
	global $sm;
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg" || $ext == "jpeg")
        $img = imagecreatefromjpeg($image);
    elseif($ext == "png")
        $img = imagecreatefrompng($image);
    elseif($ext == "gif")
        $img = imagecreatefromgif($image);
    else
        echo 'Unsupported file extension';
    $size = getimagesize($image);
    $height = $size[1];
    $width = $size[0];
    for($y = 0;$y < $height;$y += $pixelate_y+1)
    {
        for($x = 0;$x < $width;$x += $pixelate_x+1)
        {
            $rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));
            $color = imagecolorclosest($img, $rgb['red'], $rgb['green'], $rgb['blue']);
            imagefilledrectangle($img, $x, $y, $x+$pixelate_x, $y+$pixelate_y, $color);
        }       
    }
    $output_name = $sm['config']['site_url'].'/assets/sources/uploads/'.$output .'.jpg';
    $output_src = 'uploads/'.$output .'.jpg';	
    imagejpeg($img, $output_src);
    imagedestroy($img); 
	return $output_name;
}
function spotLight($lat,$lng,$r=0){
	global $mysqli,$sm;	
	$time = time()-86400;
	$time_now = time()-300;
	$i = 0;
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight
		where country = '".$sm['user']['country']."'		
		ORDER BY time desc
		LIMIT 25
		");	
	if ($spotlight->num_rows < 3) {
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight
		ORDER BY distance
		LIMIT 25
		");				
	}
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){		
			echo'<div data-surl="profile"  data-uid="'.$spotl->u_id.'" class="profile-photo vivify popIn" data-src="'.$spotl->photo.'" >'.userStatusSpotlight($spotl->u_id).'</div>';
		}	
	}
}
function spotLightMobile($lat,$lng,$wide=0){
	global $mysqli,$sm;	
	$time = time()-86400;
	$time_now = time()-300;
	$i = 0;
	$delay = 0;
	$limit = $sm['plugins']['spotlight']['limit'];
	$area = $sm['plugins']['spotlight']['area'];
	$autoWorldwide = $sm['plugins']['spotlight']['worldwide'];

	$storyOnlineBorder = $sm['theme']['spotlight_border_story_online']['val'];
	$storyBorderYes = $sm['theme']['spotlight_border_story']['val'];
	$storyBorderAddMe = $sm['theme']['spotlight_border_story_add']['val'];
	$storyBorderRadius = $sm['theme']['spotlight_border_radius']['val'];

	//stories
	$storyFrom = $sm['plugins']['story']['days'];
	$time = time();	
	$extra = 86400 * $storyFrom;
	$storyFrom = $time - $extra;

	$brickSize = 'lg';
	$brickBorderBig = '2';
	$brickBorder = '1';	
	if($wide == 1){
		$brickSize = 'xlg';
		$brickBorderBig = '3';
		$brickBorder = '2';
	} else {
		if($sm['theme']['design_style']['val'] == 'Top-Menu'){
			$brickSize = 'llg';
			$brickBorderBig = '2';
			$brickBorder = '1';			
		}
	}

	$addMe = '<div class="brick brick--'.$brickSize.' vivify delay-50 add-yourself box-shadow" style="cursor:pointer;border:'.$brickBorderBig.'px solid '.$storyBorderAddMe.';border-radius:'.$storyBorderRadius.'px">
	    <div class="brick-img profile-photo"  data-src="'.$sm['user']['profile_photo'].'" style="cursor:pointer;border-radius:'.$storyBorderRadius.'px"></div>
	    <mark class="brick  brick--bc"  data-spotlight-border style="width: 58px;height: 14px;border-radius: 3px;background:'.$storyBorderAddMe.';">
	      <span class="montserratRegular" style="color:'.$sm['theme']['spotlight_border_story_add_font']['val'].';font-size: 11px;position: relative;text-align: center;line-height: 13px">'.$sm['lang'][287]['text'].'</span>
	    </mark>
	</div>';

	if($wide == 1){
		$addMe = '';
	}
	echo $addMe;

	if($area == 'Worldwide'){
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight
		ORDER BY distance
		LIMIT $limit
		");
		if($spotlight && $spotlight->num_rows > 0){
			while($spotl = $spotlight->fetch_object()){	
				$id = $spotl->u_id;
				$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
				if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
					continue;							
				}
				$storiesFilter = 'where uid = '.$id.' and storyTime > '.$storyFrom.' and deleted = 0';

				$story = selectC('users_story',$storiesFilter);
				$storyBorder = 'none';

				$spotlightPhoto = $spotl->photo;
				$name = getData('users','name','where id ='.$id);
				$first_name = explode(' ',trim($name));	
				$first_name = explode('_',trim($first_name[0]));						
				$mark = '<mark class="brick rick--spp brick--bc spotlight-name-mark" style="width: auto;height: 12px;border-radius: 3px;background:#fff;padding-left:5px;padding-right:5px;top:0px">
                  <span class="montserratRegular" style="color:#333;font-size: 12px;position: relative;text-align: center;line-height: 12px;">'.$first_name[0].'</span>
                </mark>';
				if($status > 0){
					$storyBorder = $storyOnlineBorder;
				}
				if($story > 0){
					$storyBorder = $storyBorderYes;
				} 
				echo 
				'<div onclick="goToProfile('.$spotl->u_id.')" class="brick brick--'.$brickSize.' vivify popIn spotlight-name box-shadow  delay-'.$delay.'" style="cursor:pointer;border:'.$brickBorderBig.'px solid '.$storyBorder.';border-radius:'.$storyBorderRadius.'px">
				<div class="brick-img profile-photo" data-src="'.$spotl->photo.'" style=""></div>'.$mark.'</div>';					
				$delay = $delay+50;
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
		if($spotlight && $spotlight->num_rows > 0){
			while($spotl = $spotlight->fetch_object()){		
				$id = $spotl->u_id;
				$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
				if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
					continue;							
				}				
				$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0';

				$story = selectC('users_story',$storiesFilter);
				$storyBorder = 'none';

				$name = getData('users','name','where id ='.$id);
				$first_name = explode(' ',trim($name));	
				$first_name = explode('_',trim($first_name[0]));						
				$mark = '<mark class="brick rick--spp brick--bc spotlight-name-mark" style="width: auto;height: 12px;border-radius: 3px;background:#fff;padding-left:5px;padding-right:5px;top:0px">
                  <span class="montserratRegular" style="color:#333;font-size: 12px;position: relative;text-align: center;line-height: 12px;">'.$first_name[0].'</span>
                </mark>';

				if($status > 0){
					$storyBorder = $storyOnlineBorder;
				}
				if($story > 0){
					$storyBorder = $storyBorderYes;
				} 
				echo 
				'<div onclick="goToProfile('.$spotl->u_id.')" class="brick brick--'.$brickSize.' box-shadow vivify popIn spotlight-name  delay-'.$delay.'" style="cursor:pointer;border:'.$brickBorderBig.'px solid '.$storyBorder.';border-radius:'.$storyBorderRadius.'px">
				<div class="brick-img profile-photo" data-src="'.$spotl->photo.'" ></div>'.$mark.'</div>';					
				$delay = $delay+50;
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

						$id = $spotl->u_id;
						$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
						if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
							continue;							
						}						
						$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0';

						$status = userFilterStatus($id);
						$story = selectC('users_story',$storiesFilter);
						$storyBorder = 'none';

						$name = getData('users','name','where id ='.$id);
						$first_name = explode(' ',trim($name));	
						$first_name = explode('_',trim($first_name[0]));						
						$mark = '<mark class="brick rick--spp brick--bc spotlight-name-mark" style="width: auto;height: 12px;border-radius: 3px;background:#fff;padding-left:5px;padding-right:5px;top:0px">
                          <span class="montserratRegular" style="color:#333;font-size: 12px;position: relative;text-align: center;line-height: 12px;">'.$first_name[0].'</span>
                        </mark>';

						if($status > 0){
							$storyBorder = $storyOnlineBorder;
						}
						if($story > 0){
							$storyBorder = $storyBorderYes;
						} 

						echo 
						'<div onclick="goToProfile('.$id.')" class="brick brick--'.$brickSize.' spotlight-name box-shadow vivify popIn  delay-'.$delay.'" style="cursor:pointer;border:'.$brickBorderBig.'px solid '.$storyBorder.';border-radius:'.$storyBorderRadius.'px">
						<div class="brick-img profile-photo" data-src="'.$spotl->photo.'"></div>'.$mark.'</div>';					
						$delay = $delay+50;
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

					$id = $spotl->id;
					$checkIfBlocked = 'uid1 = '.$sm['user']['id'].' AND uid2 ='.$id;
					if(checkIfExistFilter('users_blocks',$checkIfBlocked) == 1){
						continue;							
					}					
					$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0';

					$status = userFilterStatus($id);
					$story = selectC('users_story',$storiesFilter);
					$storyBorder = 'none';

					$name = getData('users','name','where id ='.$id);
					$first_name = explode(' ',trim($name));	
					$first_name = explode('_',trim($first_name[0]));						
					$mark = '<mark class="brick rick--spp brick--bc spotlight-name-mark" style="width: auto;height: 12px;border-radius: 3px;background:#fff;padding-left:5px;padding-right:5px;top:0px">
	                  <span class="montserratRegular" style="color:#333;font-size: 12px;position: relative;text-align: center;line-height: 12px;">'.$first_name[0].'</span>
	                </mark>';
					if($status > 0){
						$storyBorder = $storyOnlineBorder;
					}
					if($story > 0){
						$storyBorder = $storyBorderYes;
					} 

					echo 
					'<div onclick="goToProfile('.$id.')" class="brick brick--'.$brickSize.' box-shadow spotlight-name vivify popIn delay-'.$delay.'" style="cursor:pointer;border:'.$brickBorderBig.'px solid '.$storyBorder.';border-radius:'.$storyBorderRadius.'px">
					<div class="brick-img profile-photo" data-src="'.profilePhoto($id).'" style=""></div>'.$mark.'</div>';					
					$delay = $delay+50;
					$i++;

				}	
			}			
		}
	}

}


function discoverStories($lat,$lng){
	global $sm,$mysqli;

	//stories
	$storyFrom = $sm['plugins']['story']['days'];
	$time = time();	
	$extra = 86400 * $storyFrom;
	$storyFrom = $time - $extra;

	$i = 0;
	//result
	$result = array();
	$query = $mysqli->query("SELECT uid, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
	* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
	FROM users_story

	WHERE storyTime > $storyFrom
	AND review = 'No'
	ORDER BY distance

	LIMIT 0,100
	");
	if($query && $query->num_rows > 0){ 
		while($q = $query->fetch_object()){	

			$id = $q->uid;
			$gender = getData('users','gender','where id = '.$id);
			if($gender != $sm['user']['s_gender']){
				continue;
			}			
			$fadeDelay = 50;
			if (!in_array($id, $result)){
				$i++;
				array_push($result,$id);
				$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0 and review = "No"';
				$limitFilter = ' order by storyTime DESC limit 0,1';
				$story = getData('users_story','story',$storiesFilter.$limitFilter);
				$storyType = getData('users_story','storyType',$storiesFilter.$limitFilter);
				
		
				$status = userFilterStatus($id);
				$storyBorder = '#ffffff';

				$name = getData('users','name','where id ='.$id);
				$first_name = explode(' ',trim($name));	
				$first_name = explode('_',trim($first_name[0]));
				$fadeDelay = $fadeDelay * $i;
				echo 
				'<div class="story-container vivify fadeIn delay-'.$fadeDelay.'" data-current-story-position="'.$i.'" onclick="openStoryDiscover(this,'.$id.')">
	                <div class="story-preview" ><div class="backgroundAnimation sloader" data-story-loader="'.$id.'" style="display:none"></div>';

	            if($storyType == 'video'){
	            	echo '<canvas id="videoStoryCanvas'.$id.'" style="display:none"></canvas>';	            	
	            	echo '<div class="story-preview-background discoverStoryPhoto" id="videoStoryBG'.$id.'"></div>';
	            } else {
	            	echo '<div class="story-preview-background discoverStoryPhoto" style="background-image:url('.$story.')"></div>';
	            } 
	            echo '
	            	<div class="story-preview-mask"></div><div class="user-avatar story-preview-avatar profile-photo" data-src="'.profilePhoto($id).'" style="border:2px solid #fff"></div>
	                    <p class="story-preview-name">
	                    	<span>'.$first_name[0].'</span>
	                    </p>
	                </div>';

	            if($storyType == 'video'){
	            	echo '<video data-discover-story-video="'.$id.'" id="videoStory'.$id.'" src="'.$story.'" type="video/mp4" muted preload style="position:absolute;top:0;left:0;display:none;width:100%;height:100%"></video>';
	            }

	            echo '</div>';
	            				
			}
				
		}
		echo '<script>totalDiscoverStories = '.$i.';</script>';	
	}	
}



function discoverStoriesMobile($lat,$lng,$looking,$filters=''){
	global $sm,$mysqli;

	//stories
	$storyFrom = $sm['plugins']['story']['days'];
	$time = time();	
	$extra = 86400 * $storyFrom;
	$storyFrom = $time - $extra;

	$i = 0;
	//result
	$result = array();
	$data = array();
	$json = array();
	$query = $mysqli->query("SELECT uid, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
	* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
	FROM users_story

	WHERE storyTime > $storyFrom
	AND review = 'No'
	ORDER BY distance

	LIMIT 0,1500
	");
	if($query && $query->num_rows > 0){ 
		while($q = $query->fetch_object()){	
			$id = $q->uid;
			$fadeDelay = 50;

			$gender = getData('users','gender','where id = '.$id);
			if($gender != $looking){
				continue;
			}
			if (!in_array($id, $result)){
				$i++;
				array_push($result,$id);
				$storiesFilter = 'where uid = '.$id.' and storyTime >'.$storyFrom.' and deleted = 0 and review = "No"';
				$limitFilter = ' order by storyTime DESC limit 0,1';
				$story = getData('users_story','story',$storiesFilter.$limitFilter);
				$storyType = getData('users_story','storyType',$storiesFilter.$limitFilter);
				
		
				$status = userFilterStatus($id);
				$storyBorder = '#ffffff';

				$name = getData('users','name','where id ='.$id);
				$first_name = explode(' ',trim($name));	
				$first_name = explode('_',trim($first_name[0]));
				$fadeDelay = $fadeDelay * $i;

				$data['id'] = $id;
				$data['profile_photo'] = profilePhoto($id);
				$data['profile_photo_big'] = profilePhoto($id,1);
				$data['city'] = getData('users','city','where id ='.$id);
				$data['age'] = getData('users','age','where id ='.$id);							
				$data['fadeDelay'] = $fadeDelay;
				$data['video'] = 0;
				$data['name'] = $first_name[0];
				$data['status'] = $status;
				$data['story'] = $story;

	            if($storyType == 'video'){
	            	$data['video'] = 1;
	            }

	            $json['stories'][] = $data;
			}
				
		}

		$json['totalDiscoverStories'] = $i;	
	} else {
		$json['result'] = 'empty';
	}

	return json_encode($json);	
}


function getGifts($val='chat') {
	global $mysqli,$sm;
	$gifts = '';
	$gift = $mysqli->query("SELECT * FROM gifts order by price ASC limit 100");
	if ($gift->num_rows > 0) { 
		while($gi = $gift->fetch_object()){
			if($val == 'live'){
				$giftSrc = "'".$gi->icon."'";
				$gifts .= '
                <div class="stream-gifts__item" onclick="sendLiveGift('.$giftSrc.','.$gi->price.')">
                    <div class="stream-gift">
                        <div class="stream-gift__content">
                            <div class="stream-gift__image-box">
                            	<img class="stream-gift__image" src="'.$gi->icon.'" alt="">
                            </div>
                            <div class="stream-gift__price">
                            <div class="stream-gift__price-icon">
                                <div class="icon icon--block icon--stretch">
                                    <svg class="icon__svg">
                                        <svg id="icon-stream-gift-price-color" viewBox="0 0 12 12"><circle cx="6" cy="6" r="5.75" fill="url(#icon-stream-gift-price-color-a)" stroke="#FFC831" stroke-width=".5"></circle><path fill="#FFBA00" d="M7.34 3.82c-.53 0-1.03.24-1.34.65a1.7 1.7 0 00-1.86-.56C3.46 4.13 3 4.74 3 5.43c0 1.46 2.01 3.3 3 3.3.99 0 3-1.84 3-3.3 0-.89-.74-1.6-1.66-1.61z"></path></svg>
                                    </svg>
                                </div>
                            </div>'.$gi->price.'</div>
                        </div>
                    </div>
                </div>
				';
			} else {
				$gifts .= '<div class="gift gift--hover  send-gift" data-gprice="'.$gi->price.'" data-src="'.$gi->icon.'" style="margin-right:10px;height:140px;width:140px;">
					<img src="'.$gi->icon.'" width="70" style="border-radius:15px" />
					<span class="'.$sm['theme']['send_gift_bg']['val'].'" style="position:absolute;bottom:10px;left:0;width:70%;margin-left:15%;text-align:center;background:'.$sm['theme']['send_gift_bg']['val'].';color:'.$sm['theme']['send_gift_color']['val'].';border-radius:5px">
					'.$gi->price.' '.$sm['lang'][73]['text'].'</span>
					<span class="sendGiftName" style="position:absolute;top:10px;left:0;width:100%;text-align:center;color:'.$sm['theme']['send_gift_body_color']['val'].'">
					'.$gi->gift.' </span>				
					</div>';				
			}
		}
	}
	return $gifts;			
}

function getGiftsBottom() {
	global $mysqli,$sm;
	$gifts = '';
	$delay = 0;
	$gift = $mysqli->query("SELECT * FROM gifts order by price ASC limit 100");
	if ($gift->num_rows > 0) { 
		while($gi = $gift->fetch_object()){
			$gifts .= '<div class="gift send-gift vivify popIn delay-'.$delay.'" data-gprice="'.$gi->price.'" data-src="'.$gi->icon.'" style="margin-right:5px;height:100px;width:120px">
				<img src="'.$gi->icon.'" width="80" style="bottom:15px"/>
				<span class="'.$sm['theme']['credits_section_rise_up_bg']['val'].'" style="position:absolute;bottom:5px;left:0;width:75%;margin-left:15%;text-align:center;background:'.$sm['theme']['send_gift_bg']['val'].';color:'.$sm['theme']['send_gift_color']['val'].';border-radius:5px">
				'.$gi->price.' '.$sm['lang'][73]['text'].'</span>		
				</div>';	
				$delay = $delay+100;
		}
	}
	return $gifts;			
}

function getGiftsAdmin() {
	global $mysqli,$sm;
	$gifts = '';
	$widget = "'manageGift'";
	$gifts.= '<div class="avatar avatar-xl" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add new gift" data-edit-gift="0" style="margin-bottom:30px;margin-right:10px;cursor:pointer;display: inline-block;text-align:center" onclick="openWidget('.$widget.',this)">
            <img src="'.$sm['config']['site_url'].'administrator/assets/images/add.png" class="avatar-img rounded">
			<span style="font-size:14px">Add gift</span>            
          </div>';
	$gift = $mysqli->query("SELECT * FROM gifts order by price ASC");
	if ($gift->num_rows > 0) { 
		while($gi = $gift->fetch_object()){
			$gifts.='
				<div class="avatar avatar-xl" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$gi->gift.'" data-gift-id="'.$gi->id.'" data-edit-gift="1" data-gift-icon="'.$gi->icon.'" data-gift-price="'.$gi->price.'" data-gift-name="'.$gi->gift.'" style="margin-bottom:30px;margin-right:10px;cursor:pointer;text-align:center" onclick="openWidget('.$widget.',this)">
				    <img src="'.$gi->icon.'" class="avatar-img rounded">
				    <span style="font-size:14px">'.$gi->price.'</span>
				</div>
			';
		}
	}
	return $gifts;			
}


function getInterestsAdmin() {
	global $mysqli,$sm;
	$result = '';
	$widget = "'manageInterest'";
	$result.= '<div class="avatar avatar-xl" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add new interest" data-edit-interest="0" style="margin-bottom:30px;margin-right:10px;cursor:pointer;display: inline-block;text-align:center" onclick="openWidget('.$widget.',this)">
            <img src="'.$sm['config']['site_url'].'administrator/assets/images/add.png" class="avatar-img rounded">
			<span style="font-size:14px">Add</span>            
          </div>';
	$query = $mysqli->query("SELECT * FROM interest order by id ASC");
	if ($query->num_rows > 0) { 
		while($q = $query->fetch_object()){
			$result.='
				<div class="avatar avatar-xl" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$q->name.'" data-interest-id="'.$q->id.'" data-edit-interest="1" data-interest-icon="'.$q->icon.'" data-interest-name="'.$q->name.'" style="margin-bottom:30px;margin-right:10px;cursor:pointer;text-align:center;" onclick="openWidget('.$widget.',this)">
				    <img src="'.$q->icon.'" class="avatar-img rounded">
				    <span style="font-size:14px;width:120px;text-overflow: ellipsis;white-space: nowrap;">
				    '.$q->name.'
				    </span>
				</div>
			';
		}
	}
	return $result;			
}

function getSpotlightUsersAdmin() {
	global $mysqli,$sm;
	$result = '';
	$query = $mysqli->query("SELECT * FROM spotlight order by time ASC");
	if ($query->num_rows > 0) { 
		while($q = $query->fetch_object()){
			$result.='
				<div class="avatar avatar-xl" data-user-spotlight="'.$q->time.'" style="margin-bottom:30px;margin-right:10px;cursor:pointer;text-align:center;" onclick="removeFromSpotlight('.$q->time.')">
				    <img src="'.$q->photo.'" class="avatar-img rounded">
				    <span style="font-size:14px;width:120px;text-overflow: ellipsis;white-space: nowrap;">
				    '.getData('users','name','where id ='.$q->u_id).'
				    </span>
				</div>
			';
		}
	}
	return $result;			
}

function getCreditsPackages() {
	global $mysqli,$sm;
	$return = '';
	$i=0;
	$query = $mysqli->query("SELECT * FROM config_credits order by credits ASC limit 100");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
			$i++;
			if($i==1){
				$return .= '<option data-price="'.$cre->price.'" data-quantity="'.$cre->credits.'" selected>'.$cre->credits.' '.$sm['lang'][305]['text'].''.$cre->price.' '.$sm['config']['currency'].'</option>';
			} else {
				$return .= '<option data-price="'.$cre->price.'" data-quantity="'.$cre->credits.'">'.$cre->credits.' '.$sm['lang'][305]['text'].''.$cre->price.' '.$sm['config']['currency'].'</option>';				
			}
		}
	}
	return $return;			
}
function getAdminCreditsPackages() {
	global $mysqli,$sm;
	$return = '';
	$i = 1;
	$query = $mysqli->query("SELECT * FROM config_credits order by credits ASC limit 100");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '
		          <div class="col-lg-4 card-body bg-light" >
		            <p><strong class="headings-color"></strong></p>
		            <p class="text-muted">
		              Credits <code>package '.$i.'</code>
		            </p>
		          </div>
		          <div class="col-lg-8 card-form__body card-body d-flex align-items-center bg-white" >
		            <div class="flex" style="">
		                <label for="pricing" >Credits</label><br>
		                <input type="number" data-pricing-id="'.$cre->id.'" data-pricing-type="credits" data-pricing-col="credits" value="'.$cre->credits.'" class="form-control" style="width:90px">
                
		            </div>
		            <div class="flex" style="margin-left:-5px">
		                <label for="pricing" >Price '.$sm['plugins']['settings']['currency'].'</label><br>
		                <input type="number" data-pricing-id="'.$cre->id.'" data-pricing-type="credits" data-pricing-col="price" value="'.$cre->price.'" class="form-control" style="width:90px;">		                
		            </div>		            	            
		          </div> 
				';	
				$i++;	
		}
	}
	return $return;			
}

function getAdminFunctionsPrices() {
	global $mysqli,$sm;
	$return = '';
	$i = 1;
	$query = $mysqli->query("SELECT * FROM config_prices where visible = 1 order by feature ASC limit 100");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '
		          <div class="col-lg-4 card-body bg-light" >
		            <p><strong class="headings-color"></strong></p>
		            <p class="text-muted">
		              '.$cre->info.'
		            </p>
		          </div>
		          <div class="col-lg-2 card-form__body card-body d-flex align-items-center bg-white" >
		            <div class="flex" style="">
		                <label for="pricing" >Credits</label><br>
		                <input type="number" data-pricing-id="'.$cre->feature.'" data-pricing-type="feature" data-pricing-col="'.$cre->feature.'" value="'.$cre->price.'" class="form-control">
                
		            </div>            	            
		          </div> 
				';	
				$i++;	
		}
	}
	return $return;			
}

function getAdminPremiumPackages() {
	global $mysqli,$sm;
	$return = '';
	$i = 1;
	$query = $mysqli->query("SELECT * FROM config_premium order by days ASC limit 100");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '
		          <div class="col-lg-4 card-body bg-light" >
		            <p><strong class="headings-color"></strong></p>
		            <p class="text-muted">
		              Premium <code>package '.$i.'</code>
		            </p>
		          </div>
		          <div class="col-lg-8 card-form__body card-body d-flex align-items-center bg-white" >
		            <div class="flex" style="">
		                <label for="pricing" >Days</label><br>
		                <input type="number" data-pricing-id="'.$cre->id.'" data-pricing-type="premium" data-pricing-col="days" value="'.$cre->days.'" class="form-control" style="width:90px">
                
		            </div>
		            <div class="flex" style="margin-left:-25px">
		                <label for="pricing" >Price '.$sm['plugins']['settings']['currency'].'</label><br>
		                <input type="number" data-pricing-id="'.$cre->id.'" data-pricing-type="premium" data-pricing-col="price" value="'.$cre->price.'" class="form-control" style="width:90px;">		                
		            </div>		            	            
		          </div> 
				';	
				$i++;	
		}
	}
	return $return;			
}
function getAdminUsersReported($limit=0) {
	global $mysqli,$sm;
	$return = '';
	$order = secureEncode($order);
	$time_now = time()-300;
	$query = $mysqli->query("SELECT reported,reported_by FROM reports order by id DESC");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				getUserInfo($cre->reported,6);
				getUserInfo($cre->reported_by,7);
				$return .= ' <tr>
							  <td class="man-photos"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'"></td>						
							  <td>'.$sm['search']['id'].'</td>				  
							  <td>'.$sm['search']['name'].' , '.$sm['search']['age'].'
							  '; if($sm['search']['last_access'] >= $time_now) {
							  	$return .= ' <i class="fa fa-circle text-success" style="font-size:8px;"></i>';
							  }
							  $return .= '
							  </td>						  
							  <td>'.$sm['search']['email'].'</td>
							  <td>'.$sm['search']['city'].'</td>
							  <td>'.$sm['search']['country'].'</td>					  
							  <td>'.$sm['search']['credits'].'</td>
							  <td>'.$sm['search']['total_photos'].'</td>
							  <td><a href="'.$sm['config']['site_url'].'profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'" target="_blank" class="label label-info">View</a> 
							  <a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$sm['search']['id'].'" target="_blank" class="label label-primary">Edit</a></td>		
							  <td><a href="'.$sm['config']['site_url'].'profile/'.$sm['manage']['id'].'/'.$sm['manage']['first_name'].'" target="_blank">'.$sm['manage']['name'].'</a></td>							  
							</tr>';				
		}
	}
	return $return;			
}

function getAdminUsers($f,$order,$limit=0) {
	global $mysqli,$sm;
	$return = '';
	$order = secureEncode($order);
	$time_now = time()-300;
	if($f == 1) {
		$query = $mysqli->query("SELECT id FROM users order by $order DESC limit $limit,50");
	}	
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				getUserInfo($cre->id,6);
				$return .= ' <tr>
							  <td class="man-photos"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'"></td>						
							  <td>'.$sm['search']['id'].'</td>				  
							  <td>'.$sm['search']['name'].' , '.$sm['search']['age'].'
							  '; if($sm['search']['last_access'] >= $time_now) {
							  	$return .= ' <i class="fa fa-circle text-success" style="font-size:8px;"></i>';
							  }
							  $return .= '
							  </td>						  
							  <td>'.$sm['search']['email'].'</td>
							  <td>'.$sm['search']['city'].'</td>
							  <td>'.$sm['search']['country'].'</td>					  
							  <td>'.$sm['search']['credits'].'</td>
							  <td>'.$sm['search']['total_photos'].'</td>
							  <td>'.$sm['search']['join_date'].'</td>					  
							  <td><a href="'.$sm['config']['site_url'].'/profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'" target="_blank" class="label label-info">View</a> 
							  <a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$sm['search']['id'].'" target="_blank" class="label label-primary">Edit</a></td>					
							</tr>';				
		}
	}
	return $return;			
}
function getAdminGifts() {
	global $mysqli,$sm;
	$return = '';
	$query = $mysqli->query("SELECT * FROM gifts order by price ASC limit 100");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '<div class="col-md-2">
								<div class="row">
									<div class="col-xs-12">
										<img src="'.$sm['config']['theme_url'].'/gifts/'.$cre->icon.'" />
									</div>							
									<div class="col-xs-12">
										<input type="text" class="form-control" data-gift="'.$cre->id.'" value="'.$cre->price.'"> 
									</div>					
								</div>
							</div>';				
		}
	}
	return $return;			
}
function getPremiumPackages() {
	global $mysqli,$sm;
	$return = '';
	$i=0;
	$query = $mysqli->query("SELECT * FROM config_premium order by price ASC limit 100");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
			$return .= '<div class="btn btn--sm btn--spp"  data-premium-send="'.$cre->days.'" data-price="'.$cre->price.'" style="margin-bottom:10px;">'.$cre->days.' '.$sm['lang'][332]['text'].'<br>$'.$cre->price.' '.$sm['config']['currency'].'</div>';				
		}
	}
	return $return;			
}
function getUserPhotosHeaderChat($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT thumb,photo FROM users_photos WHERE approved = 1 and profile = 0 and blocked = 0 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$photo .= '<a class="s-lightbox" href="'.$up->photo.'" data-s-lightbox-group="gallery" data-s-lightbox-caption="">
      			<img src="'.$up->thumb.'">
			</a>';
		}	
	}
	return $photo;
}
function getUserPhotosHeader($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$i=0;
	$photos = $mysqli->query("SELECT thumb,photo,private,blocked,id FROM users_photos WHERE approved = 1 and u_id = '".$uid."' and video = 0 and story = 0 order by profile desc, ig_id desc, id desc LIMIT 25");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$view = unblockedUser($sm['user']['id'],$uid);
			if($up->blocked == 1){
				if($view == 0){
					$photo .= '<div class="relative profile-photo" data-src="'.$up->photo.'" style="display:inline-block;width:180px;height:180px;cursor:pointer;" onClick="showAskPrivate()">
					<img src="'.$sm['config']['theme_url'].'/images/privateImg.png">
					</div>';					
				} else {
					$photo .= '<a class="s-lightbox" href="'.$up->photo.'" data-s-lightbox-group="gallery" data-s-lightbox-caption="" data-my-photo-id="'.$up->id.'"><img src="'.$up->thumb.'"></a>';
					$i++;
				}
			} else {
				$photo .= '<a class="s-lightbox" href="'.$up->photo.'" data-s-lightbox-group="gallery" data-s-lightbox-caption="" data-my-photo-id="'.$up->id.'"><img src="'.$up->thumb.'"></a>';
				$i++;			
			}			
		}	
	}
	return $photo;
}
function getUserPublicPhotosMobile($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT photo,id,thumb FROM users_photos WHERE approved = 1 and blocked = 0 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$photo .= '<div class="group-block" data-div="'.$up->id.'"><div class="group-image" style="background-image:url('.$up->thumb.');"></div>
			<a href="#" data-set-profile="true" data-pid="'.$up->id.'" style="font-size:22px;margin-right:10px;margin-top:5px;"><i class="mdi-image-portrait"></i></a>
			<a href="#" data-delete-photo="true" data-pid="'.$up->id.'" data-tooltip="Delete" style="font-size:22px;margin-right:10px;margin-top:5px;"><i class="mdi-action-delete"></i></a></div>';
		}	
	}
	return $photo;
}
function getUserPrivatePhotosMobile($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT photo,id.thumb FROM users_photos WHERE approved = 1 and blocked = 1 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$photo .= '<div class="group-block" data-div="'.$up->id.'"><div class="group-image" style="background-image:url('.$up->thumb.');"></div>
			<a href="#" data-delete-photo="true" data-pid="'.$up->id.'" data-tooltip="Delete" style="font-size:22px;margin-right:10px;margin-top:5px;"><i class="mdi-action-delete"></i></a></div>';
		}	
	}
	return $photo;
}
function getUserPhotosAll($uid,$page=''){	
	global $mysqli,$sm;	
	$array  = array();
	$limit = 15;
	if($page == 'discover'){
		$limit = $sm['plugins']['discover']['photosGalleria'];
	}
	$photos = $mysqli->query("SELECT photo FROM users_photos WHERE approved = 1 and blocked = 0 and video = 0 and story = 0 and u_id = '".$uid."' order by id desc LIMIT $limit");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$array[] = array(
				"image" => $up->photo, 
			);
		}	
	}
	return $array;
}
function getUserPhotosAllProfile($uid){	
	global $mysqli,$sm;	
	$array  = array();
	$photos = $mysqli->query("SELECT id,photo,blocked,private FROM users_photos WHERE approved = 1 and blocked = 0 and video = 0 and story = 0 and u_id = '".$uid."' order by id desc LIMIT 200");

	$view = 0;
	if(!empty($sm['user'])){
		$view = unblockedUser($sm['user']['id'],$uid);
	}
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			if($up->blocked == 1){
				if($view == 0){
				} else {
					$array[] = array(
						"image" => $up->photo, 
						"photoId" => $up->id,
						"private" => $up->private
					);
				}
			} else {
				$array[] = array(
					"image" => $up->photo, 
					"photoId" => $up->id,
					"private" => $up->private
				);					
			}			
		}	
	}
	return $array;
}
function getUserPhotos($uid,$i=0,$x=0,$y=0,$b=0){	
	global $mysqli,$sm;	
	$photo = "";
	if($i == 1){
		$photos = $mysqli->query("SELECT * FROM users_photos WHERE u_id = '".$uid."' and approved = 1 order by id desc LIMIT 200");
	} else if($b == 1){
		$photos = $mysqli->query("SELECT * FROM users_photos WHERE approved = 1 and u_id = '".$uid."' and blocked = 1 order by id desc LIMIT 200");		
	} else {
		$photos = $mysqli->query("SELECT * FROM users_photos WHERE approved = 1 and u_id = '".$uid."' and blocked = 0 order by id desc LIMIT 200");
	}	
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($uid,1);
			$photoData['id'] = $up->id;
			$photoData['src'] = $up->photo;
			$photoData['desc'] = $up->desc;
			$photoData['user'] = $sm['profile']['name'];
			$photoData['photo'] = $sm['profile']['profile_photo'];
			$photoData['blocked'] = $up->blocked;			
			$photoData['like'] = checkPhotoLike($sm['user']['id'],$up->id);
			$photoData['likes'] = getPhotoLikes($up->id);			
			$photoData['comments'] = getPhotoComments($up->id);
			$sm['photo'] = $photoData;
			if($i == 1 && $x == 0){
				$photo .= getPage('profile/mphoto');
			} else if($x == 1){
				$photo .= requestPage('profile/mphoto');		
			} else {
				$photo .= requestPage('profile/photo');
			}
		}	
	}
	return $photo;
}
function getUserPhotosSpotlight($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT * FROM users_photos WHERE u_id = '".$uid."' and approved = 1 order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($uid,1);
			$photoData['id'] = $up->id;
			$photoData['src'] = $up->photo;
			$photoData['desc'] = $up->desc;
			$photoData['user'] = $sm['profile']['name'];
			$photoData['photo'] = $sm['profile']['profile_photo'];
			$photoData['blocked'] = $up->blocked;			
			$photoData['like'] = checkPhotoLike($sm['user']['id'],$up->id);
			$photoData['likes'] = getPhotoLikes($up->id);			
			$photoData['comments'] = getPhotoComments($up->id);
			$sm['photo'] = $photoData;
			$photo .= getPage('profile/sphoto');
		}	
	}
	return $photo;
}
function getUserPrivatePhotos($u1,$u2){	
	global $mysqli,$sm;	
	$photo = "";
	$view = unblockedUser($u1,$u2);
	$photos = $mysqli->query("SELECT * FROM users_photos WHERE approved = 1 and u_id = '".$u2."' and blocked = 1 order by id desc LIMIT 200");	
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($u2,1);
			$photoData['id'] = $up->id;
			$photoData['src'] = $up->photo;
			$photoData['psrc'] = $up->private;			
			$photoData['desc'] = $up->desc;
			$photoData['user'] = $sm['profile']['name'];
			$photoData['photo'] = $sm['profile']['profile_photo'];
			$photoData['blocked'] = $up->blocked;			
			$photoData['like'] = checkPhotoLike($sm['user']['id'],$up->id);
			$photoData['likes'] = getPhotoLikes($up->id);			
			$photoData['comments'] = getPhotoComments($up->id);
			$sm['photo'] = $photoData;
			if($view == 0){
				$photo .= requestPage('profile/p_photo');
			} else {
				$photo .= requestPage('profile/photo');
			}
		}	
	}
	return $photo;
}
function unblockedUser($u1,$u2){
	global $mysqli,$sm;
	$u1 = secureEncode($u1);
	$u2 = secureEncode($u2);
	$return = 0;
	$check = $mysqli->query("SELECT * FROM blocked_photos where u1 = '".$u1."' AND u2 = '".$u2."'");
	if($check->num_rows == 1){
		$return = 1;
	}
	if($u1 == $u2){
		$return = 2;
	}
	if($sm['user']['premium'] == 1 && $sm['premium']['private'] == 1){
		$return = 1;
	}
	if($sm['basic']['private'] == 1){
		$return = 1;
	}	
	return $return;
}
function getPhotoComments($pid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT * FROM photos WHERE pid = '".$pid."' order by id asc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($up->cid,9);
			$photo .= '<li><div class="profile-photo" data-src="'.$sm['comment']['profile_photo'].'"></div><h3><a href="'.$sm['config']['site_url'].'profile/'.$sm['comment']['id'].'/comments">'.$sm['comment']['first_name'].'</a></h3><div class="comment">'.$up->comment.'</div></li>';
		}	
	}
	return $photo;
}
function getPhotoCommentsAjax($pid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT * FROM photos WHERE pid = '".$pid."' order by id asc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($up->cid,9);
			$photo .= '<li><div class="profile-photo" data-src="'.$sm['comment']['profile_photo'].'"></div><h3><a href="'.$sm['config']['site_url'].'profile/'.$sm['comment']['id'].'/comment">'.$sm['comment']['first_name'].'</a> '.$sm['comment']['online'].'</h3><div class="comment">'.$up->comment.'</div></li>';
		}	
	}
	echo $photo;
}
function getPhotoLikes($pid){	
	global $mysqli,$sm;	
	$likes = "";
	$names = "";
	$total = getPhotoLikesTotal($pid);
	$photos = $mysqli->query("SELECT * FROM photos_likes WHERE pid = '".$pid."' order by RAND() LIMIT 1");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$names.= "<b>".$up->name."</b> ";
		}
		if($names != ""){
			if($total <= 1){
				$likes = '<i class="mdi-action-favorite"></i><span>'.$names.' '.$sm['lang'][173]['text'].'</span>';				
			} else {
				$total = $total-1;
				$likes = '<i class="mdi-action-favorite"></i><span>'.$names.' '.$sm['lang'][44]['text'].' <b>'.$total.'</b> '.$sm['lang'][172]['text'].'</span>';
			}
		}
	}
	return $likes;
}
function getPhotoLikesTotal($pid) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM photos_likes WHERE pid = '".$pid."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}
function checkPhotoLike($uid,$pid) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM photos_likes WHERE pid = '".$pid."'  AND uid = '".$uid."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}
function checkRecoverCode($uid,$code) {
	global $mysqli;
	$uid = secureEncode($uid);
	$code = secureEncode($code);	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM emails WHERE uid = '".$uid."'  AND code = '".$code."'");
	if($query->num_rows > 0){
		$total = $query->fetch_assoc();
		$result = $total['total'];
	}
	return $result;
}
function getUserPhotosChat($uid,$name){	
	global $mysqli,$sm;
	$photos = '';
	$name = secureEncode($name);
	$spotlight = $mysqli->query("SELECT photo FROM users_photos WHERE approved = 1 and u_id = '".$uid."' order by id desc LIMIT 100");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$photos.='<img data-name="'.$name.'" src="'.$spotl->photo.'" />';
		}	
	}
	return $photos;
}
function getVideocallStatus($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT status FROM users_videocall WHERE u_id = '".$uid."'");
	$u = $user->fetch_object();
	return 	$u->status;
}
function getPeerId($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT peer_id FROM users_videocall WHERE u_id = '".$uid."'");
	$u = $user->fetch_object();
	return 	$u->peer_id;
}
function getIdPeer($uid) {
	global $mysqli,$sm;
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT u_id FROM users_videocall WHERE peer_id = '".$uid."'");
	$u = $user->fetch_object();
	return 	$u->u_id;
}
function getUserFriends($uid){	
	global $mysqli,$sm;
	$friends = '';
	$arr[] = $uid;
	$today = date('w');	
	$query2 = $mysqli->query("SELECT DISTINCT s_id,id FROM chat WHERE r_id = '".$uid."' and seen <= 1  ORDER BY id DESC");

	$i= 0;
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			if (!in_array($result2->s_id, $arr)){
				$arr[] = $result2->s_id;			  			
				
				$friendId = $result2->s_id;
				$fake = getData('users','fake','where id ='.$friendId);
				$online_day = getData('users','online_day','where id ='.$friendId);
				$last_access = getData('users','last_access','where id ='.$friendId);				

				$new = getNewMessages($uid,$friendId);
				$i++;

				$delay = $i * 50;
				$friends.='
		        <div class="brick sb-friends box-shadow vivify popIn delay-'.$delay.'" id="user'.$friendId.'" onclick="rightChatLink('.$friendId.','.getNewMessages($uid,$friendId).')" data-chat="'.$friendId.'" style="cursor:pointer;"  >
		            <a href="javascript:;"  data-uid="'.$friendId.'"  data-message="'.getNewMessages($uid,$friendId).'">
		            	<div class="brick-img profile-photo"  style="cursor:pointer;border-radius:50%" data-src="'.profilePhoto($friendId).'"></div>';
		            	if($last_access+300 >= time() || $fake == 1 && $online_day == $today){
		            		$friends.='<div class="onlineFriendRight"></div>';
		            	}
		            	if($new > 0){
		            		$friends.='<div class="mark mark--red" id="mark'.$friendId.'" style="right:-5px;top:-2px;">'.$new.'</div>';
		            	}
				$friends.='                	
		            </a>
		        </div>
				';				
			}
		}	
	}
	$query2 = $mysqli->query("SELECT DISTINCT r_id,id FROM chat WHERE s_id = '".$uid."' and notification <= 1 ORDER BY id DESC");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			if (!in_array($result2->r_id, $arr)){
				$arr[] = $result2->r_id;

				$friendId = $result2->r_id;
				$fake = getData('users','fake','where id ='.$friendId);
				$online_day = getData('users','online_day','where id ='.$friendId);
				$last_access = getData('users','last_access','where id ='.$friendId);				

				$new = getNewMessages($uid,$friendId);
				$i++;
				$delay = $i * 50;
				$friends.='
		        <div class="brick sb-friends box-shadow vivify popIn delay-'.$delay.'" id="user'.$friendId.'" onclick="rightChatLink('.$friendId.','.getNewMessages($uid,$friendId).')" data-chat="'.$friendId.'" style="cursor:pointer;"  >
		            <a href="javascript:;"  data-uid="'.$friendId.'"  data-message="'.getNewMessages($uid,$friendId).'">
		            	<div class="brick-img profile-photo"  style="cursor:pointer;border-radius:50%" data-src="'.profilePhoto($friendId).'"></div>';
		            	if($last_access+300 >= time() || $fake == 1 && $online_day == $today){
		            		$friends.='<div class="onlineFriendRight"></div>';
		            	}
		            	if($new > 0){
		            		$friends.='<div class="mark mark--red" id="mark'.$friendId.'" style="right:-5px;top:-2px;">'.$new.'</div>';
		            	}
				$friends.='                	
		            </a>
		        </div>
				';	
			}
		}	
	}
	return $friends;
}
function getUserLC($uid){	
	global $mysqli,$sm;
	$id = 0;
	$id1 = 0;
	$id2 = 0;
	$time1 = 0;
	$time2 = 0;
	$query2 = $mysqli->query("SELECT DISTINCT s_id,id FROM chat WHERE r_id = '".$uid."' and seen <= 1 order by time DESC LIMIT 1");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			$id1 = $result2->s_id;
			$time1 = $result2->id;
		}	
	}
	$query2 = $mysqli->query("SELECT DISTINCT r_id,id FROM chat WHERE s_id = '".$uid."' and notification <= 1 order by time DESC LIMIT 1");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			$id2 = $result2->r_id;
			$time2 = $result2->r_id;
		}	
	}
	if($time1 > $time2){
		$id = $id1; 
	} else {
		$id = $id2; 
	}
	return $id;	
}
function getGuest($id){	
	global $mysqli,$sm;
	$r = 0;
	$query2 = $mysqli->query("SELECT id FROM users WHERE guest = 1 order by id ASC LIMIT 1");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			$r = $result2->id;
			getUserInfo($id,1);
			$mysqli->query("UPDATE users SET lat = '". $sm['profile']['lat'] ."',lng = '". $sm['profile']['lng'] ."',city = '". $sm['profile']['city'] ."',country = '". $sm['profile']['country'] ."',lang = '".$sm['profile']['lang']."',s_age ='18,30,1',s_gender = '".$sm['profile']['gender']."' where id = '".$r."'");
		}	
	}
	return $r;
}
function getAdminUser(){
	global $sm,$mysqli;
	$query2 = $mysqli->query("SELECT id FROM users WHERE admin = 1 order by id desc limit 1");
	if ($query2->num_rows > 0) { 
		$r = $query2->fetch_object();
		getUserInfo($r->id,1);
	}	
}
function getUserFriendsMobile($uid){	
	global $mysqli,$sm;
	$friends = '';
	$arr = array();
	$arr[] = $uid;
	$query2 = $mysqli->query("SELECT s_id,r_id,seen,notification FROM chat WHERE r_id = '".$uid."' || s_id = '".$uid."' order by id desc");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			if (!in_array($result2->s_id, $arr)){
				$arr[] = $result2->s_id;			  
				getUserInfo($result2->s_id,4);
				if($result2->r_id == $uid && $result2->seen == 2){				
				} else {				
					$friends.='
					<li class="list-message" data-ix="list-item">
					<a class="w-clearfix w-inline-block" href="mobile.php?page=chat&id='.$sm['friend']['id'].'">
					  '.userStatusMessagesMobile($sm['friend']['id']).'
					  <div class="w-clearfix column-left">
						<div class="image-message profile-photo" data-src="'.$sm['friend']['profile_photo'].'"><img src="'.$sm['friend']['profile_photo'].'">
						</div>
					  </div>
					  <div class="column-right">
	<div style="position:absolute;right:10px;top:15px;font-size:10px;color:#a0a0a0">'.time_elapsed_string(getLastMessageMobileTime($sm['user']['id'],$sm['friend']['id'])).'</div>
	<div style="position:absolute;right:10px;top:42px;font-size:12px;">'.getLastMessageMobileSeen($sm['user']['id'],$sm['friend']['id']).'</div>	
						<div class="message-title">'.$sm['friend']['first_name'].'</div>
						<div class="message-text">'.getLastMessageMobile($sm['user']['id'],$sm['friend']['id']).'</div>
					  </div>
					</a>
				  </li> ';	
				}				  
			}
			if (!in_array($result2->r_id, $arr)){
				$arr[] = $result2->r_id;			  
				getUserInfo($result2->r_id,4);
				if($result2->s_id == $uid && $result2->notification == 2){				
				} else {				
					$friends.='
					<li class="list-message" data-ix="list-item">
					<a class="w-clearfix w-inline-block" href="mobile.php?page=chat&id='.$sm['friend']['id'].'">
					  '.userStatusMessagesMobile($sm['friend']['id']).'
					  <div class="w-clearfix column-left">
						<div class="image-message profile-photo" data-src="'.$sm['friend']['profile_photo'].'"><img src="'.$sm['friend']['profile_photo'].'">
						</div>
					  </div>
					  <div class="column-right">
	<div style="position:absolute;right:10px;top:15px;font-size:10px;color:#a0a0a0">'.time_elapsed_string(getLastMessageMobileTime($sm['user']['id'],$sm['friend']['id'])).'</div>	
	<div style="position:absolute;right:10px;top:42px;font-size:12px;">'.getLastMessageMobileSeen($sm['user']['id'],$sm['friend']['id']).'</div>	
						<div class="message-title">'.$sm['friend']['first_name'].'</div>
						<div class="message-text">'.getLastMessageMobile($sm['user']['id'],$sm['friend']['id']).'</div>
					  </div>
					</a>
				  </li> ';		
				}				  
			}			
		}	
	}
	return $friends;
}
function getUserNewFriends($uid){	
	global $mysqli,$sm;
	$friends = '';
	$query = $mysqli->query("SELECT id FROM users WHERE id = '".$uid."'");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			getUserInfo($result->u1,4);
			$friends.='<div class="sidebar-friends" data-name="'.$sm['friend']['name'].'" data-uid="'.$sm['friend']['id'].'" data-chat="'.$sm['friend']['id'].'"
			data-all="1" data-fan="1" data-conv="'.checkConv($uid,$sm['friend']['id']).'" data-message="'.getNewMessages($uid,$sm['friend']['id']).'" data-status="'.userFilterStatus($sm['friend']['id']).'">
			<a href="javascript:;" data-uid="'.$sm['friend']['id'].'" ><div class="friend-list"><div class="profile-photo" data-src="'.$sm['friend']['profile_photo'].'"></div><h3>'.$sm['friend']['name'].'</h3><span class="'.$sm['friend']['status'].'"></span></div></a></div> ';
		}	
	}
	return $friends;
}
function searchFriends($uid,$filter){	
	global $mysqli,$sm;
	$search = '';
	$query = $mysqli->query("SELECT id FROM users WHERE name LIKE '%$filter%' OR screen_name LIKE '%$filter%' order by id DESC LIMIT 1500");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			getUserInfo($result->id,6);
			if(isFriend($uid,$sm['search']['id']) == 0){
				$search.='<div class="sidebar-friends" data-name="'.$sm['search']['name'].'" data-uid="'.$sm['search']['id'].'" 
				data-twitter="'.isFriend($uid,$sm['search']['id']).'" data-friend="'.isFriend($sm['search']['id'],$uid).'" data-conv="'.checkConv($uid,$sm['search']['id']).'" data-message="'.getNewMessages($uid,$sm['search']['id']).'"  data-status="'.userFilterStatus($sm['search']['id']).'">
				<a href="javascript:;" data-id="'.$sm['search']['screen_name'].'" data-uid="'.$sm['search']['id'].'" data-invite="'.$sm['search']['invite'].'"><div class="friend-list"><img src="'.$sm['search']['profile_photo'].'" alt="" /><h3>'.$sm['search']['name'].'</h3><span class="'.$sm['search']['status'].'"></span></div></a></div> ';
			}
		}	
	}	
	return $search;
}
function isFan($uid1,$uid2) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u1 = '".$uid1."' and u2 = '".$uid2."' and love >= 1");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}
function isStoryPurchased($sid,$uid) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_story_purchase where sid = '".$sid."' and uid = '".$uid."'");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}
function isBlocked($uid1,$uid2) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM black_list where u2 = '".$uid1."' and u1 = '".$uid2."'");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}
function clearMessageBR($content){
	$content = str_replace('<br>', '', $content);
	$content = str_replace('</br>', '', $content);
    $content = str_replace('&amp;lt;br&amp;gt;', '', $content);
    $content = str_replace('&lt;br&gt;', '', $content);	
    return $content;
}
function getChat($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$text = "";
	$next = 0;
	$last = 0;
	
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."'
								OR r_id = '".$uid1."' and s_id = '".$uid2."' ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$m = $spotl->message;
			$m = clearMessageBR($m);
			$message = $spotl->message;
			$continue = true;

            $content = $m;

            $content = str_replace('<br>', '', $content);
            $content = str_replace('</br>', '', $content);
			$message = str_replace('<br>', '', $message);
           

            $lightboxStart = '';
            $lightboxEnd = '';
            if($spotl->photo == 1){
                $content = '
                <div class="message__pic_ s-lightbox" data-s-lightbox-original="'.$m.'" style="cursor:pointer;">
                	<img src="'.$m.'" />
                </div>';
            }

            if($spotl->photo == 2){
            $viewVideo = "'".$m."'";
            $content = '
            <div class="message__pic_" style="cursor:pointer" onclick="viewVideo('.$viewVideo.')">
				<video autoplay muted width="100%" height="100%">
					<source src="'.$m.'">
				</video>
            </div>';
            }            

            if($spotl->gift >= 1){
                $content = '<div class="message__pic_" style="cursor:pointer;border:none">
                	<img  src="'.$m.'" />
                </div>';
            }                

            if($spotl->gif == 1){
                $content = '<div class="message__pic_" style="cursor:pointer;border:none">
                	<img  src="'.$m.'" />
                </div>';
            }            

            if($spotl->story > 0){
            	$story = getDataArray('users_story','id = '.$spotl->story);
                if($story['storyType'] == 'video'){
                    $content = '<div class="message__pic_" style="cursor:pointer;">
						<video data-chat-video-story="'.$story['id'].'" src="'.$story['story'].'" type="video/mp4" muted preload style="position:absolute;top:0;left:0;width:100%;height:100%"></video>
                    </div>
                    <span class="message__fromStory" style="opacity:.6;font-size:11px;margin-bottom:10px">
                    	'.$sm['lang'][663]['text'].'</span><br>
                    <span class="message__fromStoryMessage">'.$m.'</span>';
                } else {
                    $content = '<div class="message__pic_" style="cursor:pointer;">
                        <img  src="'.$story['story'].'" />
                    </div>
                    <span class="message__fromStory" style="opacity:.6;font-size:11px;margin-bottom:10px">
                    	'.$sm['lang'][663]['text'].'</span><br>
                    <span class="message__fromStoryMessage">'.$m.'</span>';
                }
            }    

			if($continue == true){
				if($uid1 == $spotl->s_id) {
					$chat.='<div class="js-message-block" id="me">
							<div class="message">
								<div class="brick brick--xsm brick--hover">
									<div class="brick-img profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
								</div>
								<div class="message__txt" >
									<span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", $spotl->time).'</span>
									<div class="message__name lgrey">'.$sm['user']['first_name'].'</div>
									<p class="montserrat chat-text">'.$lightboxStart.$content.$lightboxEnd.'</p>
								</div>
							</div>
						</div>	
					';					
					$text = "";
				} else {
					$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$uid2."' and r_id = '".$uid1."'");	
					$chat.='<div class="js-message-block" id="you">
							<div class="message">
								<div class="brick brick--xsm brick--hover">
									<div class="brick-img profile-photo" data-src="'.$sm['profile']['profile_photo'].'"></div>
								</div>
								<div class="message__txt">
									<span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", $spotl->time).'</span>
									<div class="message__name lgrey" >'.$sm['profile']['first_name'].'</div>
									<p class="montserrat chat-text">'.$lightboxStart.$content.$lightboxEnd.'</p>
								</div>
							</div>
						</div>	
					';					
					$text = "";
				}
			}
		}	
	}
	return $chat;
}

function getChatControlPanel($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$text = "";
	$next = 0;
	$last = 0;
	
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."'
								OR r_id = '".$uid1."' and s_id = '".$uid2."' ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$m = $spotl->message;
			$message = $spotl->message;
			$continue = true;

            $content = $m;

            if($spotl->photo == 1){
                $content = '
	            <a href="'.$m.'" class="avatar avatar-xxl avatar-4by3 mt-2 s-lightbox" data-s-lightbox-group="gallery" data-s-lightbox-caption="">
	                <img src="'.$m.'" alt="image" class="avatar-img rounded" style="width:150px">
	            </a>';
            }

            if($spotl->gift >= 1){
                $content = '<img src="'.$m.'" alt="image" class="avatar-img rounded" style="width:150px">';
            }                

            if($spotl->gif == 1){
                $content = '<img src="'.$m.'" alt="image" class="avatar-img rounded" style="width:150px">';
            }

            $arr = array();
	        $arr['action'] = 'deleteChatMessage';
	        $arr['id'] = $spotl->id;
	        $deleteMedia = json_encode($arr,JSON_UNESCAPED_UNICODE);
            $onClickDeleteMessage = "'adminDeleteData(".$deleteMedia.")'";

            if($spotl->story > 0){
            	$story = getDataArray('users_story','id = '.$spotl->story);
                if($story['storyType'] == 'video'){
                    $content = '<div class="message__pic_"  style="cursor:pointer;width:150px>
						<video data-chat-video-story="'.$story['id'].'" src="'.$story['story'].'" type="video/mp4" muted preload style="position:absolute;top:0;left:0;width:100%;height:100%"></video>
                    </div>
                    <span style="opacity:.6;font-size:11px;margin-bottom:10px">
                    	'.$sm['lang'][663]['text'].'</span><br>
                    '.$m;
                } else {
                    $content = '<img src="'.$story['story'].'" class="avatar-img rounded" style="width:150px"><br>
                    <span style="opacity:.6;font-size:11px;margin-bottom:10px">
                    	'.$sm['lang'][663]['text'].'</span><br>
                    '.$m;
                }
            }    

			if($continue == true){
				if($uid1 == $spotl->s_id) {
					$chat.='
                    <div class="media py-3" data-chat-id="'.$spotl->id.'">
                        <a href="#" class="avatar avatar-sm mr-3">
                            <img src="'.profilePhoto($uid1).'" class="avatar-img rounded-circle" alt="avatar">
                        </a>
                        <div class="media-body">
                            <div class="d-flex align-items-center">
                                <div class="flex">
                                    <a href="#" class="text-body bold">'.getUserData($uid1,"name").'</a>
                                </div>                               
                                <small class="text-muted" style="font-size:11px">
                                	'.time_elapsed_string($spotl->time).'
                                <button type="button" onclick='.$onClickDeleteMessage.' class="btn btn-sm btn-light" style="width:38px">
                                    <i class="material-icons">delete_forever</i>
                                </button>                                 	
                                </small>
                            </div>
                            <div>'.$content.'</div>
                        </div>
                    </div>	
					';					
					$text = "";
				} else {
					$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$uid2."' and r_id = '".$uid1."'");	
					$chat.='
	                    <div class="media py-3" data-chat-id="'.$spotl->id.'">
	                        <a href="#" class="avatar avatar-sm mr-3">
	                            <img src="'.profilePhoto($spotl->s_id).'" class="avatar-img rounded-circle" alt="avatar">
	                        </a>
	                        <div class="media-body">
	                            <div class="d-flex align-items-center">
	                                <div class="flex">
	                                    <a href="#" class="text-body bold">'.getUserData($spotl->s_id,"name").'</a>
	                                </div>
	                                <small class="text-muted" style="font-size:11px">'.time_elapsed_string($spotl->time).'
		                                <button type="button" onclick='.$onClickDeleteMessage.' class="btn btn-sm btn-light" style="width:38px">
		                                    <i class="material-icons">delete_forever</i>
		                                </button>
	                                </small>
	                            </div>
	                            <div>'.$content.'</div>
	                        </div>
	                    </div>	
					';					
					$text = "";
				}
			}
		}	
	}
	return $chat;
}
function getLastChat($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches)){
				$icons = $matches[1];
				foreach ($icons as $icon){
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<div class="js-message-block" id="me">
						<div class="message">
							<div class="brick brick--xsm brick--hover">
								<div class="brick-img profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
							</div>
							<div class="message__txt">
								<span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", $spotl->time).'</span>
								<div class="message__name lgrey">'.$sm['user']['first_name'].'</div>
								<p class="montserrat chat-text">'.$message.'</p>
							</div>
						</div>
					</div>	
				';
			}else {
				$chat.='<div class="js-message-block" id="you">
						<div class="message">
							<div class="brick brick--xsm brick--hover">
								<div class="brick-img profile-photo" data-src="'.$sm['profile']['profile_photo'].'"></div>
							</div>
							<div class="message__txt">
								<span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", $spotl->time).'</span>
								<div class="message__name lgrey">'.$sm['profile']['first_name'].'</div>
								<p class="montserrat chat-text">'.$message.'</p>
							</div>
						</div>
					</div>	
				';
			}
		}	
	}
	return $chat;
}
function getLastChatMobile($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<li class="list-chat right" data-ix="list-item" id="me" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
						<div class="w-clearfix column-right chat right">
						  <div class="arrow-globe right"></div>
						  <div class="chat-text right">'.$message.'</div>
						</div>
					  </li>';
			}else {
				$chat.='<li class="w-clearfix list-chat" data-ix="list-item" id="you" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
				<div class="column-left chat"><div class="image-message chat">
				<img src="'.$sm['chat']['profile_photo'].'"></div></div>
<div class="w-clearfix column-right chat">
  <div class="arrow-globe"></div>
  <div class="chat-text">'.$message.' </p></div></div></li>';	
			}
		}	
	}
	return $chat;
}
function getLastChatMobile2($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' and seen = 0 ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<li class="list-chat right" data-ix="list-item" id="me" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
						<div class="w-clearfix column-right chat right">
						  <div class="arrow-globe right"></div>
						  <div class="chat-text right">'.$message.'</div>
						</div>
					  </li>';
			}else {
				$chat.='<li class="w-clearfix list-chat" data-ix="list-item" id="you" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
				<div class="column-left chat"><div class="image-message chat">
				<img src="'.$sm['chat']['profile_photo'].'"></div></div>
<div class="w-clearfix column-right chat">
  <div class="arrow-globe"></div>
  <div class="chat-text">'.$message.' </p></div></div></li>';	
			}
		}	
	}
	return $chat;
}
function getLastMessage($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' and seen = 0 ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<div class="post"><div class="left" id="me"><div class="profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
				<h1>'.$sm['user']['name'].'</h1>';
				if($spotl->photo == 1){
					$chat.='<div class="photos1"><div class="post-photo1" data-instance="photo'.$spotl->time.'" src="'.$message.'" data-src="'.$message.'"></div></div></div></div>';
				}else {
					$chat.='<p class="me">'.$text.$message.' </p></div></div>';
				}				
			}else {
				$chat.='<div class="post"><div class="left" id="you"><div class="profile-photo" data-src="'.$sm['chat']['profile_photo'].'"></div>
				<h1>'.$sm['chat']['name'].'</h1> ';
				if($spotl->photo == 1){
					$chat.='<div class="photos1"><div class="post-photo1" data-instance="photo'.$spotl->time.'" src="'.$message.'" data-src="'.$message.'"></div></div></div></div>';
				} else {
					$chat.='<p class="you">'.$text.$message.' </p></div></div>';
				}
			}
		}	
	}
	return $chat;
}
function getLastMessageMobile($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			if($spotl->photo == 1 ){
				$message = '<i class="mdi-image-photo-camera"></i>';		
			}
			else if($spotl->access == 1 ){
				$message = $sm['lang'][174]['text'];		
			}	else {
				$message = $spotl->message;			
			}
		}	
	}
	return $message;
}
function getLastMessageMobileTime($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT time FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = time_elapsed_string($spotl->time);
		}	
	}
	return $message;
}
function getLastMessageMobileSeen($uid1,$uid2){	
	global $mysqli,$sm;
	$message = '';
	$spotlight = $mysqli->query("SELECT seen,s_id FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			if($spotl->s_id == $uid1 && $spotl->seen == 0){
				$message = '<i class="mdi-navigation-check" style="color:#488ad8"></i>';
			}
			if($spotl->s_id == $uid1 && $spotl->seen == 1){
				$message = '<i class="mdi-navigation-check" style="color:#13d213"></i><i class="mdi-navigation-check" style="color:#13d213"></i>';
			}
			if($spotl->s_id != $uid1 && $spotl->seen == 0){
				$message = '<i class="mdi-image-brightness-1" style="color:#13d213"></i>';
			}
			if($spotl->s_id != $uid1 && $spotl->seen == 1){
				$message = '<i class="mdi-image-brightness-1" style="color:#488ad8"></i>';
			}			
		}	
	}
	return $message;
}
function cleanMessage($message){	
	global $mysqli,$sm;
	$chat = '';
	if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
	{
		$icons = $matches[1];
		foreach ($icons as $icon)
		{
			$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
		}
	}		
	$chat = $message;				
	return $chat;
}
function cleanMessageById($id){	
	global $mysqli,$sm;
	$chat = '';
	$cm = $mysqli->query("SELECT message FROM chat WHERE id = '".$id."'");
	$men = $cm->fetch_object();
	$message = $men->message;
	if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
	{
		$icons = $matches[1];
		foreach ($icons as $icon)
		{
			$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
		}
	}		
	$chat = $message;				
	return $chat;
}
function getAdminLang(){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM languages ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$lang.= '<div class="col-md-3" style="height:50px;">
						<a href="index.php?page=admin&p=editlang&id='.$result->id.'" class="btn btn-primary" style="padding:10px;">
							'.$result->name.'
						</a>';
						if($result->visible == 1 ){ 
						$lang.='
							<div class="onoffswitch">
								<input type="checkbox" data-lang-switch="1" data-lang-id="'.$result->id.'" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch'.$result->id.'" checked>
								<label class="onoffswitch-label" for="myonoffswitch'.$result->id.'"></label>
							</div>';
						} else {
						$lang.='
							<div class="onoffswitch">
								<input type="checkbox" name="onoffswitch" data-lang-switch="0"  data-lang-id="'.$result->id.'" class="onoffswitch-checkbox" id="myonoffswitch'.$result->id.'">
								<label class="onoffswitch-label" for="myonoffswitch'.$result->id.'"></label>
							</div>';
						}						
					$lang.=' </div>';
		}	
	}
	return $lang;
}
function getAdminLangEdit($id,$table='',$landing=''){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM $table where lang_id = '$id' AND text <> '' $landing ORDER BY id ASC");

	if($id != 1){
		$english = $mysqli->query("SELECT * FROM $table where lang_id = 1 AND text <> '' $landing ORDER BY id ASC");
	}
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){		
			$filter = strtolower($result->text);
			$text = "'".secureEncode($result->text)."'";
			$customId = "'lang'";
			$theme = 'No';
			if($table == 'site_lang' || $table == 'landing_lang'){
				$theme = $result->theme;
			}
			$lang.= '<div class="col-md-4" style="height:50px;" data-filter-lang="'.$filter.'">
				<div class="search-form search-form--light">
	                <input class="form-control" value="'.$result->text.'" data-lang-text="'.secureEncode($result->text).'" id="lang'.$result->id.'" data-langid="'.$result->lang_id.'" data-edit-lang="'.$result->id.'" data-theme="'.$theme.'" data-idVal="lang" data-lid="'.$result->id.'" />';
	                if($result->lang_id != 1){ 
	                	$lang.= '
		                <button class="btn" type="button" role="button" data-toggle="tooltip" data-placement="top" title="Auto-translate" data-original-title="Auto-translate"
		                	onclick="autoTranslateSingle('.$text.','.$customId.','.$result->id.');">
		                	<i class="material-icons">g_translate</i>
		                </button>';	                	
	                }
	                $lang.= '
	            </div>			
			</div>';
		}	
	}
	return $lang;
}
function getAdminLangEditSeo($id,$table){	
	global $mysqli,$sm;
	$lang = '';
	$page = '';
	$query = $mysqli->query("SELECT * FROM $table where lang_id = '$id' AND text <> '' ORDER BY page ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$filter = strtolower($result->text);
			$text = "'".secureEncode($result->text)."'";
			$customId = "'lang'";
			if($result->page != $page){
				$lang.='
					<div class="col-md-12">
		                <div class="card card-form flex-column flex-sm-row divHidden" style="height: 60px;width: 100%;">
		                    <div class="card-form__body card-body-form-group flex">
		                      <div class="row">
		                          <div class="col-sm-12"> 
		                              <div class="form-group">
		                                  <label>Page: '.$result->page.'</label>                    
		                              </div>
		                          </div>                                                 
		                      </div>
		                    </div>              
		                </div>					
					</div>
				';
				$page = $result->page;
			}
			$lang.= '<div class="col-md-4" style="height:50px;" data-filter-lang="'.$filter.'">
				<div class="search-form search-form--light">
	                <input class="form-control" value="'.$result->text.'" data-lang-text="'.secureEncode($result->text).'" id="lang'.$result->id.'" data-page="'.$result->page.'" data-langid-seo="'.$result->lang_id.'" data-idVal="lang" data-lid="'.$result->id.'" data-lang-loading="'.$result->id.$result->page.'" />';
	                $lang.= '
	            </div>			
			</div>';
		}	
	}
	return $lang;
}
function getAdminLangEditGender($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM config_genders where lang_id = '$id' ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$filter = strtolower($result->name);
			$text = "'".secureEncode($result->name)."'";
			$customId = "'langGender'";
			$lang.= '<div class="col-md-4" style="height:50px;" data-filter-lang="'.$filter.'">
				<div class="search-form search-form--light">
					<input class="form-control" value="'.$result->name.'" id="langGender'.$result->id.'" data-langid-g="'.$result->lang_id.'" data-idVal="langGender" data-lang-text="'.secureEncode($result->name).'" data-edit-lang="'.$result->id.'" data-gid="'.$result->id.'" />';
	                if($result->lang_id != 1){ 
	                	$lang.= '
		                <button class="btn" type="button" role="button" data-toggle="tooltip" data-placement="top" title="Auto-translate" data-original-title="Auto-translate"
		                	onclick="autoTranslateSingle('.$text.','.$customId.','.$result->id.');">
		                	<i class="material-icons">g_translate</i>
		                </button>';	                	
	                }
	                $lang.= '
	            </div>			
			</div>';			
		}	
	}
	return $lang;
}
function getAdminLangEditQuestions($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM config_profile_questions where lang_id = '$id' ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$filter = strtolower($result->question);
			$text = "'".secureEncode($result->question)."'";
			$customId = "'langQuestion'";			
			$lang.= '<div class="col-md-4" style="height:50px;" data-filter-lang="'.$filter.'">
				<div class="search-form search-form--light">
					<input class="form-control" value="'.$result->question.'" id="langQuestion'.$result->id.'"  data-langid-q="'.$result->lang_id.'" data-lang-text="'.secureEncode($result->question).'" data-edit-lang="'.$result->id.'" data-idVal="langQuestion" data-questionid="'.$result->id.'" />';
	                if($result->lang_id != 1){ 
	                	$lang.= '
		                <button class="btn" type="button" role="button" data-toggle="tooltip" data-placement="top" title="Auto-translate" data-original-title="Auto-translate"
		                	onclick="autoTranslateSingle('.$text.','.$customId.','.$result->id.');">
		                	<i class="material-icons">g_translate</i>
		                </button>';	                	
	                }
	                $lang.= '
	            </div>			
			</div>';			
		}	
	}
	return $lang;
}
function getAdminLangEditAnswers($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM config_profile_answers where lang_id = '$id' ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$filter = strtolower($result->answer);
			$text = "'".secureEncode($result->answer)."'";
			$customId = "'langAnswer'";
			$lang.= '<div class="col-md-4" style="height:50px;" data-filter-lang="'.$filter.'">
				<div class="search-form search-form--light">
					<input class="form-control" id="langAnswer'.$result->qid.$result->id.'" value="'.$result->answer.'" data-edit-lang="'.$result->qid.$result->id.'" data-langid-answer="'.$result->lang_id.'" data-qid="'.$result->qid.'" data-idVal="langAnswer" data-lang-text="'.secureEncode($result->answer).'" data-answerid="'.$result->id.'" data-answeid="'.$result->id.''.$result->qid.'" />';
	                if($result->lang_id != 1){ 
	                	$lang.= '
		                <button class="btn" type="button" role="button" data-toggle="tooltip" data-placement="top" title="Auto-translate" data-original-title="Auto-translate"
		                	onclick="autoTranslateSingle('.$text.','.$customId.','.$result->qid.$result->id.');">
		                	<i class="material-icons">g_translate</i>
		                </button>';	                	
	                }
	                $lang.= '
	            </div>			
			</div>';			
		}	
	}
	return $lang;
}
function getAdminLangEditEmail($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM email_lang where lang_id = '$id' ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$lang.= '<div class="col-md-2" style="height:50px;"><input class="form-control" value="'.$result->text.'"  data-langid-mail="'.$result->lang_id.'" data-emailid="'.$result->id.'" /></div>';
		}	
	}
	return $lang;
}
function getUserTotalPhotos($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and approved = 1");
	if($query->num_rows > 0){
		$total = $query->fetch_assoc();
	} else {
		$total['total'] = 0;
		if($total['total'] == 0){
			$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and profile = 1");
			if($query->num_rows > 0){
				$total = $query->fetch_assoc();
			} else {				
				$total['total'] = 0;
			}	
		}
	}
	return $total['total'];
}
function getUserTotalConv($u1,$u2) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM chat where r_id = '".$u1."' AND s_id = '".$u2."' OR r_id = '".$u2."' AND s_id = '".$u1."'");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getUserTodayConv($uid) {
	global $mysqli;
	$date = date('m/d/Y', time());
	$count = 0;
	$query = $mysqli->query("SELECT count FROM users_chat where uid = '".$uid."' and date = '".$date."' ");
	if($query->num_rows > 0){
		$total = $query->fetch_object();
		$count = $total->count;
	}
	return $count;
}
function getUserTotalPhotosPublic($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and blocked = 0 and approved = 1 and video = 0");
	if($query->num_rows > 0){
		$total = $query->fetch_assoc();
	}else {
		$total['total'] = 0;	
	}
	return $total['total'];
}
function getUserTotalPhotosPrivate($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and blocked = 1 and video = 0");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getUserTotalLikers($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u2 = '".$uid."' and love = 1");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getUserLikePercent($likes,$nolikes){
	if($likes > 0){
		$percentage = ($likes / $nolikes) * 100;	
		return $percentage;		
	} else {
		return 0;		
	}
}
function getPercentage($total, $number){
  if ( $total > 0 ) {
   return round($number / ($total / 100),2);
  } else {
    return 0;
  }
}
function getUserTotalNoLikers($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u2 = '".$uid."' and love = 0");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getUserTotalLikes($uid) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u1 = '".$uid."' and love = 1");
	$total = $query->fetch_assoc();
	return $total['total'];
}
function getNewMessages($uid1,$uid2) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM chat where r_id = '".$uid1."' and s_id = '".$uid2."' and seen = 0");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = $total['total'];
	}
	return $result;
}
function checkConv($uid1,$uid2) {
	global $mysqli;
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM chat where s_id = '".$uid1."' and r_id = '".$uid2."'");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}


/* Other functions */
function smoothLink($query='') {
    global $sm;
    $query = $sm['config']['site_url'] . $query;
    return $query;
}
function secureEncode($string,$strip=1) {
    global $mysqli;
    $string = trim($string);
    $string = mysqli_real_escape_string($mysqli, $string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = str_replace('\\r\\n', '<br>',$string);
    $string = str_replace('\\r', '<br>',$string);
    $string = str_replace('\\n\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    if ($strip == 1) {
        $string = stripslashes($string);
    }
    $string = str_replace('&amp;#', '&#',$string);
    return $string;
}

function callInstagram($url){
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_SSL_VERIFYHOST => 2
	));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function time_elapsed_string($ptime){
	global $sm;
    $etime = time() - $ptime;
    if ($etime < 1){
        return $sm['lang'][306]['text'];
    }
    $a = array( 365 * 24 * 60 * 60  =>  $sm['lang'][307]['text'],
                 30 * 24 * 60 * 60  =>  $sm['lang'][308]['text'],
                      24 * 60 * 60  =>  $sm['lang'][309]['text'],
                           60 * 60  =>  $sm['lang'][310]['text'],
                                60  =>  $sm['lang'][311]['text'],
                                 1  =>  $sm['lang'][312]['text']
                );
    $a_plural = array( $sm['lang'][307]['text']   => $sm['lang'][313]['text'],
                       $sm['lang'][308]['text']  => $sm['lang'][314]['text'],
                       $sm['lang'][309]['text']    => $sm['lang'][315]['text'],
                       $sm['lang'][310]['text']   => $sm['lang'][316]['text'],
                       $sm['lang'][311]['text'] => $sm['lang'][317]['text'],
                       $sm['lang'][312]['text'] => $sm['lang'][318]['text']
                );
    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' '.$sm['lang'][319]['text'];
        }
    }
}
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
function prefered_language(array $available_languages, $http_accept_language) {
    $available_languages = array_flip($available_languages);
    $langs = [];
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
    foreach($matches as $match) {
        list($a, $b) = explode('-', $match[1]) + array('', '');
        $value = isset($match[2]) ? (float) $match[2] : 1.0;
        if(isset($available_languages[$match[1]])) {
            $langs[$match[1]] = $value;
            continue;
        }
        if(isset($available_languages[$a])) {
            $langs[$a] = $value - 0.1;
        }
    }
    arsort($langs);
    return $langs;
}
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getSiteInterests(){
	global $mysqli;
	$result=array();
	$interests = $mysqli->query("SELECT * FROM interest order by count desc LIMIT 200");
	if ($interests->num_rows > 0) { 
		while($up = $interests->fetch_assoc()){
			$result[] = $up;
		}
	}		
	return $result;	
}

function orderByTime($a, $b){
    $t1 = $a['time'];
    $t2 = $b['time'];
    return $t1 - $t2;
}  

function validateURL($url){ 
	if(filter_var($url, FILTER_VALIDATE_URL)) {  
		return true;
	} else { 
		return false;
	} 
}

function checkUrlBar($url){
	$check_bar = substr($url, -1);
	if($check_bar != '/'){
		$url = $url.'/';	
	}
	return $url;
}

function getMaximumFileUploadSize(){  
    return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));  
}  
function convertPHPSizeToBytes($sSize){
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix,array('P','T','G','M','K'))){
        return (int)$sSize;  
    } 
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P':
            $iValue *= 1024;
        case 'T':
            $iValue *= 1024;
        case 'G':
            $iValue *= 1024;
        case 'M':
            $iValue *= 1024;
        case 'K':
            $iValue *= 1024;
            break;
    }
    return (int)$iValue;
}

function validate_username($input){
	return preg_match('/^[a-zA-Z0-9_.]+$/D',$input);
}