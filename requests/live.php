<?php
header('Content-Type: application/json');
require_once('../assets/includes/core.php');
if(!empty($sm['user']['id'])){
	$uid = $sm['user']['id'];	
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	switch ($_GET['action']) {
		case 'live':

			$mysqli->query("UPDATE live set is_streaming='No' where uid = '".$uid."'");

			$customTxt = secureEncode($_GET['message']);
			$mysqli->query("INSERT INTO live (uid,viewers,start_time,custom_text,is_streaming,lat,lng,gender)
				VALUES('".$uid."', 1, '".time()."', '".$customTxt."', 'Yes', '".$sm['user']['lat']."', '".$sm['user']['lng']."','".$sm['user']['gender']."')");
		break;		
		case 'close':
			$live = secureEncode($_POST['id']);
			$mysqli->query("UPDATE live set viewers=viewers-1 where uid = '".$live."'");
		break;
		case 'endStream':
			$time = time();
			if(isset($_GET['sb'])){ //admin
				$uid = secureEncode($_GET['stream']);
				$sb = secureEncode($_GET['sb']);
				if($sb == 1){//ban
					$mysqli->query("INSERT INTO live_streamer_banned (uid) VALUES('".$uid."')");		
				}
				if($sb == 3){//ban
					$mysqli->query("INSERT INTO live_streamer_banned (uid) VALUES('".$uid."')");		
				}				
				if($sb == 2){//unban
					$mysqli->query("DELETE FROM live_streamer_banned WHERE uid = '".$uid."'");		
				}
				if($sb == 0 || $sb == 3){
					$mysqli->query("UPDATE live set end_time='".$time."' where uid = '".$uid."' order by id desc limit 1");
					$mysqli->query("UPDATE live set is_streaming='No' where uid = '".$uid."'");
					$notification= 'live'.$uid;
			        $info['liveId'] = $uid;
			        $info['type'] = 'end';

			        if(is_numeric($sm['plugins']['pusher']['id'])){
						$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
					}
				}				
			} else {
				$mysqli->query("UPDATE live set end_time='".$time."' where uid = '".$uid."' order by id desc limit 1");
				$mysqli->query("UPDATE live set is_streaming='No' where uid = '".$uid."'");
			}						
		break;	

		case 'checkBanned':
			$viewerId = secureEncode($_GET['userId']);
			$streamId = secureEncode($_GET['streamId']);
			$check = getData('live_banned','streamer_id','WHERE streamer_id ='.$streamId.' AND banned_id = '.$viewerId);

			$arr = array();
			if($check == 'noData'){
				$banned = 'No';
			} else {
				$banned = 'Yes';
			}
			echo $banned;
		break;				

		case 'endStreamFromViewer':
			$time = time();
			$live = secureEncode($_GET['live']);
			$mysqli->query("UPDATE live set end_time='".$time."' where uid = '".$live."' order by id desc limit 1");
			$mysqli->query("UPDATE live set is_streaming='No' where uid = '".$live."'");			
		break;

		case 'watching':	
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$liveId = $data[3];

			$notification= 'live'.$liveId;
	        $info['liveId'] = $liveId;
	        $info['type'] = 'watching';
	        $info['name'] = secureEncode($data[1]);
	        $info['photo'] = secureEncode($data[2]);
	        $info['userId'] = secureEncode($data[0]);	        
	        $info['time'] = date("H:i", time());

	        if(is_numeric($sm['plugins']['pusher']['id'])){
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
			}
			$mysqli->query("UPDATE live set viewers=viewers+1 where uid = '".$liveId."'");
		break;

		case 'status':	
			$status = secureEncode($_GET['status']);
			$liveId = $sm['user']['id'];

			$notification= 'live'.$liveId;
	        $info['liveId'] = $liveId;
	        $info['type'] = 'status';
	        $info['photo'] = profilePhoto($liveId);
	        $info['price'] = secureEncode($_GET['price']);;
	        $info['status'] = $status;	        
	        $info['time'] = date("H:i", time());

	        if(is_numeric($sm['plugins']['pusher']['id'])){
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
			}

			if($status == 'private'){
			  $mysqli->query("UPDATE live set in_private='Yes',private_price = '".$info['price']."' where uid = '".$liveId."'");
			} else {
			  $mysqli->query("UPDATE live set in_private='No',private_price = 0 where uid = '".$liveId."'");
			}
			
		break;		

		case 'leave':	
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$liveId = $data[3];

			$notification= 'live'.$liveId;
	        $info['liveId'] = $liveId;
	        $info['type'] = 'leave';
	        $info['name'] = secureEncode($data[1]);
	        $info['photo'] = secureEncode($data[2]);
	        $info['userId'] = secureEncode($data[0]);	        
	        $info['time'] = date("H:i", time());

	        if(is_numeric($sm['plugins']['pusher']['id'])){
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
			}

			$mysqli->query("UPDATE live set viewers=viewers-1 where uid = '".$liveId."'");
		break;

		case 'sendLiveGift':	
			$query = secureEncode($_GET['query']);
			$data = explode(',',$query);
			$liveId = $data[3];

			$notification= 'live'.$liveId;
	        $info['liveId'] = $liveId;
	        $info['type'] = 'gift';
	        $info['name'] = secureEncode($data[1]);
	        $info['photo'] = secureEncode($data[2]);
	        $info['userId'] = secureEncode($data[0]);
	        $info['gift'] = secureEncode($data[4]);
	        $info['credits'] = secureEncode($data[5]);	        
	        $info['time'] = date("H:i", time());

	        if(is_numeric($sm['plugins']['pusher']['id'])){
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
			}

			if($sm['plugins']['live']['transferCredits'] == 'Yes'){
				$mysqli->query("UPDATE live set credits=credits+'".$data[5]."' where uid = '".$liveId."'");	
			}
			
		break;						

		case 'sendLiveMessage':	
			$query = secureEncode($_GET['query']);
			$data = explode(';-B-;',$query);
			$liveId = $data[0];

			$notification= 'live'.$liveId;
	        $info['liveId'] = $liveId;
	        $info['type'] = 'message';
	        $info['message'] = secureEncode($data[1]);
	        $info['name'] = secureEncode($data[2]);
	        $info['photo'] = secureEncode($data[3]);
	        $info['userId'] = secureEncode($data[4]);	        
	        $info['time'] = date("H:i", time());
	        if(is_numeric($sm['plugins']['pusher']['id'])){
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
			}
		break;

		case 'blockUserLive':	
			$bannedId = secureEncode($_GET['userId']);
			$streamId = $sm['user']['id'];
			$notification= 'live'.$streamId;

	        $info['type'] = 'banned';
	        $info['bannedId'] = $bannedId;	        
	        $info['time'] = date("H:i", time());
	       	$info['name'] = getData('users','name','WHERE id ='.$bannedId);
	       	$info['photo'] = profilePhoto($bannedId);

	       	if(is_numeric($sm['plugins']['pusher']['id'])){
				$sm['push']->trigger($sm['plugins']['pusher']['key'], $notification, $info);
			}
			$mysqli->query("INSERT INTO live_banned (streamer_id,banned_id) VALUES('".$streamId."','".$bannedId."')");			
		break;		

		case 'log':
			$min = secureEncode($_POST['min']);
			$sec = secureEncode($_POST['sec']);	
			$totalSeconds = secureEncode($_POST['totalSeconds']);		
			$callId = secureEncode($_POST['callId']);			
			$time = $min.":".$sec;
			$date = time();
			$mysqli->query("UPDATE videocall set duration='".$time."',total_seconds='".$totalSeconds."' where call_id = '".$callId."'");
		break;	

	}
}
$mysqli->close();