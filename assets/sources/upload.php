<?php
session_start();
header('Content-type:application/json;charset=utf-8');
require('../includes/config.php');
$check_bar = substr($site_url, -1);
if($check_bar != '/'){
    $site_url = $site_url.'/';
}
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
$mysqli->set_charset('utf8mb4');
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
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
function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if(version_compare(phpversion(), '5.4.0', '>=')) { 
        return json_decode($json, $assoc, $depth, $options);
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) { 
        return json_decode($json, $assoc, $depth);
    }
    else {
        return json_decode($json, $assoc);
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
function secureEncode($string) {
    $str = preg_replace('/[^A-Za-z0-9\. -]/', '', $string);
    return $str;
}
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
                'region'  => aws('region')
            )
        );
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
if(isset($_GET['fromUrl'])){
    if(!isset($_SESSION['user'])){
        die('no admin');
        exit;
    } else {
        $checkAdmin = getData('users','admin','WHERE id = '.secureEncode($_SESSION['user']));
        if($checkAdmin == 0){
            die('no admin');
            exit;            
        }
    }

    $url = $_GET['fromUrl']; //ONLY ADMIN CAN ACCESS TO THIS, ITS FOR UPLOAD PHOTOS FROM INSTAGRAM ONLY FROM THE ADMIN PANEL
    if(strpos($url, '.php') !== false || strpos($url, '.py') !== false || strpos($url, '.htaccess') !== false || strpos($url, '.rb') !== false){
        echo 'NO!';
        exit;
    } 
    if(strpos($url, 'instagram') === false){
        echo 'NO!';
        exit;
    }        

    if (strpos($url, 'jpg') !== false){
        $file_name = rand(0,19992).time().'.jpg';
    } else {
        $file_name = rand(0,19992).time().'.mp4';
    }
    
    $filepath = sprintf('uploads/%s_%s', uniqid(), $file_name);
    $thumbpath = sprintf('uploads/thumb_%s_%s', uniqid(), $file_name);
    if(aws('enabled') == 'Yes'){  
        $keyName = time().basename($file_name);
        $pathInS3 = 'https://s3.'.aws('region').'.amazonaws.com/' . $bucketName . '/' . $keyName;             
        try {  
            $file = $url;            
            $s3->putObject(
                array(
                    'Bucket'=> $bucketName,
                    'Key' =>  $keyName,
                    'SourceFile' => $file,
                    'ACL'    => 'public-read'
                )
            );            
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo json_encode([
                'erorr' => $e
            ]);
        }  
        $filepath = $pathInS3;
        if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false) {        
            $thumbpath = awsThumb($pathInS3,$thumbpath);
            $thumbpath = $site_url.'assets/sources/'.$thumbpath; 
        }   
    } else {
        file_put_contents($filepath, file_get_contents($url));        
        if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false || strpos($filepath, 'JPG') !== false || strpos($filepath, 'JPEG') !== false || strpos($filepath, 'PNG') !== false) {
            make_thumb($filepath, $thumbpath, 200);
        }
        $filepath = $site_url.'assets/sources/'.$filepath;
        $thumbpath = $site_url.'assets/sources/'.$thumbpath;
    }
    $result = array();
    if (strpos($filepath, 'mp4') !== false || strpos($filepath, 'ogg') !== false || strpos($filepath, 'webm') !== false) {
        $result['status'] = 'ok';
        $result['video'] = 1;
        $result['path'] = $filepath;
        $result['thumb'] = '';
        echo json_encode($result);       
    } else {
        if(watermark('enabled') == 'Yes' && aws('enabled') == 'No' && strpos($_SERVER['HTTP_REFERER'], 'admin&p=plugin') === false && strpos($_SERVER['HTTP_REFERER'], 'editor') === false){  
            $watermarkImg = watermark('watermark');
            $watermarkImg = str_replace($site_url.'assets/sources/', '', $watermarkImg);
            $watermarkTarget = str_replace($site_url.'assets/sources/', '', $filepath); 
            watermark_image($watermarkTarget,$watermarkImg,$watermarkTarget);
        }
        $result['status'] = 'ok';
        $result['video'] = 0;
        $result['path'] = $filepath;
        $result['thumb'] = $thumbpath;
        echo json_encode($result);       
    }     
} else {
    try {
        if (
            !isset($_FILES['file']['error']) ||
            is_array($_FILES['file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
        $file_name = trim(basename(stripslashes($_FILES['file']['name'])), ".\x00..\x20");
        $file_name = str_replace(" ", "", $file_name);
        $file_name = str_replace("(", "", $file_name);
        $file_name = str_replace(")", "", $file_name);
        $file_name = secureEncode($file_name);
        $filepath = sprintf('uploads/%s_%s', uniqid(), $file_name);
        $thumbpath = sprintf('uploads/thumb_%s_%s', uniqid(), $file_name);
        if(aws('enabled') == 'Yes'){  
            $keyName = time().basename($file_name);
            $pathInS3 = 'https://s3.'.aws('region').'.amazonaws.com/' . $bucketName . '/' . $keyName;             
            try {  
                $file = $_FILES['file']['tmp_name'];            
                $s3->putObject(
                    array(
                        'Bucket'=> $bucketName,
                        'Key' =>  $keyName,
                        'SourceFile' => $file,
                        'ACL'    => 'public-read'
                    )
                );            
            } catch (Aws\S3\Exception\S3Exception $e) {
                echo json_encode([
                    'erorr' => $e
                ]);
            }  
            $filepath = $pathInS3;
            if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false) {        
                $thumbpath = awsThumb($pathInS3,$thumbpath);
                $thumbpath = $site_url.'assets/sources/'.$thumbpath; 
            }   
        } else {

            $filepath = strtolower($filepath);
            if(strpos($filepath, '.php') !== false || strpos($filepath, '.py') !== false || strpos($filepath, '.htaccess') !== false || strpos($filepath, '.rb') !== false){
                exit;
            }

            if (!move_uploaded_file(
                $_FILES['file']['tmp_name'],
                $filepath
            )) {
                throw new RuntimeException('Failed to move uploaded file.');
            }
            //generate thumb
            if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false || strpos($filepath, 'JPG') !== false || strpos($filepath, 'JPEG') !== false || strpos($filepath, 'PNG') !== false) {
                make_thumb($filepath, $thumbpath, 200);
            }
            $clearfilepath = $filepath;
            $filepath = $site_url.'assets/sources/'.$filepath;
            $thumbpath = $site_url.'assets/sources/'.$thumbpath;
        }

        $result = array();
        if(strpos($filepath, 'json') !== false || strpos($filepath, 'JSON') !== false){
            $fileContents = file_get_contents($clearfilepath);
            if(strpos($_SERVER['HTTP_REFERER'], 'p=themes') !== false || strpos($_SERVER['HTTP_REFERER'], 'p=themesLanding') !== false){
                $json = json_clean_decode($fileContents);
                $settings = $json->theme_settings;
                $decoded = json_decode($fileContents,true);
                $settings = str_replace('\"', '"', $settings);
                $result['type'] = 'preset';
                $result['settings'] = $settings;
                $name = $decoded['preset'].rand(0,9999);
                $result['name'] = $name;
                $result['alias'] = $decoded['preset_alias'];
                $result['landing'] = $decoded['landing'];
                $result['data'] = $settings;
                $result['theme'] = $decoded['theme'];
                $result['base'] = $decoded['preset_base'];
                foreach ($decoded['fonts'] as $data) {
                    $mysqli->query('INSERT INTO theme_preset_fonts(preset,font,setting) VALUES 
                    ("'.$name.'","'.$data['font'].'","'.$data['setting'].'")');
                }
            }
            if(strpos($_SERVER['HTTP_REFERER'], 'p=languages') !== false){
                $decoded = json_decode($fileContents,true);
                $result['type'] = 'language';
                $result['name'] = $decoded['name'];
                $result['site_lang'] = $decoded['site_lang'];
                $query = 'INSERT INTO languages (name,prefix) VALUES ("'.$decoded['name'].'","'.$decoded['prefix'].'")';
                if ($mysqli->query($query) === TRUE) {
                    $last_id = $mysqli->insert_id;
                    foreach ($decoded['site_lang'] as $data) {
                        $mysqli->query('INSERT INTO site_lang(id,lang_id,text) VALUES 
                            ("'.$data['id'].'","'.$last_id.'","'.$data['text'].'")');
                    }
                    foreach ($decoded['app_lang'] as $data) {
                        $mysqli->query('INSERT INTO app_lang(id,lang_id,text) VALUES 
                            ("'.$data['id'].'","'.$last_id.'","'.$data['text'].'")');
                    }
                    foreach ($decoded['email_lang'] as $data) {
                        $mysqli->query('INSERT INTO email_lang(id,lang_id,text) VALUES 
                            ("'.$data['id'].'","'.$last_id.'","'.$data['text'].'")');
                    }
                    foreach ($decoded['seo_lang'] as $data) {
                        $mysqli->query('INSERT INTO seo_lang(id,lang_id,text,page) VALUES 
                            ("'.$data['id'].'","'.$last_id.'","'.$data['text'].'","'.$data['page'].'")');
                    }
                    foreach ($decoded['questions_lang'] as $data) {
                        $mysqli->query('INSERT INTO config_profile_questions(id,question,lang_id,method,q_order,gender) VALUES 
                            ("'.$data['id'].'","'.$data['question'].'","'.$last_id.'","'.$data['method'].'","'.$data['q_order'].'","'.$data['gender'].'")');
                    }
                    foreach ($decoded['answer_lang'] as $data) {
                        $mysqli->query('INSERT INTO config_profile_answers(id,qid,answer,lang_id) VALUES 
                            ("'.$data['id'].'","'.$data['qid'].'","'.$data['answer'].'","'.$last_id.'")');
                    }
                    foreach ($decoded['gender_lang'] as $data) {
                        $mysqli->query('INSERT INTO config_genders(id,name,lang_id,sex) VALUES 
                            ("'.$data['id'].'","'.$data['name'].'","'.$last_id.'","'.$data['sex'].'")');
                    }                                        

                    foreach ($decoded['landing_lang'] as $data) {
                        $mysqli->query('INSERT INTO landing_lang(id,lang_id,text,theme,preset) VALUES 
                            ("'.$data['id'].'","'.$last_id.'","'.$data['text'].'","'.$data['theme'].'","'.$data['preset'].'")');
                    }                                                                                                                        
                }
            }            
            echo json_encode($result);
        } else {
            if (strpos($filepath, 'mp4') !== false || strpos($filepath, 'ogg') !== false || strpos($filepath, 'webm') !== false) {
                $result['status'] = 'ok';
                $result['video'] = 1;
                $result['path'] = $filepath;
                $result['thumb'] = '';
                echo json_encode($result);       
            } else {
                if(watermark('enabled') == 'Yes' && aws('enabled') == 'No' && strpos($_SERVER['HTTP_REFERER'], 'admin&p=plugin') === false && strpos($_SERVER['HTTP_REFERER'], 'editor') === false){
                    if(isset($_GET['fromEditor'])){
                        $result['fromEditor'] = true;
                    } else {
                        $watermarkImg = watermark('watermark');
                        $watermarkImg = str_replace($site_url.'assets/sources/', '', $watermarkImg);
                        $watermarkTarget = str_replace($site_url.'assets/sources/', '', $filepath); 
                        watermark_image($watermarkTarget,$watermarkImg,$watermarkTarget);                        
                    }
                }
                $result['status'] = 'ok';
                $result['video'] = 0;
                $result['path'] = $filepath;
                $result['thumb'] = $thumbpath;
                echo json_encode($result);       
            }        
        }
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'path' => $filepath
        ]);
    }
}
function watermark_image($target, $wtrmrk_file, $newcopy) {
    $watermark = imagecreatefrompng($wtrmrk_file);
    imagealphablending($watermark, false);
    imagesavealpha($watermark, true);
    $imgType = get_image_type($target);
    if(strpos($imgType, 'png') !== false) {
       $img = imagecreatefrompng($target);
    } else {
       $img = imagecreatefromjpeg($target); 
    }    
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wtrmrk_w = imagesx($watermark);
    $wtrmrk_h = imagesy($watermark);
    $position = watermark('position');
    if($position == 'Bottom left'){
        $dst_x = 25;
        $dst_y = $img_h - $wtrmrk_h - 15;          
    }
    if($position == 'Bottom right'){
        $dst_x = $img_w - $wtrmrk_w - 25; 
        $dst_y = $img_h - $wtrmrk_h - 15;          
    }
    if($position == 'Top left'){
        $dst_x = 25; 
        $dst_y = 15;         
    }
    if($position == 'Top right'){
        $dst_x = $img_w - $wtrmrk_w - 25; 
        $dst_y = 15;        
    }
    if($position == 'Center'){
        $dst_x = ($img_w / 2) - ($wtrmrk_w / 2);
        $dst_y = ($img_h / 2) - ($wtrmrk_h / 2);       
    }                
    imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $wtrmrk_w, $wtrmrk_h);
    imagejpeg($img, $newcopy, 100);
    imagedestroy($img);
    imagedestroy($watermark);
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
function awsThumb($url, $filename, $width = 200, $height = true) {
    $image = ImageCreateFromString(file_get_contents($url));
    $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;
    $output = ImageCreateTrueColor($width, $height);
    ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
    ImageJPEG($output, $filename, 95); 
    return $filename; 
}
function get_image_type( $filename ) {
    $img = getimagesize( $filename );
    if ( !empty( $img[2] ) )
        return image_type_to_mime_type( $img[2] );
    return false;
}