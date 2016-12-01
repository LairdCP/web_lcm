<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	session_start();

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	session_unset();     // unset $_SESSION variable for the run-time
	$result = session_destroy();   // destroy session data in storage
	if ($result == true){
		$returnedResult['SDCERR'] = SDCERR_SUCCESS;
	}

	echo json_encode($returnedResult);
?>
