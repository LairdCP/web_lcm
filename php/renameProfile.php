<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$Profile = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	$cfgs = new SDCConfig();
	$result = GetConfig($Profile->{'currentName'},$cfgs);

	$cfgs->configName = $Profile->{'newName'};

	if ($result == SDCERR_SUCCESS){
		$result = ModifyConfig(trim($Profile->{'currentName'}),$cfgs);
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>