<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	exec('rm /tmp/fw_update_log.txt');

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
