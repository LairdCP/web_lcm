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

	$result = ActivateConfig($Profile->profileName);

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
