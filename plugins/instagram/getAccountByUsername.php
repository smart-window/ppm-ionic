<?php
require 'vendor/autoload.php';

// If account is public you can query Instagram without auth

$instagram = new \InstagramScraper\Instagram();

// For getting information about account you don't need to auth:

$account = $instagram->getAccount('kathk7_');

// Available fields
echo "Account info:\n<br>";
//echo "Id: {$account->getId()}\n<br>";
echo "Username: {$account->getUsername()}\n<br>";
echo "Full name: {$account->getFullName()}\n<br>";
echo "Biography: {$account->getBiography()}\n<br>";
echo "Profile picture url: {$account->getProfilePicUrl()}\n<br>";
//echo "External link: {$account->getExternalUrl()}\n<br>";
echo "Number of published posts: {$account->getMediaCount()}\n<br>";
echo "Number of followers: {$account->getFollowsCount()}\n<br>";
//echo "Number of follows: {$account->getFollowedByCount()}\n<br>";
//echo "Is private: {$account->isPrivate()}\n<br>";
//echo "Is verified: {$account->isVerified()}\n<br>";

$result = $instagram->getPaginateMedias('kathk7_');
$medias = $result['medias'];
if ($result['hasNextPage'] === true) {
    $result = $instagram->getPaginateMedias('kathk7_', $result['maxId']);
    $medias = array_merge($medias, $result['medias']);
}
foreach ($medias as $media) {
	echo "Created at: {$media->getCreatedTime()}\n<br>";
	echo "Caption: {$media->getCaption()}\n<br>";
	echo "Number of likes: {$media->getLikesCount()}<br>";
	echo "<img style='width:300px' src='".$media->getImageHighResolutionUrl()."'><br>";
	echo "Media type (video or image): {$media->getType()}<br>";
}

/*
$result = $instagram->getPaginateMedias('nicolas_mezquida');
$medias = $result['medias'];
if ($result['hasNextPage'] === true) {
    $result = $instagram->getPaginateMedias('nicolas_mezquida', $result['maxId']);
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
