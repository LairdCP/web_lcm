<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}

	$suppLogLevel = new_WF_SUPP_LOGLEVELp();
	$result = LRD_WF_GetSuppLogLevel($suppLogLevel);
	$returnedResult['suppDebugLevel'] = WF_SUPP_LOGLEVELp_value($suppLogLevel);
	delete_WF_SUPP_LOGLEVELp($suppLogLevel);

	$driverLogLevel = new_LRD_WF_DRV_DEBUGp();
	$result = LRD_WF_Driver_get_debug($driverLogLevel, null);
	$returnedResult['driverDebugLevel'] = LRD_WF_DRV_DEBUGp_value($driverLogLevel);
	delete_LRD_WF_DRV_DEBUGp($driverLogLevel);

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
