<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require("../../../php/webLCM.php");
	require("remote_update.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}

	exec('reboot');

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
