<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");

	$log_file = '/tmp/fw_update_log.txt';

	if (file_exists ($log_file)){
		$returnedResult['log'] = file($log_file);
	} else {
		$returnedResult['SDCERR'] = SDCERR_FAIL;
	}

	echo json_encode($returnedResult);

?>
