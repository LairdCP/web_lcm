<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	unlink(FW_LOGFILE);
	unlink(FW_LOGFILE_LOCK);

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
