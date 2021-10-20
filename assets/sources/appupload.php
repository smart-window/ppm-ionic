<?php
header("Access-Control-Allow-Origin: *");
require_once("../includes/core.php");
require_once("../includes/custom/app_core.php");
require_once 'S3.php';

//AWS
require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
if(aws('enabled') == 'Yes'){
    // AWS Info
    $bucketName = aws('bucket');
    $IAM_KEY = aws('s3');
    $IAM_SECRET = aws('secret');
    // Connect to AWS
    try {
        $s3 = S3Client::factory(
            array(
                'credentials' => array(
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET
                ),
                'version' => 'latest',
                'region'  => 'us-east-2'
            )
        );
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

function aws($val) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = 'amazon' and setting = '".$val."'");
    $result = $config->fetch_object();
    return $result->setting_val;
}
function watermark($val) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = 'watermark' and setting = '".$val."'");
    $result = $config->fetch_object();
    return $result->setting_val;
}

function getPhotoType($data){
    if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png','wav','mpeg','mp4' ])) {
            throw new \Exception('invalid image type');
        }

        $data = base64_decode($data);

        if ($data === false) {
            throw new \Exception('base64_decode failed');
        } else {
            return $type;    
        }
    } else {
        throw new \Exception('did not match data URI with image data');
        return false;
    }    
}

function regImage($base64img,$uid){
    global $sm;
    $arr = array();
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64img));
    $time = time();

    $filepath = 'uploads/'.$uid.$time.'.'.getPhotoType($base64img);
    $thumbpath = 'uploads/thumb_'.$uid.$time.'.'.getPhotoType($base64img);   
    
    $filepath = strtolower($filepath);
    if(strpos($filepath, '.php') !== false || strpos($filepath, '.py') !== false || strpos($filepath, '.htaccess') !== false || strpos($filepath, '.rb') !== false){
        exit;
    }    
    file_put_contents($filepath, $data);

    if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false || strpos($filepath, 'JPG') !== false || strpos($filepath, 'JPEG') !== false || strpos($filepath, 'PNG') !== false) {
        make_thumb($filepath, $thumbpath, 200);
    }

    $purl = $sm['config']['site_url'].'assets/sources/'.$filepath;
    $thumburl = $sm['config']['site_url'].'assets/sources/'.$thumbpath;


    $arr['photo'] = $purl;
    $arr['thumb'] = $thumburl;
    echo json_encode($arr);
}


function uploadImage($base64img,$uid){
    global $mysqli,$sm;
    $arr = array();
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64img));
    $time = time();
    
    $filepath = 'uploads/'.$uid.$time.'.'.getPhotoType($base64img);
    $thumbpath = 'uploads/thumb_'.$uid.$time.'.'.getPhotoType($base64img);   
    
    $filepath = strtolower($filepath);
    if(strpos($filepath, '.php') !== false || strpos($filepath, '.py') !== false || strpos($filepath, '.htaccess') !== false || strpos($filepath, '.rb') !== false){
        exit;
    }    
    file_put_contents($filepath, $data);

    if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false || strpos($filepath, 'JPG') !== false || strpos($filepath, 'JPEG') !== false || strpos($filepath, 'PNG') !== false) {
        make_thumb($filepath, $thumbpath, 200);
    }

 
    $purl = $sm['config']['site_url'].'assets/sources/'.$filepath;
    $thumburl = $sm['config']['site_url'].'assets/sources/'.$thumbpath;

    $photoReview = 1;
    if($sm['plugins']['settings']['photoReview'] == 'Yes' && !isset($_POST['adminPanel'])){
        $photoReview = 0;           
    }

    $mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,approved)
    VALUES ('$uid','$purl', '$thumburl','".$photoReview."')");                                                     
    $arr['user']['photos'] = userAppPhotos($uid);
    echo json_encode($arr);
}

function sendPhoto($base64img,$uid,$rid){
    global $mysqli,$sm;
    $base64img = str_replace('data:image/jpeg;base64,', '', $base64img);
    $data = base64_decode($base64img);
    $time = time();
    $file = 'uploads/'.$uid.$time.'.jpg';
    $photo = $sm['config']['site_url'].'/assets/sources/'.$file;
    file_put_contents($file, $data);
    $mysqli->query("INSERT INTO chat (s_id,r_id,time,message,photo) VALUES ('".$uid."','".$rid."','".$time."','".$photo."' , 1)");  
        $event = 'chat'.$rid.$uid;
        $arr['type'] = 'image';
        $arr['message'] = $photo;
        $arr['id'] = $uid;
        $arr['chatHeaderRight']='<div class="js-message-block" id="you">
                <div class="message">
                    <div class="brick brick--xsm brick--hover">
                        <div class="brick-img profile-photo" data-src="'.$photo.'"></div>
                    </div>
                    <div class="message__txt">
                        <span class="lgrey message__time" style="margin-right: 15px;">'.date("H:i", $time).'</span>
                        <div class="message__name lgrey"></div>
                        <a href="#img'.$time.'">
                            <p class="montserrat chat-text">
                                <div class="message__pic_ js-wrap" style="cursor:pointer;">
                                    <img  src="'.$photo.'" />
                                </div>
                            </p>
                        </a>
                    </div>
                </div>
            </div>  
        ';     
        $sm['push']->trigger($sm['plugins']['pusher']['key'], $event, $arr );    
}

switch ($_POST['action']) {
    case 'register':
        regImage(secureEncode($_POST['base64']),secureEncode($_POST['uid']));
    break;
    case 'videoRecord':
        $arr = array();
        $data = base64_decode(preg_replace('#^data:video/\w+;base64,#i','', secureEncode($_POST['base64'])));
        $time = time();
        $file = 'uploads/'.secureEncode($_POST['uid']).$time.'.webm';
        $video = $sm['config']['site_url'].'assets/sources/'.$file;    
        file_put_contents($file, $data);

        $mysqli->query("UPDATE videocall set r_id_video = '".$video."' where call_id = '".secureEncode($_POST['callId'])."' and r_id = '".secureEncode($_POST['uid'])."'"); 
        $mysqli->query("UPDATE videocall set c_id_video = '".$video."' where call_id = '".secureEncode($_POST['callId'])."' and c_id = '".secureEncode($_POST['uid'])."'");
         
        $arr['videoRecord'] = $video;
        $arr['called'] = secureEncode($_POST['called']);
        $arr['uid'] = secureEncode($_POST['uid']);
        echo json_encode($arr);    
    break;    
    case 'upload':
        uploadImage(secureEncode($_POST['base64']),secureEncode($_POST['uid']));
    break;
    case 'sendChat':
        sendPhoto(secureEncode($_POST['base64']),secureEncode($_POST['uid']),secureEncode($_POST['rid']));
    break;  
}


function make_thumb($src, $dest, $desired_width) {
    $imgType = get_image_type($src);
    if(strpos($imgType, 'png') !== false) {
       $source_image = imagecreatefrompng($src);
    } else {
       $source_image = imagecreatefromjpeg($src); 
    }   
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    $desired_height = floor($height * ($desired_width / $width));
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    imagejpeg($virtual_image, $dest);
}

function get_image_type( $filename ) {
    $img = getimagesize( $filename );
    if ( !empty( $img[2] ) )
        return image_type_to_mime_type( $img[2] );
    return false;
}

function awsThumb($url, $filename, $width = 200, $height = true) {

    $image = ImageCreateFromString(file_get_contents($url));
    $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;
    $output = ImageCreateTrueColor($width, $height);
    ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
    ImageJPEG($output, $filename, 95); 
    return $filename; 
}