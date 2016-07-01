<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$Profile = json_decode(stripslashes(file_get_contents("php://input")));

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
