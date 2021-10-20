<?php 
/* Belloo By Xohan - xohansosa@gmail.com - https://www.premiumdatingscript.com/*/
require_once('../assets/includes/core.php');
if(!isset($_GET['page'])){
    $_GET['page'] = 'index';
}
if($sm['plugins']['autoRegister']['enabled'] == 'Yes' && $logged == false){
    $ip = getUserIpAddr();
    $checkIp = getData('users','id','WHERE ip = "'.$ip.'"');
    if($checkIp == 'noData'){
        $_SESSION['user'] = $checkIp;
        getUserInfo($checkIp,0);
        checkUserPremium($checkIp);
        $sm['user_notifications'] = userNotifications($checkIp);
        
        $sm['lang'] = siteLang($sm['user']['lang']);
        $sm['alang'] = appLang($sm['user']['lang']);
        $sm['elang'] = emailLang($sm['user']['lang']);
        $sm['seoLang'] = seoLang($sm['user']['lang']);
        $sm['landingLang'] = landingLang($sm['user']['lang'],$landingTheme,$_SESSION['landingPreset']);
        $sm['genders'] = siteGenders($sm['user']['lang']);      

        $modPermission = array();
        if($sm['user']['admin'] >= 1){
            $moderationList = getArray('moderation_list','','moderation ASC');
            foreach ($moderationList as $mod) {  
                if($sm['user']['admin'] == 1){
                    $modPermission[$mod['moderation']] = 'Yes';
                } else {
                    $modVal = getData('moderators_permission','setting_val','WHERE setting = "'.$mod['moderation'].'" AND id = "'.$sm['user']['moderator'].'"');
                    $modPermission[$mod['moderation']] = $modVal;
                }
            }      
        }
        $sm['moderator'] = $modPermission;

        $time = time();
        $logged = true; 
        $ip = getUserIpAddr();
        if($sm['user']['ip'] != $ip){
            $mysqli->query("UPDATE users set ip = '".$ip."' where id = '".$checkIp."'");
        }
        if($sm['user']['last_access'] < $time || $sm['user']['last_access'] == 0){  
            $mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$checkIp."'"); 
        }
    } else {
        
        $rand = rand(0,1012451);
        $rand2 = rand(0,1012451);
        $rand3 = rand(0,1012451);
        $salt = base64_encode($rand);
        $pswd = crypt($sm['plugins']['autoRegister']['guestDefaultPswd'],$salt);
        $lang = getData('languages','id','WHERE id = '.$_SESSION['lang']);
        if($lang == 'noData'){
            $lang = $sm['plugins']['settings']['defaultLang'];
        }  

        $name = $sm['plugins']['autoRegister']['guestDefaultName'].' '.$rand;
        $username = $sm['plugins']['autoRegister']['guestDefaultName'].$rand.$rand2;

        $siteEmail = explode('@',$sm['plugins']['settings']['siteEmail']);
        $email = $username.'@'.$siteEmail[1];
        $age = 29;
        $birthday = date('F', mktime(0, 0, 0, 06, 10)).' 15, 1990';

        if($_GET['page'] == $sm['plugins']['autoRegister']['guestCustomOneUrl']){
            $gender = $sm['plugins']['autoRegister']['guestCustomOneGender'];
            $looking = $sm['plugins']['autoRegister']['guestCustomOneLooking'];
        } else if($_GET['page'] == $sm['plugins']['autoRegister']['guestCustomTwoUrl']){
            $gender = $sm['plugins']['autoRegister']['guestCustomTwoGender'];
            $looking = $sm['plugins']['autoRegister']['guestCustomTwoLooking'];         
        } else {
            $gender = $sm['plugins']['autoRegister']['guestDefaultGender'];
            $looking = $sm['plugins']['autoRegister']['guestDefaultLooking'];
        }

        $ip = getUserIpAddr();

        if(!empty($sm['plugins']['ipstack']['key'])){
            $location = json_decode(file_get_contents('http://api.ipstack.com/'.$ip.'?access_key='.$sm['plugins']['ipstack']['key']));
            $city = $location->city;    
            $country = $location->country_name;     
            $lat = $location->latitude;     
            $lng = $location->longitude;
        } else {
            $city = 'Los Angeles';
            $country = 'United States';
            $lat = '-56.1250444';
            $lng = '-34.8872424';
        }

        $date = date('m/d/Y', time());
        
        $dID = 0;
        $bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$city." ".$country;

        $query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,online_day,password,ip,last_access,username,join_date_time,app_id) VALUES ('".$name."', '".$email."','".$pswd."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$lang."','".$date."','".$bio."','".$looking."','18,29,1',0,0,'".$sm['plugins']['autoRegister']['guestDefaultPswd']."','".$ip."','".time()."','".$username."','".time()."','".$dID."')";    
        if ($mysqli->query($query) === TRUE) {
            $last_id = $mysqli->insert_id;
            $mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");   

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

            $mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
            $mysqli->query("INSERT INTO users_extended (uid,field1) VALUES ('".$last_id."','".$sm['lang'][224]['text']."')");


            $_SESSION['user'] = $last_id;
            getUserInfo($last_id);
            checkUserPremium($_SESSION['user']);
            $sm['user_notifications'] = userNotifications($_SESSION['user']);
            
            $sm['lang'] = siteLang($sm['user']['lang']);
            $sm['alang'] = appLang($sm['user']['lang']);
            $sm['elang'] = emailLang($sm['user']['lang']);
            $sm['seoLang'] = seoLang($sm['user']['lang']);
            $sm['landingLang'] = landingLang($sm['user']['lang'],$landingTheme,$_SESSION['landingPreset']);
            $sm['genders'] = siteGenders($sm['user']['lang']);      

            $modPermission = array();
            if($sm['user']['admin'] >= 1){
                $moderationList = getArray('moderation_list','','moderation ASC');
                foreach ($moderationList as $mod) {  
                    if($sm['user']['admin'] == 1){
                        $modPermission[$mod['moderation']] = 'Yes';
                    } else {
                        $modVal = getData('moderators_permission','setting_val','WHERE setting = "'.$mod['moderation'].'" AND id = "'.$sm['user']['moderator'].'"');
                        $modPermission[$mod['moderation']] = $modVal;
                    }
                }      
            }
            $sm['moderator'] = $modPermission;

            $time = time();
            $logged = true;     
            
            if($sm['user']['ip'] != $ip){
                $mysqli->query("UPDATE users set ip = '".$ip."' where id = '".$_SESSION['user']."'");
            }
            if($sm['user']['last_access'] < $time || $sm['user']['last_access'] == 0){  
                $mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$_SESSION['user']."'");    
            }
        }           
    }   
}
?>
<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, target-densityDpi=device-dpi">
	<title></title>      
    <link href="css/magic.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/belloo20.css" rel="stylesheet">       
    <link href="css/belloo.css" rel="stylesheet">
    <link href="css/vivify.min.css" rel="stylesheet"> 
    <link href="css/i.css" rel="stylesheet"> 
   
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <script>
	var site_url = '<?= $sm['config']['site_url']; ?>';
    var siteUrl = '<?= $sm['config']['site_url']; ?>';
	</script>
    <script src="lib/ionic/js/ionic.bundle.js"></script>
    <script src="lib/collide/collide.js"></script>
    <script src="lib/ionic-ion-tinder-cards/ionic.tdcards.js"></script>
    <script src="lib/angular-elastic/elastic.js"></script>
    <script src="js/giphy.js"></script>
    <script src="lib/ngCordova/dist/ng-cordova.js"></script>
	<script src="lib/gsap/src/uncompressed/TweenMax.js"></script>
	<script src="lib/ngFx/dist/ngFx.js"></script>
    <script src="lib/hammer/hammer.js"></script>
    <link rel="icon" type="image/png" href="<?= $sm['theme']['favicon']['val']; ?>" sizes="32x32">
    <script src="lib/angular-animate/angular-animate.js"/></script>
    <script src="https://angular-ui.github.io/ui-router/release/angular-ui-router.min.js"></script>    
    <link rel="stylesheet" href="lib/awlert/dist/css/awlert.css">
    <script src="lib/awlert/dist/js/awlert.min.js"></script>
	<script src="lib/ng-cordova-oauth/dist/ng-cordova-oauth.min.js"></script>
    <script src="https://js.pusher.com/4.0/pusher.min.js"></script>
    <link href="css/autocomplete.css" rel="stylesheet" type="text/css" />       
    <link rel="stylesheet" href="<?php echo $sm['config']['theme_url']; ?>/css/vendor/slim-lightbox.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $sm['config']['theme_url']; ?>/css/vendor/little-widgets.css"/>  

    <link rel="stylesheet" href="../themes/default/css/crossplatform.css"/>

    <script>
        var nextStory = 0;
        var storyPage = '';
        var mobileSite = true;  
        var mobileTheme = '<?= $sm['settings']['mobileTheme'];?>'; 
        var siteLang = <?= $_SESSION['lang']; ?>;
        var userId=0;
        <?php 
        if(!isset($sm['user']['id'])){
            echo '
                var ag1 = '.$sm['plugins']['settings']['minRegisterAge'].';
                var ag2 = 30;
            ';
        } else {
            $e_age = explode( ',', $sm['user']['s_age'] );
            $age1 = $e_age[0];
            $age2 = $e_age[1];
            echo '
                var ag1 = '.$age1.';
                var ag2 = '.$age2.';
            ';
        }
        ?>
    </script>

	<script>
    var oneSignalID=-1,notificationOpenedCallback=function(e){},reg=0,c_quantity=0,lid=43,loader=0,p_quantity=0,ticky=!1,app,interval,c_price,reg_photo="",reg_username="",reg_lat="",reg_lng="",reg_city="",reg_country="",reg_thumb="",p_price,url,mobile=!1,chatLimit=!1,config="",peer,in_videocall=!1,site_prices,account_basic,account_premium,lang,tlang,alang,online=[],unread=[],usPhotos="",cu,regName,localStream,tlang,current_user,user="",show_chat_premium=1,game_array,user_info,current_user_id=0,user_name,meet_limit=0,meet_pages=0,spotlight=[],da=site_url,chats=[],f=da,matche=[],visitors=[],mylikes=[],superlikes=[],myfans=[],cards=[],gresult=[],chatUser,s_age,bottom=!1,s_radius,s_gender,onlineMeet=0,chatInterval,y=f,user_country,u=y,a=u,user_city,site_lang,site_config,totalDiscoverStories=0,storiesGlobal=[],goBackGlobal='';
    	<?php 
        $user = array();

        if (isset($_SESSION['user']) && is_numeric($_SESSION['user']) && $_SESSION['user'] > 0) {
            if(isset($_GET['logout'])){
                unset($_SESSION['user']);
                setcookie("user", 0, time() - 3600); 
                echo 'oneSignalID = 0;';             
            } else {
                $user = $sm['user'];      
                echo 'oneSignalID = '.$_SESSION['user'].';';
            }
        }
        $themeFilter = 'WHERE theme ="'.$sm['settings']['desktopTheme'].'" AND preset = "'.$sm['settings']['desktopThemePreset'].'"';
        $sm['theme'] = json_decode(getData('theme_preset','theme_settings',$themeFilter),true);        
        $site_plugins = json_encode($sm['plugins']); 
        $site_theme = json_encode($sm['theme']);
        $allG = count($sm['genders']);
        $account_basic = json_encode($sm['basic']);
        $user = json_encode($user);  
        $allG = $allG + 1;
        $pk = $sm['plugins']['pusher']['key'];
        echo '
            var allG = '.$allG.';
            user = '.$user.';  
            var plugins = '. $site_plugins  . ';
            var site_theme = '. $site_theme  . ';  
            var uploadStory = false;
            var user_name;  
            var current_user_id = 0;
            var account_basic = '. $account_basic  . ';
            var ph = 0;
            var upphotos = [];
            var extFilter = ["jpg", "jpeg", "png", "mp4", "ogg", "webm"];
            var storyAlbumFilter = ["video/3gpp", "video/mpeg", "video/mp4","video/webm","video/ogg"];            
        '; 
        echo '
        function request_source(){
            return \'' . $sm['config']['ajax_path'] . '\';
        }';
        ?>

        var gUrl = request_source()+'/rt.php';
        var aUrl = request_source()+'/api.php';  
        function inIframe () {
        try {
          return window.self !== window.top;
        } catch (e) {
          return true;
        }
        }

        var rt = '';
        var channel = '';
        if(inIframe() !== true){
            rt = new Pusher("<?= $pk; ?>", {
              encrypted: true,
              cluster: "<?=$sm['plugins']['pusher']['cluster']; ?>"
            });    
            channel = rt.subscribe("<?= $pk; ?>");
        } else {
            rt = new Pusher("<?= $pk; ?>", {
              encrypted: true,
              cluster: "<?=$sm['plugins']['pusher']['cluster']; ?>"
            });    
            channel = rt.subscribe("<?= $pk; ?>iframe");    
            console.log('iframe loaded no real time');
        }
    </script>
    <?php 
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipstack = array('127.0.0.1', "::1");
        if(in_array($_SERVER['REMOTE_ADDR'], $ipstack)){
            $ip = '192.196.0.1';
        }
    ?>
    <script>
        var userIp = '<?= $ip; ?>';
        var upType = 0;
        var updateVisitorLanguage = false;
        var updateLocationRegister;
    </script>

    <script src="lib/adsense/ng-adsense.js"></script>    
    <script src="js/app.js"></script>
    <script src="js/controllers.js"></script>
    <script src="js/services.js"></script>
    <script src="js/directives.js"></script>
    <?php if($sm['plugins']['videocall']['enabled'] == 'Yes'){ ?>
    <script src="<?php echo $sm['config']['theme_url']; ?>/js/vendor/peer.js"></script> 
    <?php } ?>
    <script src="js/resource.min.js"></script>
    <script src="lib/jquery/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ng-flow/2.5.1/ng-flow-standalone.min.js"></script>
    <script src="https://connect.facebook.net/en_US/sdk.js"></script>    
    <link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet" type="text/css"/>

    <?php if($sm['settings']['mobileTheme'] == 'twigo'){ 
        $mainFont = 'Roboto'; ?>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">        
        <link href="twigo/css/twigo.css" rel="stylesheet"> 
    <?php } else {
        $mainFont = 'Rubik';
    } ?>
    <style>
        .comforta{
            font-family: '<?=$mainFont;?>' !important;
        }
        .b-none{
            font-family: '<?=$mainFont;?>' !important;   
        }
        .csms-profile-info__name-inner{
            font-family: '<?=$mainFont;?>' !important;
        }
        .csms-p-1{
            font-family: '<?=$mainFont;?>' !important;
        }
        .chat-name{
            font-family: '<?=$mainFont;?>' !important;
        }        
        .text-muted{
            font-family: '<?=$mainFont;?>' !important;
        }  
        .item span{
            font-family: '<?=$mainFont;?>' !important;   
        }       
        .positive-bg{
            background: #000 !important;
        }
        .button.button--primary{
            background: #000!important;
            color: #fff!important;
        }
        .profile-info__title{
            font-family: '<?=$mainFont;?>' !important; 
        }
        .profile-info__content{
            font-family: '<?=$mainFont;?>' !important; 
        }
        .button.button--secundary.active{
            background: #00F9D1!important;
            color: #000!important;
        } 
        .black-text{
            color: #000!important;
        }         
        .selector__label{
            font-size: 16px;
        }
        .storyOn{
            border:3px solid <?= $sm['theme']['story_on']['val'];?>;
        }              
    </style>



    <?php if(!empty($sm['plugins']['analytics']['ga_id'])){ ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $sm['plugins']['analytics']['ga_id']; ?>"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', '<?= $sm['plugins']['analytics']['ga_id']; ?>');
        </script>    
    <?php } ?>

    <?= $sm['plugins']['customHtml']['mobile_header']; ?>
  </head>
  <body ng-app="starter" ng-controller="AppCtrl" style="background: #fff;">
    <ion-nav-bar></ion-nav-bar>
    <ion-pane ui-view="">   
    </ion-pane>

    <div class="inapp-notification-wrapper chatNotification" style="z-index: 99999" ng-click="goTo('home.matches');hideNotification()">
        <div class="inapp-notification js-inapp-notification-touchable">
            <div class="inapp-notification__promo">
                <div class="inapp-notification__images">
                    <div class="inapp-notification__user chatNotificationPhoto" style="border-radius: 50%"></div>
                </div>
            </div>
            <div class="inapp-notification__content chatNotificationContent comforta"></div> 
        </div>
    </div>
    
    <div id="storytime" data-story="0"></div>
    <div class="preload-photos" style="display:none"></div>     
    <div id="upload-area" style="display: none">
        <input type='file' id="uploadContent" style="display: none" />    
    </div>
    <div id="upload-story" style="display: none" >
        <input type='file' id="uploadStoryContent">
    </div>    
  
    <script src="<?php echo $sm['config']['theme_url']; ?>/js/vendor/jquery.dm-uploader.min.js"></script>
    <?php foreach($sm['plugin'] as $plugin){ ?>
        <?php if(!empty($plugin['js_file'])){
    echo '<script src="'.$sm['config']['site_url'].'themes/plugins/'.$plugin['js_file'].'.js"></script>';
                } 
        } ?>
    <script>
    function locationUpdated(value){ 
        var lat = value.latitude;
        var lng = value.longitude;
        var city = value.name;
        var country = value.country;
        var message = userId+','+lat+','+lng+','+city+','+country;
        if(updateLocationRegister == 'Yes'){
            reg_city = city;
            reg_lng = lng;
            reg_country = country;
            reg_lat = lat;
            return false;
        }
        $.ajax({
            url: request_source()+'/api.php', 
            data: {
                action:"updateLocation",
                query: message                   
            },  
            type: "get",  
            dataType: "JSON",         
            success: function(response) {
                window.location.reload();                        
            },
        });             
    }
    </script>
    <script src="lib/hammer/jquery.hammer.js"></script>
    <script src="<?php echo $sm['config']['theme_url']; ?>/js/vendor/action-sheet.js"></script>    
    <script src="lib/autocomplete/autocomplete.js"></script>    
    <script src="<?php echo $sm['config']['theme_url']; ?>/js/vendor/slim-lightbox.min.js"></script>
    <script src="<?php echo $sm['config']['theme_url']; ?>/js/vendor/little-widgets.js"></script> 
    <script src="<?php echo $sm['config']['theme_url']; ?>/js/vendor/adapter.min.js"></script>   
    <script type="text/javascript">
        var currentStory;
        var socialStory = new Story({
            playlist: currentStory
        });          
    </script>
    <?= $sm['plugins']['customHtml']['mobile_footer']; ?>
  </body>
</html>