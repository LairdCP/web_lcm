<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$advanced = json_decode(stripslashes(file_get_contents("php://input")));

	$result = LRD_WF_SetSuppLogLevel($advanced->{'suppDebugLevel'});
	if ($result == SDCERR_SUCCESS){
			$result = LRD_WF_Driver_set_debug($advanced->{'driverDebugLevel'}, null);
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
