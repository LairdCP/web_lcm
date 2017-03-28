<?php
# Copyright (c) 2017, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$Profile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();
	$result = GetConfig(uchr($Profile->{'currentName'}),$cfgs);

	$cfgs->configName = uchr($Profile->{'newName'});

	if ($result == SDCERR_SUCCESS){
		$result = ModifyConfig(uchr($Profile->{'currentName'}),$cfgs);
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>
