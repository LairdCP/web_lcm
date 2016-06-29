<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	session_start();
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
		'SESSION' => SDCERR_FAIL,
	];

	if (isset($_SESSION['LAST_ACTIVITY'])){
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60)) {
			// last request was more than 30 minutes ago
			session_unset();     // unset $_SESSION variable for the run-time
			session_destroy();   // destroy session data in storage
			echo json_encode($returnedResult);
			return;
		} else {
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
			$returnedResult['SESSION'] = SDCERR_SUCCESS;
		}
	} else {
		echo json_encode($returnedResult);
	}

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

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
