<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_SUCCESS,
	];

	if (file_exists ('/tmp/fw_update_log.txt')){
		$returnedResult['fwUpdate'] = "running";
	} else {
		$returnedResult['fwUpdate'] = "stopped";
	}

	echo json_encode($returnedResult);

?>