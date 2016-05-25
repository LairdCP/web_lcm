<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_SUCCESS,
	];

	exec('reboot');

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>