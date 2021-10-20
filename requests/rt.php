<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require('../assets/includes/config.php');
include('../assets/includes/realtime.php');
require('../assets/includes/pusher.php');


date_default_timezone_set(pluginsData('settings','timezone'));
$options = array(
'cluster' => pusherData('cluster'),
'encrypted' => false
);
$rt = new Pusher(
    pusherData('key'),
    pusherData('secret'),
    pusherData('id'),
    $options
); 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($_GET['action']) {
        case 'message':
            $query = secureEncode($_GET['query']);
            $data = explode('[rt]',$query);
            $s_id = $data[0];
            $r_id = $data[1];
            $photo = $data[2];
            $name = $data[3];
            $m = $data[4];
            $type = $data[5];
            $storyType = '';
            $storyUrl = '';

            $lang = getUserLang($r_id);
            if($type == 'story'){
                if(isset($data[6])){
                    $storyUrl = $data[6];
                    $storyType = $data[7];
                }
            }
            $time = time();
            $notiMessage = getLang(686,$lang);

            if($type == 'credits'){
                $m = '<b>'.getLang(583,$lang).' '.$data[6].' '.getLang(128,$lang).'!</b>';
                $notiMessage = $m;                
            }

            if($type == 'videocall'){
                $notiMessage = $m;                
            }            

            $content = $m;
            if($type == 'image'){
                $content = '<div class="message__pic_" style="cursor:pointer;"><img  src="'.$m.'" /></div>';
                $notiMessage = getLang(687,$lang);
            }
            if($type == 'gif'){
                $content = '
                <div class="message__pic_" style="cursor:pointer;border:none">
                    <img  src="'.$m.'" />
                </div>';
                $notiMessage = getLang(688,$lang);
            }  
            if($type == 'gift'){
                $content = '
                <div class="message__pic_" style="cursor:pointer;border:none">
                    <img  src="'.$m.'" />
                </div>';
                $notiMessage = getLang(689,$lang);
            }                        
            if($type == 'story'){
                if($storyType == 'video'){
                    $content = '<div class="message__pic_" style="cursor:pointer;">
                        <video src="'.$storyUrl.'" type="video/mp4" muted preload style="position:absolute;top:0;left:0;width:100%;height:100%"></video>
                    </div>
                    <span style="opacity:.6;font-size:11px;margin-bottom:10px">
                        '.getLang(663,$lang).'</span><br>
                    '.$m;
                } else {
                    $content = '<div class="message__pic_" style="cursor:pointer;">
                        <img  src="'.$storyUrl.'" />
                    </div>
                    <span style="opacity:.6;font-size:11px;margin-bottom:10px">
                        '.getLang(663,$lang).'</span><br>
                    '.$m;
                }
                
            }            

            $event = 'chat'.$r_id.$s_id;
            $arr['type'] = $type;
            $arr['notification_chat'] = false;
            $arr['message'] = $m;
            $arr['id'] = $s_id;
            $arr['action'] = 'message';
            $arr['chatHeaderRight']='<div class="js-message-block" id="you">
                    <div class="message">
                        <div class="brick brick--xsm brick--hover">
                            <div class="brick-img profile-photo" data-src="'.$photo.'"></div>
                        </div>
                        <div class="message__txt">
                            <span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", time()).'</span>
                            <div class="message__name lgrey">'.$name.'</div>
                            <p class="montserrat chat-text">
                                '.$content.'
                            </p>
                        </div>
                    </div>
                </div>  
            '; 
            if(is_numeric(pusherData('id'))){
                $rt->trigger(pusherData('key'), $event, $arr );
            }

            $notiData['notification_chat'] = false;
            $results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$r_id."' AND seen = 0 AND notification = 0 order by id desc");  

            if($results->num_rows > 0){     
                $notiData['notification_chat'] = getUserFriends($r_id); 
                $notiData['unread'] = checkUnreadMessages($r_id);                
            }

            $noti= 'notification'.$r_id;
            $notiData['id'] = $s_id;
            $notiData['message'] = $notiMessage;
            $notiData['time'] = date("H:i", time());
            $notiData['type'] = $type;
            $notiData['icon'] = $photo;
            $notiData['name'] = $name;      
            $notiData['photo'] = 0;
            $notiData['action'] = 'message';
            $notiData['unread'] = checkUnreadMessages($r_id);   
            if(is_numeric(pusherData('id'))){    
                $rt->trigger(pusherData('key'), $noti, $notiData);  
            }
            
        break;

        case 'typing':
            $query = secureEncode($_GET['query']);
            $data = explode(',',$query);
            $s_id = $data[0];
            $r_id = $data[1];
            $t = $data[2];
            $time = time();
            $event = 'typing'.$r_id.$s_id;
            $arr['t'] = $t;
            if(is_numeric(pusherData('id'))){
                $rt->trigger(pusherData('key'), $event, $arr );
            }
        break;

        case 'endVideocall':
            $query = secureEncode($_GET['query']);
            $data = explode(',',$query);
            $r_id = $data[0];
            $event = 'videocall'.$r_id;
            $rt->trigger(pusherData('key'), $event, $arr );
        break;

        case 'themeUpdate':
            $sm = array();
            $query = secureEncode($_GET['query']);
            $data = explode(',',$query);
            $preset = $data[0];
            $theme = $data[1];
            $themeType = $data[2];
            $reload = $data[3];

            $event = $preset;
            
            $arr['preset'] = $preset;
            $arr['theme'] = $theme;
            $arr['reload'] = $reload;
            if(is_numeric(pusherData('id'))){
                $rt->trigger(pusherData('key').'iframe', $event, $arr );
                $rt->trigger(pusherData('key'), $event, $arr );
            }

        break;                
            
    }
}