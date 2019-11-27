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

	$returnedResult['fwUpdateTM'] = 0;
	if(file_exists(FW_TM_File)){
		$returnedResult['fwUpdateTM'] = 1;
	}

	$returnedResult['SDCERR'] = SDCERR_SUCCESS;

	echo json_encode($returnedResult);

?>
