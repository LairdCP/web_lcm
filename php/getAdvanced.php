<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

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