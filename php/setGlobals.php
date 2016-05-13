<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$global = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	$cfgs = new SDCGlobalConfig();
	$result = GetGlobalSettings($cfgs);

	if($result == SDCERR_SUCCESS){
		$cfgs->authServerType = $global->{'authServerType'};

		$result = SetGlobalSettings($cfgs);
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>