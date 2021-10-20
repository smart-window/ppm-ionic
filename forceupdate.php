<?php
ini_set('max_execution_time',90);
require('assets/includes/config.php');
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}
$updated = false;
$aV = $_GET['version'];

if (file_exists('updates/update-'.$aV.'.zip' )) {
	unlink('updates/update-'.$aV.'.zip');
}

$newUpdate = file_get_contents('https://www.premiumdatingscript.com/updates/belloo/update-'.$aV.'.zip');
$dlHandler = fopen('updates/update-'.$aV.'.zip', 'w');
if ( !fwrite($dlHandler, $newUpdate) ) { exit(); }
fclose($dlHandler);


$zipHandle = zip_open('updates/update-'.$aV.'.zip');	

while ($aF = zip_read($zipHandle) ) {

	$thisFileName = zip_entry_name($aF);
	$thisFileDir = dirname($thisFileName);
	//Continue if its not a file


	if (!is_dir($thisFileDir ) ){
		 mkdir($thisFileDir,0777,true);
	}

	if ( !is_dir($thisFileName) ) {
		$contents = zip_entry_read($aF, zip_entry_filesize($aF));
		$contents = str_replace("\r\n", "\n", $contents);
		$updateThis = '';
		if ( $thisFileName == 'upgrade.php' ){
			$upgradeExec = fopen ('upgrade.php','w');
			fwrite($upgradeExec, $contents);
			fclose($upgradeExec);
			include ('upgrade.php');
			unlink('upgrade.php');
		} else if ($thisFileName == 'upgrade.sql'){
			global $mysqli;
			$sqlExec = fopen('upgrade.sql','w');
			fwrite($sqlExec, $contents);
			fclose($sqlExec);
				
			$queries = file_get_contents("upgrade.sql");
			$mysqli->multi_query($queries);
			if($mysqli->more_results()){
				while ($mysqli->next_result()) {
					if (!$mysqli->more_results()){
						break;
					} 
				}
			}
			unlink('upgrade.sql');
		} else {
			if (substr($thisFileName,-1,1) == '/') continue;
			$updateThis = fopen($thisFileName, 'w');
			fwrite($updateThis, $contents);
			fclose($updateThis);
			unset($contents);
		}
		$updated = true;
	}
	zip_entry_close($aF);
}

zip_close($zipHandle);

if ($updated == true){

	$mysqli->query("UPDATE settings set setting_val = '$aV' where setting = 'currentVersion'");
    $mysqli->query("UPDATE settings SET setting_val = 'No' WHERE setting = 'updateAvailable'");
    $mysqli->query("UPDATE settings SET setting_val = '0' WHERE setting = 'checkUpdate'");

    header('Location: index.php?page=admin&p=main_dashboard&updated='.$aV);
    exit;	
}
?>