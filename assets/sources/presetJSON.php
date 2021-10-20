<?php
header('Content-type:application/json;charset=utf-8');
require('../includes/config.php');
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
// Check connection
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}
function aws($val) {
    global $mysqli;
    $config = $mysqli->query("SELECT setting_val FROM plugins_settings where plugin = 'amazon' and setting = '".$val."'");
    $result = $config->fetch_object();
    return $result->setting_val;
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
                'region'  => 'us-east-2'
            )
        );
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
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
    $filepath = sprintf('uploads/%s_%s', uniqid(), $_FILES['file']['name']);
    $thumbpath = sprintf('uploads/thumb_%s_%s', uniqid(), $_FILES['file']['name']);
    if(aws('enabled') == 'Yes'){  
        $keyName = time().basename($_FILES["file"]['name']);
        $pathInS3 = 'https://s3.us-east-2.amazonaws.com/' . $bucketName . '/' . $keyName;    
        try {  
            $file = $_FILES['file']['tmp_name'];            
            $s3->putObject(
                array(
                    'Bucket'=> $bucketName,
                    'Key' =>  $keyName,
                    'SourceFile' => $file,
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                    'ACL'    => 'public-read'
                )
            );            
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo json_encode([
                'erorr' => $e
            ]);
        } 
        $filepath = $pathInS3;       
    } else {
        if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            $filepath
        )) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        //generate thumb
        if (strpos($filepath, 'jpg') !== false || strpos($filepath, 'jpeg') !== false || strpos($filepath, 'png') !== false) {
            make_thumb($filepath, $thumbpath, 200);
        }
        $check_bar = substr($site_url, -1);
        if($check_bar != '/'){
            $site_url = $site_url.'/'; 
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
        $result['status'] = 'ok';
        $result['video'] = 0;
        $result['path'] = $filepath;
        $result['thumb'] = $thumbpath;
        echo json_encode($result);       
    }
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'path' => $filepath
    ]);
}
function watermark_image($target, $wtrmrk_file, $newcopy) {
    $watermark = imagecreatefrompng($wtrmrk_file);
    imagealphablending($watermark, false);
    imagesavealpha($watermark, true);
    $img = imagecreatefromjpeg($target);
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wtrmrk_w = imagesx($watermark);
    $wtrmrk_h = imagesy($watermark);
    $dst_x = ($img_w / 2) - ($wtrmrk_w / 2); // For centering the watermark on any image
    $dst_y = ($img_h / 2) - ($wtrmrk_h / 2); // For centering the watermark on any image
    imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $wtrmrk_w, $wtrmrk_h);
    imagejpeg($img, $newcopy, 100);
    imagedestroy($img);
    imagedestroy($watermark);
}
function make_thumb($src, $dest, $desired_width) {
    if(exif_imagetype($src) != IMAGETYPE_JPEG){
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