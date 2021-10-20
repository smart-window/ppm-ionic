<?php
/* Belloo By Xohan - xohansosa@gmail.com */
header('Content-Type: application/json');
require_once('../assets/includes/core.php');
function getFakeUsersChat($tab) {
	global $mysqli,$sm;

	$return = array();
	$time_now = time()-300;
	$timestamp = time();
	$dw = date( "w", $timestamp);

	if($tab == 'New'){
		$query = $mysqli->query("SELECT DISTINCT s_id,r_id FROM chat where fake = 1  and seen = 0 order by id DESC");
	} else {
		$query = $mysqli->query("SELECT DISTINCT s_id,r_id FROM chat where fake = 1  order by id DESC");
	}

	$return['empty'] = false;

	if ($query->num_rows > 0) { 
		while($value = $query->fetch_object()){
			$senderName = getData('users','name','WHERE id ='.$value->s_id);
			$fakeName = getData('users','name','WHERE id ='.$value->r_id);
			$fakeId = $value->r_id;
			$senderId = $value->s_id;
			$fakePhoto = profilePhoto($value->r_id);

			$senderStatus = userStatus($senderId);
			$senderStatusIcon = 'avatar-online';

			if($senderStatus == 'n'){
				$senderStatusIcon = 'avatar-offline';
			}

			$chatId = getData('chat','id','WHERE s_id = '.$value->s_id.' AND r_id = '.$value->r_id.' ORDER BY id DESC LIMIT 1');

			$searchName = strtolower($senderName);

			$conv = $senderId.$fakeId;

			$newMessagesFilter = 'WHERE r_id = '.$fakeId.' AND s_id = '.$senderId.' AND seen = 0';
			$newMessagesCount = selectC('chat',$newMessagesFilter);
			$return['conv'][] = $conv;
			$return[$conv]['chatId'] = $chatId;

			$newMessageLabel = '<span class="badge badge-pill badge-success" data-new-message="'.$chatId.'" style="margin-right:10px;font-size: 10px">NEW</span>';

			if($tab == 'All'){
				if($newMessagesCount == 0){
					$newMessageLabel = '';
				}			
			}
			$return[$conv]['data'] = '<div class="list-group-item d-flex align-items-start border-right-3 border-right-primary bg-light rightSectionChatUser" data-conv="'.$conv.'" data-chatid="'.$chatId.'" 
                          data-uid="'.$fakeId.'" data-search-bind="'.$searchName.'" 
                          data-cid="'.$senderId.'" data-name="'.$fakeName.'" data-id="'.$fakeId.'" onclick="viewChat(this)" data-photo="'.profilePhoto($fakeId).'"
                          onmouseover="hoverFriendList(this,1)" onmouseout="hoverFriendList(this,2)" style="cursor: pointer;">
	                <div class="mr-3 d-flex flex-column align-items-center">
						<div class="avatar avatar-md '.$senderStatusIcon.'" style="margin-bottom:5px" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$fakeName.'">
						    <img src="'.profilePhoto($senderId).'" alt="Avatar" class="avatar-img rounded-circle">
						</div>	 
						<div class="avatar avatar-md avatar-online" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$senderName.'">
						    <img src="'.profilePhoto($fakeId).'" alt="Avatar" class="avatar-img rounded-circle">
						</div>	 						                                           
	                </div>
	                <div class="flex">
	                    <p class="m-0">
	                        <span class="d-flex align-items-center mb-1">
	                            <a href="javascript:;" class="text-dark-gray">From: '.$senderName.'</a>
	                            <small class="ml-auto text-muted" style="font-size: 10px">
	                            '.$newMessageLabel.'
	                            '.getLastMessageMobileTime($fakeId,$senderId).'</small>
	                        </span>
	                        <span class="d-flex align-items-end">
	                            <span class="flex mr-3">
	                                <strong class="text-body mb-1">To: '.$fakeName.'</strong><br>
                                       <small class="text-muted" style="margin-top:15px;max-height: 3rem; overflow: hidden; position: relative; display: inline-block;background: #fff;border-radius: 5px;padding: 5px 10px 5px 10px">
                                          '.getLastMessageMobile($fakeId,$senderId).'
                                       </small>
	                            </span>
	                            <a href="javascript:;" class="d-flex align-items-center mb-1">
	                                <small class="text-muted mr-1">'.$newMessagesCount.'</small>
	                                <i class="material-icons icon-16pt text-muted">chat</i>
	                            </a>
	                        </span>
	                    </p>
	                </div>
	            </div>';				
		}
	} else {
		$return['empty'] = true;	
	}
	return json_encode($return);			
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	switch ($_POST['action']) {
		case 'load':
			$uid = secureEncode($_POST['user']);
			getUserInfo($uid,3);
			$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$sm['chat']['id']."' and r_id = '".$sm['user']['id']."'");
			$arr =array();
			$arr['id']=$sm['chat']['id'];
			$arr['name']=$sm['chat']['name'];
			$arr['chat'] = getChat($sm['user']['id'],$sm['chat']['id']);
			echo json_encode($arr);		
		break;		
		case 'send':
			if (strpos($_POST['message'], '<img') !== false) {
			    $message = $_POST['message'];
			} else {
				$message = secureEncode($_POST['message']);
			}
			$r_id = secureEncode($_POST['r_id']);
			$mobile = secureEncode($_POST['mobile']);
			$time = time();

			if(isset($_POST['fast'])){
				$fast = secureEncode($_POST['fast']);		
				if($fast){
					$date = date('m/d/Y', time());
					$mysqli->query("INSERT INTO users_chat (uid,date,count,last_chat) VALUES ('".$sm['user']['id']."','".$date."',1,'".$time."') ON DUPLICATE KEY UPDATE count=count+1");						
				}
			}
			getUserInfo($r_id,1);
			$fake = $sm['profile']['fake'];
			$online_day = $sm['profile']['online_day'];		
			$data['message'] = $sm['lang'][658]['text'];
			$data['chatHeaderRight']='<div class="js-message-block" id="you">
					<div class="message">
						<div class="brick brick--xsm brick--hover">
							<div class="brick-img profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
						</div>
						<div class="message__txt">
							<span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", time()).'</span>
							<div class="message__name lgrey">'.$sm['user']['first_name'].'</div>
							<p class="montserrat chat-text">'.$message.'</p>
						</div>
					</div>
				</div>	
			';	

			$data['time'] = date("H:i", time());			
			$event = 'chat'.$r_id.$sm['user']['id'];
			$noti= 'notification'.$r_id;
			$data['id'] = $sm['user']['id'];
			$data['type'] = 1;
			$data['icon'] = $sm['user']['profile_photo'];
			$data['name'] = $sm['user']['first_name'];		
			$data['photo'] = 0;
	        $data['action'] = 'message';
	        if(is_numeric($sm['plugins']['pusher']['id'])){ 
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $event, $data);
			}
			$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake,online_day) VALUES ('".$sm['user']['id']."','".$r_id."','".$time."','".$message."','".$fake."','".$online_day."')");
			$arr = array();

			$data['notification_chat'] = false;
			$results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$r_id."' AND seen = 0 AND notification = 0 order by id desc");	

			if($results->num_rows > 0){		
				$data['notification_chat'] = getUserFriends($r_id);	
				$data['unread'] = checkUnreadMessages($r_id);
			}
			if(is_numeric($sm['plugins']['pusher']['id'])){ 
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $noti, $data);	
			}
			if($fake == 0){
				$sm['profile_notifications'] = userNotifications($sm['profile']['id']);
				if($sm['profile']['last_access']+300 <= time() && $sm['profile_notifications']['message'] == 1){
					chatMailNotification($r_id,$message,$sm['user']['id']);
				} 
			}

			$arr['message'] = cleanMessage($message);
			if($mobile == 1){
				$arr['chat'] = getLastChatMobile($sm['user']['id'],$r_id);
			} else {
				$arr['chat'] = getLastChat($sm['user']['id'],$r_id);	
			}

			//activity content 
			if($sm['plugins']['logActivity']['enabled'] == 'Yes'){
				$ac = array();
				$ac['u1']['id'] = $sm['user']['id']; 
				$ac['u2']['id'] = $sm['profile']['id']; 
				$ac['u1']['name'] = $sm['user']['first_name']; 
				$ac['u2']['name'] = $sm['profile']['first_name']; 	
				$ac['u1']['photo'] = $sm['user']['profile_photo']; 
				$ac['u2']['photo'] = $sm['profile']['profile_photo']; 
				$ac['message'] = $arr['message'];

				$adminPush= 'adminActivity';
				$pushData['message'] = $ac;	
				$ac = json_encode($ac);

				$title = $sm['user']['first_name'].' sent a message to '.$sm['profile']['first_name'];
				activity('message',$ac,$title);	 
				if(is_numeric($sm['plugins']['pusher']['id'])){       
			    	$sm['push']->trigger($sm['plugins']['pusher']['key'], $adminPush, $pushData);
				}
		    }			
			
		break;	

		case 'send_fake':
			$message = secureEncode($_POST['mensaje']);
			$r_id = secureEncode($_POST['uid2']);
			$s_id = secureEncode($_POST['uid1']);		
			$mobile = secureEncode($_POST['mobile']);		
			$time = time();
			getUserInfo($r_id,1);
			$fake = $sm['profile']['fake'];
			getUserInfo($s_id,6);
			$data['message'] = cleanMessage($message);
			if($mobile == 1){
				$data['chat'] = getLastChatMobile($sm['search']['id'],$r_id);
			} else {
				$data['chatHeaderRight']='<div class="js-message-block" id="you">
						<div class="message">
							<div class="brick brick--xsm brick--hover">
								<div class="brick-img profile-photo" data-src="'.$sm['search']['profile_photo'].'"></div>
							</div>
							<div class="message__txt">
								<span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", $spotl->time).'</span>
								<div class="message__name lgrey">'.$sm['search']['first_name'].'</div>
								<p class="montserrat chat-text">'.cleanMessage($message).'</p>
							</div>
						</div>
					</div>	
				';	
			}
			$data['time'] = date("H:i", time());			
			$event = 'chat'.$r_id.$sm['search']['id'];
			$noti= 'notification'.$r_id;
			$data['id'] = $sm['search']['id'];
			$data['type'] = 1;
			$data['icon'] = $sm['search']['profile_photo'];		
			$data['name'] = $sm['search']['first_name'];
			$data['photo'] = 0;	
			$mysqli->query("INSERT INTO chat (s_id,r_id,time,message,fake) VALUES ('".$s_id."','".$r_id."','".$time."','".$message."','".$fake."')");
			$arr = array();
			$data['notification_chat'] = false;
			$results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$r_id."' AND seen = 0 AND notification = 0 order by id desc");			
			if($results->num_rows > 0){		
				$data['notification_chat'] = getUserFriends($r_id);	
				$data['unread'] = checkUnreadMessages($r_id);
			}
			if($fake == 0){
				$sm['profile_notifications'] = userNotifications($sm['profile']['id']);
				if($sm['profile']['last_access']+300 >= time() && $sm['profile_notifications']['message'] == 1){
					chatMailNotification($r_id,$message,$s_id);
				} 
			}
			$arr['message'] = cleanMessage($message);
			if($mobile == 1){
				$arr['chat'] = getLastChatMobile($s_id,$r_id);
			} else {
				$arr['chat'] = getLastChat($s_id,$r_id);	
			}
			echo json_encode($arr);			
		break;

		case 'fake_list':
			$tab = secureEncode($_POST['tab']);
			echo getFakeUsersChat($tab);
		break;	

		case 'load_fake':
		$u1 = secureEncode($_POST['u1']);
		$u2 = secureEncode($_POST['u2']);
		//Secure info
		$u1 = secureEncode($u1);	
		$u2 = secureEncode($u2);
		//Get all chats
		$chatt = $mysqli->query("SELECT * FROM chat where s_id = '".$u1."' and r_id = '".$u2."' OR r_id = '".$u1."' and s_id = '".$u2."' order by id asc");
		//Update chat to seen
		$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$u2."' and r_id = '".$u1."'");
		if ($chatt->num_rows > 0) { 
			while($chat= $chatt->fetch_object()){
				//Get profile photo
				$mensaje = $chat->message;	
				//Check if current user is the sender
				if($chat->s_id == $u1){
					if($chat->seen == 1){
						echo'
						<li class="me">
							<div class="image">
								<img src="'.profilePhoto($chat->s_id).'"/>
							</div>
							<p> '.$mensaje.' </p>	
						</li> 
						';	
					} else {
						echo'
						<li class="me">
							<div class="image">
								<img src="'.profilePhoto($chat->s_id).'"/>
							</div>
							<p> '.$mensaje.' </p>	
						</li> 
						';							
					}	
				} else {
					echo'									
					<li class="you">
					<div class="image">
					<img src="'.profilePhoto($chat->s_id).'"/>
					</div>
					<p> '.$mensaje.' </p>	
					</li> 
					';					
				}										
			}		
		}	
		break;

		case 'current_fake':
			//GET POST INFO
			$u1 = secureEncode($_POST['u1']);	
			$u2 = secureEncode($_POST['u2']);
			//GET NEW MESSAGES
			$results = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$u1."' AND s_id = '".$u2."' AND seen = 0  order by id asc");
			//CHECK IF CURRENT USER HAS NEW MESSAGE
			if($results->num_rows > 0){		
				while ($obj = $results->fetch_object()){
					//UPDATE MESSAGE AS SEEN
					$mysqli->query("UPDATE chat SET seen = 1 where r_id = '".$u1."' and s_id = '".$u2."'");
					//SHOW NEW MESSAGES
					echo'
					<li class="you">
						<div class="image">
							<img src="'.profilePhoto($obj->s_id).'"/>
						</div>
						<p> '.$obj->message.' </p>
					</li> 
					';		
				}
			}	
		break;

		case 'access':
			$time = time();		
			$access = secureEncode($_POST['access']);
			$r_id = secureEncode($_POST['r_id']);		
			if($access == 1) {
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message) VALUES 
												  ('".$sm['user']['id']."','".$r_id."','".$time."','".$sm['lang'][189]['text']."')");	
				$mysqli->query("UPDATE chat SET access = 2 WHERE s_id = '".$r_id."' AND r_id = '".$sm['user']['id']."' AND access = 1");
				$mysqli->query("INSERT INTO blocked_photos (u1,u2) VALUES ('".$r_id."','".$sm['user']['id']."')");	
			} else {
				$mysqli->query("INSERT INTO chat (s_id,r_id,time,message) VALUES
												  ('".$sm['user']['id']."','".$r_id."','".$time."','".$sm['lang'][190]['text']."')");	
				$mysqli->query("UPDATE chat SET access = 2 WHERE s_id = '".$r_id."' AND r_id = '".$sm['user']['id']."' AND access = 1");
			}
		break;	

		case 'current':
			$uid = secureEncode($_POST['uid']);
			$mob = secureEncode($_POST['mobile']);
			getUserInfo($uid,3);
			$arr = array();	
			$arr['result'] = 0;	
			$results = $mysqli->query("SELECT s_id,id,photo FROM chat WHERE r_id = '".$sm['user']['id']."' AND s_id = '".$uid."' AND seen = 0  order by id asc");
			if($results->num_rows > 0){
				$re = $results->fetch_object();
				$arr['result'] = 1;
				$arr['photo'] = $re->photo;
				if($mob == 1){
					$arr['chat'] = getLastChatMobile2($sm['user']['id'],$uid);
				} else {
					$arr['chat'] = getLastMessage($sm['user']['id'],$uid);	
				}
				$arr['message'] = cleanMessageById($re->id);			
				$mysqli->query("UPDATE chat set seen = 1 where r_id = '".$sm['user']['id']."' and s_id = '".$uid."'");				
			}
			echo json_encode($arr);			
		break;
		case 'read':
			$id = secureEncode($_POST['id']);	
			$mysqli->query("UPDATE chat set seen = 1, notification = 1 where r_id = '".$sm['user']['id']."' and s_id = '".$id."'");
		break;

		case 'notification':
			$user = secureEncode($_POST['user']);
			$time = time();		
			$arr = array();	
			if($user == 0){
				$results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$sm['user']['id']."' AND seen = 0 AND notification = 0 order by id desc");
			} else {
				$results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$sm['user']['id']."' AND s_id <> '".$user."' AND seen = 0 AND notification = 0 order by id desc");			
			}
			if($results->num_rows > 0){		
				while($use = $results->fetch_object()){
					$arr[] = $use->s_id;	
				}		
				$mysqli->query("UPDATE chat set notification = 1 where r_id = '".$sm['user']['id']."'");				
			}
			echo json_encode($arr);			
		break;	

		case 'new':
			echo getUserFriends($sm['user']['id']);			
		break;	

		case 'today':
			$uid = secureEncode($sm['user']['id']);
			$time = time();
			$date = date('m/d/Y', time());
			$mysqli->query("INSERT INTO users_chat (uid,date,count,last_chat) VALUES ('".$uid."','".$date."',1,'".$time."') ON DUPLICATE KEY UPDATE count=count+1");	
		break;	

		case 'chat_limit':
			$uid = secureEncode($sm['user']['id']);
			$date = date('m/d/Y', time());
			$mysqli->query("DELETE FROM users_chat WHERE uid = '".$uid."' AND date = '".$date."'");	
		break;		
	}
}
//CLOSE DB CONNECTION
$mysqli->close();