<?php

$check_bar = substr($site_url, -1);
if($check_bar != '/'){
    $site_url = $site_url.'/'; 
}

$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}

function checkUnreadMessages($uid){
    global $mysqli;
    $query = $mysqli->query("SELECT count(id) as total FROM chat WHERE r_id = '".$uid."' AND seen = 0");    
    $total = $query->fetch_assoc();
    return $total['total']; 
}

function profilePhoto($uid,$big=0) {
    global $mysqli,$site_url;
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
        $profile_photo = $site_url."themes/default/images/no_user.png";
    }
    return $profile_photo;
}

function pluginsData($p,$val) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings_values where plugin = '".$p."' and setting = '".$val."'");
    $result = $config->fetch_object();
    return $result->setting_val;
}

function getLang($id,$langid) {
    global $mysqli;
    $config = $mysqli->query("SELECT text FROM site_lang where id = '".$id."' and lang_id = '".$langid."'");
    $result = $config->fetch_object();
    return $result->text;
}

function getUserLang($id) {
    global $mysqli;
    $config = $mysqli->query("SELECT lang FROM users where id = '".$id."'");
    $result = $config->fetch_object();
    return $result->lang;
}


function pusherData($val) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings_values where plugin = 'pusher' and setting = '".$val."'");
    $result = $config->fetch_object();
    return $result->setting_val;
}

 
function secureEncode($string) {
    $string = trim($string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = str_replace('\\r\\n', '<br>',$string);
    $string = str_replace('\\r', '<br>',$string);
    $string = str_replace('\\n\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = stripslashes($string);
    $string = str_replace('&amp;#', '&#',$string);
    return $string;
}

function requestPage($page_url='',$type,$sm) {
    global $sm;
    if($type == 'Desktop'){
        $theme = getData('settings','setting_val','WHERE setting = "desktopTheme"');
    }
    $page = '../themes/' . $theme . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}

function getData($table,$col,$filter=''){
    global $mysqli;
    $q = $mysqli->query("SELECT $col FROM $table $filter");
    $r = $q->fetch_object();
    return  $r->$col;   
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

function getUserFriends($uid){  
    global $mysqli;
    $friends = '';
    $arr[] = $uid;
    $today = date('w'); 
    $query2 = $mysqli->query("SELECT DISTINCT s_id,id FROM chat WHERE r_id = '".$uid."' and seen <= 1  ORDER BY id DESC");
    if ($query2->num_rows > 0) { 
        while($result2 = $query2->fetch_object()){
            if (!in_array($result2->s_id, $arr)){
                $arr[] = $result2->s_id;                        
                
                $friendId = $result2->s_id;
                $fake = getData('users','fake','where id ='.$friendId);
                $online_day = getData('users','online_day','where id ='.$friendId);
                $last_access = getData('users','last_access','where id ='.$friendId);               

                $new = getNewMessages($uid,$friendId);

                $friends.='
                <div class="brick sb-friends" id="user'.$friendId.'" onclick="rightChatLink('.$friendId.','.getNewMessages($uid,$friendId).')" data-chat="'.$friendId.'" style="cursor:pointer;"  >
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

                $friends.='
                <div class="brick sb-friends" id="user'.$friendId.'" onclick="rightChatLink('.$friendId.','.getNewMessages($uid,$friendId).')" data-chat="'.$friendId.'" style="cursor:pointer;"  >
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
