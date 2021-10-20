<?php
require_once('../assets/includes/core.php');
$et = siteConfig('fEngageTime');
$el = siteConfig('fEngageLimit');
$time = time();	
$extra = 3600 * $et;
$lastAccess = $time - $extra;
echo 'Engage - started!<br>';
if(siteConfig('fEngage') == 'Yes'){
$query = $mysqli->query("SELECT id FROM users where fake = 0 and last_access <= '".$lastAccess."' order by RAND() limit $el");
	if ($query->num_rows > 0) { 
		while($u = $query->fetch_object()){
			getUserInfo($u->id,6);
			$chance = rand(1, 100);
			echo '<br>User: '.$sm['search']['name'].'<br>';
			//LIKE
			echo 'Engage method: Like!<br>';
			engageUser($sm['search']['lat'],$sm['search']['lng'],$sm['search']['s_gender'],$sm['search']['s_age']);
			echo 'Engage: '.$sm['profile']['name'].'<br><br>';
			$mysqli->query("INSERT INTO users_likes (u1,u2,love) VALUES ('".$sm['profile']['id']."','".$sm['search']['id']."',1)");
			$mysqli->query("UPDATE users set popular = popular+1 where id = '".$sm['search']['id']."'");	
			if(isFan($sm['search']['id'],$sm['profile']['id']) == 0){
				fanMailNotification($sm['search']['id']);
			}
			if(isFan($sm['search']['id'],$sm['profile']['id']) == 1){
				matchMailNotification($sm['search']['id']);							   
			}
		}
	}
	echo 'Engage - ended!';
}