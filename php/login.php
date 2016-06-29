<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$result = session_start();

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	syslog(LOG_WARNING, $result);
	if ($result == true){
		syslog(LOG_WARNING, "Is true");
		$returnedResult['SDCERR'] = SDCERR_SUCCESS;
		syslog(LOG_WARNING, $returnedResult['SDCERR']);
		if (!isset($_SESSION['LAST_ACTIVITY'])){
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
		}
		$returnedResult['LAST_ACTIVITY'] = $_SESSION['LAST_ACTIVITY'];
	}

	echo json_encode($returnedResult);
?>
