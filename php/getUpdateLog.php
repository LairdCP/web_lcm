<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_SUCCESS,
	];

	$log_file = '/tmp/fw_update_log.txt';

	if (file_exists ($log_file)){
		$returnedResult['log'] = file($log_file);
	} else {
		$returnedResult['SDCERR'] = SDCERR_FAIL;
	}

	echo json_encode($returnedResult);

?>