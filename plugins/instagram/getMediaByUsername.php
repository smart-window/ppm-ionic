<?php
include '../../assets/includes/core.php';
require 'vendor/autoload.php';
$instagram = new \InstagramScraper\Instagram();


$username = $_GET['username'];
$sort = $_GET['sort'];
$limit = $_GET['limit'];
$uid = $_GET['uid'];
$type = $_GET['type'];

$username = str_replace(' ', '', $username);
$result = $instagram->getPaginateMedias($username);
$medias = $result['medias'];
if ($result['hasNextPage'] === true) {
    $result = $instagram->getPaginateMedias($username, $result['maxId']);
    $medias = array_merge($medias, $result['medias']);
}

$arr = array();
$i = 0;

foreach ($medias as $media) {
	
	$checkData = getData('users_photos','ig_id','WHERE u_id = '.$uid.' AND ig_id = "'.$media->getId().'"');
	if($checkData != 'noData'){ 
		continue; 
	}

	if($type != 'All'){
		if($type == 'videos' && $media->getType() != 'video'){
			continue;
		}
		if($type == 'photos' && $media->getType() == 'video'){
			continue;
		}		
	}

	if($i == $limit){ 
		break; 
	}

	$arr[$i]['src'] = $media->getImageHighResolutionUrl();
	//$arr[$i]['src'] = $media->getImageStandardResolutionUrl();
	//$arr[$i]['src'] = $media->getImageLowResolutionUrl();
	$arr[$i]['type'] = $media->getType();
	if($media->getType() == 'video'){
		$link = $media->getLink();
		$json_media_by_url = $instagram->getMediaByUrl($link);
		$arr[$i]['src'] = $json_media_by_url['videoStandardResolutionUrl'];					
	}	
	$arr[$i]['ig_id'] = $media->getId();
	$arr[$i]['likes'] = $media->getLikesCount();
	$i++;
}

$sortArr = array();
if(count($arr) > 0){
	foreach ($arr as $key => $row){
		$sortArr[$key] = $row[$sort];
	}
	array_multisort($sortArr, SORT_DESC, $arr);	
}
echo json_encode($arr);
/*
$result = $instagram->getPaginateMedias($username);
$medias = $result['medias'];
if ($result['hasNextPage'] === true) {
    $result = $instagram->getPaginateMedias($username, $result['maxId']);
    $medias = array_merge($medias, $result['medias']);
}
foreach ($medias as $media) {
	echo "Media info:\n<br>";
	echo "Id: {$media->getId()}\n<br>";
	echo "Shortcode: {$media->getShortCode()}\n<br>";
	echo "Created at: {$media->getCreatedTime()}\n<br>";
	echo "Caption: {$media->getCaption()}\n<br>";
	echo "Number of comments: {$media->getCommentsCount()}<br>";
	echo "Number of likes: {$media->getLikesCount()}<br>";
	echo "<img style='width:300px' src='".$media->getImageHighResolutionUrl()."'><br>";
	echo "Get link: {$media->getLink()}<br>";	
	echo "Media type (video or image): {$media->getType()}<br>";
}*/
