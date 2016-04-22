<?php
	include("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$oldProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	$cconfig = new SDCConfig();

	$result = GetConfig($oldProfile->{'profileName'}, $cconfig);
	if ($result == SDCERR_SUCCESS){
		$result = DeleteConfig(trim($oldProfile->{'profileName'}));
	}

	$returnedResult = [
		'SDCERR' => $result,
	];

	echo json_encode($returnedResult);

?>
