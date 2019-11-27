<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
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

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>
