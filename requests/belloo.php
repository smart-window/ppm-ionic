<?php
/* Belloo By Xohan - xohansosa@gmail.com */
header('Content-Type: application/json');
require_once('../assets/includes/core.php');

if(!empty($sm['user']['id'])){
	$uid = $sm['user']['id'];	
}
switch ($_POST['action']) {
	case 'skipUploadPhoto':
		$_SESSION['skipUploadPhoto'] = true;
	break;
	case 'updateVerifyEmail':
		$arr = array();
		$email = secureEncode($_POST['email']);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$arr['error'] = 1;
			$arr['reason'] = $sm['lang'][181]['text'];				
			echo json_encode($arr);	
			exit;	
		}			
		$email_check = $mysqli->query("SELECT email FROM users WHERE email = '".$email."'");	
		if($email_check->num_rows == 1 ){
			$arr['error'] = 1;
			$arr['reason'] = $sm['lang'][188]['text'];			
		} else {
			$arr['error'] = 0;
			$mysqli->query("UPDATE users set email = '".$email."' where id = '".$sm['user']['id']."'");
		}		
		echo json_encode($arr);
	break;
	case 'update_user_meet':
		$lat = secureEncode($_POST['lat']);
		$lng = secureEncode($_POST['lng']);		
		$city = secureEncode($_POST['city']);					
		$country = secureEncode($_POST['country']);		
		$mysqli->query("UPDATE users set lat = '".$lat."', lng = '".$lng."', city = '".$city."', country = '".$country."' where id = '".$sm['user']['id']."'");
		if($sm['theme']['design_style_wide']['val'] == 'Yes'){
			echo spotlightMobile($lat,$lng,1);		
		} else {
			echo spotlightMobile($lat,$lng,0);			
		}
	break;
	case 'uploadMedia':
		$media = $_POST['media']; //THE DATA IS SECURED IN LINE 60-61
		$f = 0;
		$time = time();
		$arr = array();

		$photoReview = 1;
		if($sm['plugins']['settings']['photoReview'] == 'Yes' && !isset($_POST['adminPanel'])){
			$photoReview = 0;			
		}
		if(isset($_POST['adminPanel'])){
			$uid = secureEncode($_POST['uid']);
		}
		if(!isset($_POST['album'])){
			if (is_array($media)){
				for ($i=0;$i < sizeof($media);$i++){

					$p = secureEncode($media[$i]['path']);
					$t = secureEncode($media[$i]['thumb']);

					$ig_id = 0;
					if(isset($_POST['ig_id'])){
						$ig_id = secureEncode($_POST['ig_id']);
					}
					$fake = getData('users','fake','WHERE id ='.$uid);
					if($media[$i]['video'] == 0){
		          		if($f == 0){
		          			if (\strpos($sm['user']['profile_photo'], 'no_user') !== false) {
		          				$mysqli->query("INSERT INTO users_photos (u_id,photo,thumb,approved,profile,video,time,ig_id,fake)
									VALUES ('".$uid."','".$p."', '".$t."','".$photoReview."',1,0,'".$time."','".$ig_id."','".$fake."')");	
							} else {
		          				$mysqli->query("INSERT INTO users_photos (u_id,photo,thumb,approved,video,time,ig_id,fake) VALUES ('".$uid."','".$p."', '".$t."','".$photoReview."',0,'".$time."','".$ig_id."','".$fake."')");	
							}
		          		} else {
		          			$mysqli->query("INSERT INTO users_photos 
		          				(u_id,photo,thumb,approved,video,time,ig_id,fake)
								VALUES ('".$uid."','".$p."', '".$t."','".$photoReview.",0,'".$time."','".$ig_id."','".$fake."')");	
		          		} 
					} else {
		          		$mysqli->query("INSERT INTO users_photos (u_id,photo,thumb,approved,video,time,ig_id,fake)
						VALUES ('".$uid."','".$p."', '".$t."','".$photoReview."',1,'".$time."','".$ig_id."','".$fake."')");
					}		         		
					$f++;
				}
	      	}			
		}

		$filter = 'u_id = '.$sm['user']['id'].' order by id desc limit 1';
		$arr['data'] = getDataArray('users_photos',$filter);

      	if(isset($_POST['bio'])){
      		$bio = secureEncode($_POST['bio']);
			$mysqli->query("UPDATE users SET bio = '".$bio."' WHERE id = '".$sm['user']['id']."'");    		
      	}  


      	if(isset($_POST['album'])){
      		$album = secureEncode($_POST['album']);
      		$p = $media[0]['thumb'];
      		
      		$stories = '';
      		$a = 0;
      		for ($i=0;$i < sizeof($_POST['stories']);$i++){
      			if($i+1 == sizeof($_POST['stories'])){
      				$stories.= secureEncode($_POST['stories'][$i]);
      			} else {
      				$stories.= secureEncode($_POST['stories'][$i]).',';
      			}
      		} 	

			$query = "INSERT INTO users_story_albums (uid,photo,name,stories) 
						VALUES ('".$uid."','".$p."','".$album."','".$stories."')";
			if ($mysqli->query($query) === TRUE) {
				$last_id = $mysqli->insert_id;
				$arr['stories'] = json_encode(getAlbumStories($last_id,$album,$p));
			}		
      	}

      	echo json_encode($arr);        	 
	break;

	case 'verifyAccount':
		$media = $_POST['media']; //THE DATA IS SECURED IN LINES 130-131
		$time = time();
		if (is_array($media)){
			for ($i=0;$i < sizeof($media);$i++){	
				$p = secureEncode($media[$i]['path']);
				$t = secureEncode($media[$i]['thumb']);		
          		$mysqli->query("INSERT INTO users_verification (uid,media,time) VALUES ('".$uid."','".$p."', '".$time."') 
          			ON DUPLICATE KEY UPDATE media = '".$p."',verify = 0,time = '".$time."',status = 'No'");	
			}
      	}   
	break;

	case 'uploadStory':
		$media = $_POST['media']; //THE DATA IS SECURED IN LINES 151-152
		$time = time();
		$arr = array();
		$fake = 0;
		if(isset($_POST['adminPanel'])){
			$uid = secureEncode($_POST['uid']);
			$sm['plugins']['story']['reviewStory'] = 'No';
			$fake = getData('users','fake','WHERE id ='.$uid);
		}		
		if (is_array($media)){
			for ($i=0;$i < sizeof($media);$i++){

				$p = secureEncode($media[$i]['path']);
				$t = secureEncode($media[$i]['thumb']);

				$ig_id = 0;
				
				if(isset($_POST['ig_id'])){
					$ig_id = secureEncode($_POST['ig_id']);
				}

				$type = 'image';
				$video = 0;
				$price = 0;
				if(isset($_POST['creditsPhoto'])){
					$price = secureEncode($_POST['creditsPhoto']);
				}
				if($media[$i]['video'] == 1){
					$type = 'video';
					$video = 1;
					if(isset($_POST['creditsVideo'])){
						$price = secureEncode($_POST['creditsVideo']);
					}					
				}	

				if($sm['plugins']['story']['reviewStory'] == 'No'){
					$approved = 1;
				} else {
					$approved = 0;
				}

				if(isset($_POST['uploadTo'])){

					$uploadTo = secureEncode($_POST['uploadTo']);
					if($uploadTo == 'profile'){
						$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,approved,ig_id,fake)
	          			 VALUES ('".$uid."','".$time."','".$p."','".$t."','".$video."',".$approved.",'".$ig_id."','".$fake."')");						
					}
					if($uploadTo == 'story'){
		          		$query = "INSERT INTO users_story (uid,storyTime,story,storyType,lat,lng,review,credits)
		          			 VALUES ('".$uid."','".$time."','".$p."','".$type."','".$sm['user']['lat']."','".$sm['user']['lng']."','".$sm['plugins']['story']['reviewStory']."','".$price."')";

		          		if ($mysqli->query($query) === TRUE) {
		          			$last_id = $mysqli->insert_id;
							$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,story,approved,ig_id,fake)
		          			 VALUES ('".$uid."','".$time."','".$p."','".$t."','".$video."',".$last_id.",".$approved.",'".$ig_id."','".$fake."')");

		          		}
					}
					if($uploadTo == 'All'){
		          		$query = "INSERT INTO users_story (uid,storyTime,story,storyType,lat,lng,review,credits)
		          			 VALUES ('".$uid."','".$time."','".$p."','".$type."','".$sm['user']['lat']."','".$sm['user']['lng']."','".$sm['plugins']['story']['reviewStory']."','".$price."')";

		          		if ($mysqli->query($query) === TRUE) {
		          			$last_id = $mysqli->insert_id;
							$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,approved,ig_id,fake)
	          			 VALUES ('".$uid."','".$time."','".$p."','".$t."','".$video."',".$approved.",'".$ig_id."','".$fake."')");

							$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,story,approved,ig_id,fake)
		          			 VALUES ('".$uid."','".$time."','".$p."','".$t."','".$video."',".$last_id.",".$approved.",'".$ig_id."','".$fake."')");

		          		}
					}					
				} else {
	          		$query = "INSERT INTO users_story (uid,storyTime,story,storyType,lat,lng,review)
	          			 VALUES ('".$uid."','".$time."','".$p."','".$type."','".$sm['user']['lat']."','".$sm['user']['lng']."','".$sm['plugins']['story']['reviewStory']."')";

	          		if ($mysqli->query($query) === TRUE) {
	          			$last_id = $mysqli->insert_id;
						$mysqli->query("INSERT INTO users_photos (u_id,time,photo,thumb,video,story,approved,ig_id)
	          			 VALUES ('".$uid."','".$time."','".$p."','".$t."','".$video."',".$last_id.",".$approved.",'".$ig_id."')");

	          		}
				}       			 	
			}
      	} 

		$storyFrom = $sm['plugins']['story']['days'];
		$time = time();	
		$extra = 86400 * $storyFrom;
		$storyFrom = $time - $extra;
		$storiesFilter = 'where uid = '.$uid.' and storyTime >'.$storyFrom.' and deleted = 0';      	
		$arr['story'] = selectC('users_story',$storiesFilter);
		$arr['stories'] = getUserStories($sm['user']['name'],$sm['user']['profile_photo'],$storiesFilter,'storyTime ASC');
		$arr['storiesProfile'] = json_encode(getUserStories($sm['user']['name'],$sm['user']['profile_photo'],$storiesFilter,'storyTime ASC'));		
		echo json_encode($arr);     	  
	break;

	

	case 'riseUp':
		$time = time();	
		$extra = 86400 * 5;
		$riseUp = $time + $extra;	
		$price = secureEncode($_POST['price']);
		$mysqli->query("UPDATE users set last_access = '".$riseUp."', meet = 1 where id = '".$sm['user']['id']."'");
		$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$sm['user']['id']."'";
		$mysqli->query($query2);			
	break;	
	case 'dailyChat':	
		$price = secureEncode($_POST['price']);
		$mysqli->query("DELETE FROM users_chat where uid = '".$sm['user']['id']."'");			
		$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$sm['user']['id']."'";
		$mysqli->query($query2);
	break;		
	case 'online_now': 
        $query = 'id='.$sm['user']['id'];
        $action = 'getOnlineFriends';
        $lastm = apiCall($action,$query);
        $arr = array();  
        $arr['r'] = '';                                    
        foreach ($lastm['result'] as $val) { 
        	$arr['r'].='
            <a href="#"  onClick="goToProfile('.$val['id'].')"><li class="chat__human" style="margin-bottom: 5px;">
                <img class="chat__avatar" style="padding: 1px;margin:3px" src="'.$val['photo'].'" alt="" />
                <span class="chat__name comforta" style="position: absolute;margin-left:5px;margin-top: 5px;font-weight: bold">'.$val['firstName'].'</span>
                <span class="chat__last_m" style="position: absolute;margin-left:5px;margin-top: 23px;color:#999"> '.$val['last_m'].'</span>
            </li></a>';
       }
       $arr['total'] = $lastm['total_online'];
       echo $arr;	
	break;
	case 'discover100':
		$time = time();	
		$extra = 86400 * 5;
		$riseUp = $time + $extra;	
		$price = secureEncode($_POST['price']);
		$mysqli->query("UPDATE users set last_access = '".$riseUp."', discover = 100 where id = '".$sm['user']['id']."'");
		$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$sm['user']['id']."'";
		$mysqli->query($query2);			
	break;
				
	case 'wall':
		$id = secureEncode($_POST['id']);
		$b = secureEncode($_POST['b']);
		getUserInfo($id,1);	
		$check = blockedUser($sm['user']['id'],$sm['profile']['id']);
		if($check == 1){
			echo '<script>alert("'.$sm['profile']['first_name'].' '.$sm['lang'][865]['text'].'");</script>';
			getUserInfo($uid,1);	
			$sm['content'] = requestPage('profile/content');
			echo $sm['content'];				
			exit;
		}
		$sm['content'] = requestPage('profile/content');
		echo $sm['content'];		
	break;

	case 'themeStylesReload':
		$preset = secureEncode($_POST['preset']);
		$theme = secureEncode($_POST['theme']);
        $themeFilter = 'WHERE theme = "'.$theme.'" AND preset = "'.$preset.'"';
        $sm['theme'] = json_decode(getData('theme_preset','theme_settings',$themeFilter),true);
		$sm['styles'] = requestPage('styles');
		echo $sm['styles'];		
	break;

	case 'wall-header':
		$id = secureEncode($_POST['id']);
		getUserInfo($id,1);	
		$check = blockedUser($sm['user']['id'],$sm['profile']['id']);
		if($check == 1){
			echo '<script>alert("'.$sm['profile']['first_name'].' is blocking you,sorry you cant contact her");</script>';
			getUserInfo($uid,1);	
			$sm['content'] = requestPage('profile/content-header');
			echo $sm['content'];				
			exit;
		}		
		$sm['content'] = requestPage('profile/content-header');
		echo $sm['content'];		
	break;	
	case 'game':
		$e_age = explode( ',', $sm['user']['s_age'] );
		$age1 = $e_age[0];
		$age2 = $e_age[1];
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
		} else {
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
				if($sm['user']['id'] != $u_t->id){
					$array1[] = $u_t->id;				
				}		
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
		$resultado = array_slice($resultado, 0, 20);
		$user_g = array_shift($resultado);
		$i=0;
		$info = array();
		if($user_g == 0){
			if($gender == $all){
				$user_game = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'				  
						  ORDER BY distance ASC, last_access DESC
						  LIMIT 20");
						$sexy_game = $user_game->fetch_object();
						$photo = profilePhoto($sexy_game->id);
						$storyFrom = $sm['plugins']['story']['days'];
						$time = time();	
						$extra = 86400 * $storyFrom;
						$storyFrom = $time - $extra;
						$storiesFilter = 'where uid = '.$sexy_game->id.' and storyTime >'.$storyFrom.' and deleted = 0';					
						$info[] = array(
							  "id" => $sexy_game->id,
							  "name" => $sexy_game->name,
							  "status" => userFilterStatus($sexy_game->id),
							  "distance" => distance($sm['user']['lat'],$sm['user']['lng'],$sexy_game->lat,$sexy_game->lng),				  
							  "age" => $sexy_game->age,
							  "city" => $sexy_game->city,
							  "bio" => $sexy_game->bio,				  
							  "photos" => getUserPhotosAll($sexy_game->id),	  
							  "total" => getUserTotalLikers($sexy_game->id),
							  "photo" => $photo,
							  "story" => selectC('users_story',$storiesFilter),
							  "stories" => getUserStories($sexy_game->name,$photo,$storiesFilter,'storyTime ASC')
						);
			} else {
				$user_game = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'
						  AND gender = '".$sm['user']['s_gender']."'					  
						  ORDER BY distance ASC, last_access DESC
						  LIMIT 20");
						$sexy_game = $user_game->fetch_object();
						$photo = profilePhoto($sexy_game->id);
						$storyFrom = $sm['plugins']['story']['days'];
						$time = time();	
						$extra = 86400 * $storyFrom;
						$storyFrom = $time - $extra;
						$storiesFilter = 'where uid = '.$sexy_game->id.' and storyTime >'.$storyFrom.' and deleted = 0';					
						$info[] = array(
							  "id" => $sexy_game->id,
							  "name" => $sexy_game->name,
							  "status" => userFilterStatus($sexy_game->id),
							  "distance" => distance($sm['user']['lat'],$sm['user']['lng'],$sexy_game->lat,$sexy_game->lng),				  
							  "age" => $sexy_game->age,
							  "city" => $sexy_game->city,
							  "bio" => $sexy_game->bio,				  
							  "photos" => getUserPhotosAll($sexy_game->id),	  
							  "total" => getUserTotalLikers($sexy_game->id),
							  "photo" => $photo,
							  "story" => selectC('users_story',$storiesFilter),
							  "stories" => getUserStories($sexy_game->name,$photo,$storiesFilter,'storyTime ASC')
						);					  
			}			
		} else {
			foreach($resultado as $user_g){
				$user_game = $mysqli->query("SELECT id,name,city,age,bio,lat,lng FROM users WHERE id = '".$user_g."'");
				$sexy_game = $user_game->fetch_object();
				$photo = profilePhoto($sexy_game->id);
				$storyFrom = $sm['plugins']['story']['days'];
				$time = time();	
				$extra = 86400 * $storyFrom;
				$storyFrom = $time - $extra;
				$storiesFilter = 'where uid = '.$sexy_game->id.' and storyTime >'.$storyFrom.' and deleted = 0';					
				$info[] = array(
					  "id" => $sexy_game->id,
					  "name" => $sexy_game->name,
					  "status" => userFilterStatus($sexy_game->id),
					  "distance" => distance($sm['user']['lat'],$sm['user']['lng'],$sexy_game->lat,$sexy_game->lng),				  
					  "age" => $sexy_game->age,
					  "city" => $sexy_game->city,
					  "bio" => $sexy_game->bio,				  
					  "photos" => getUserPhotosAll($sexy_game->id),	  
					  "total" => getUserTotalLikers($sexy_game->id),
					  "photo" => $photo,
					  "story" => selectC('users_story',$storiesFilter),
					  "stories" => getUserStories($sexy_game->name,$photo,$storiesFilter,'storyTime ASC')
				);
			}			
		}	

		echo json_encode($info);

	break;	
	case 'del_conv':
		$sid = secureEncode($_POST['id']);
		$mysqli->query("UPDATE chat set seen = 2 WHERE r_id = '".$uid."' AND s_id = '".$sid."'");
		$mysqli->query("UPDATE chat set notification = 2 WHERE s_id = '".$uid."' AND r_id = '".$sid."'");		
	break;		
}
$mysqli->close();