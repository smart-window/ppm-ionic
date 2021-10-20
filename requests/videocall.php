<?php
header('Content-Type: application/json');
require_once('../assets/includes/core.php');
if(!empty($sm['user']['id'])){
	$uid = $sm['user']['id'];	
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	switch ($_POST['action']) {
		case 'update':
			$peer = secureEncode($_POST['peer']);
			$mysqli->query("INSERT INTO users_videocall(u_id,peer_id,status) VALUES ('".$uid."','".$peer."',1)
				ON DUPLICATE KEY UPDATE peer_id = '".$peer."',status = 1");
		break;
		case 'check':
			$id = secureEncode($_POST['id']);
			echo isFan($id,$sm['user']['id']);
		break;
		case 'income':
			$peer = secureEncode($_POST['peer']);
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
		case 'callStatus':
			$callId = secureEncode($_POST['callId']);
			$mysqli->query("UPDATE videocall set status=1 where call_id = '".$callId."'");
		break;

		case 'saveCall':	
			$c_id = secureEncode($_POST['c_id']);
			$r_id = secureEncode($_POST['r_id']);
			$callId = secureEncode($_POST['callId']);			
			$date = time();
			$mysqli->query("INSERT INTO videocall (c_id,r_id,call_id,call_date) VALUES ('".$c_id."','".$r_id."','".$callId."','".$date."')");
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
		case 'getpeerid':
			$peer = secureEncode($_POST['id']);
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
	}
}
$mysqli->close();