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
		'SDCERR' => SDCERR_SUCCESS,
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

	if (file_exists ('/tmp/fw_update_log.txt')){
		$returnedResult['fwUpdate'] = "running";
	} else {
		$returnedResult['fwUpdate'] = "stopped";
	}

	echo json_encode($returnedResult);

?>
