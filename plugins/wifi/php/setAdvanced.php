<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$advanced = json_decode(stripslashes(file_get_contents("php://input")));

	$result = LRD_WF_SetSuppLogLevel($advanced->{'suppDebugLevel'});
	if ($result == SDCERR_SUCCESS){
			$result = LRD_WF_Driver_set_debug($advanced->{'driverDebugLevel'}, null);
	}

	if($advanced->{'fwUpdateTM'} == 1){
		$fwTMFile = fopen(FW_TM_File, "w");
		if ($fwTMFile != false){
			fwrite($fwTMFile,"on \n");
			fclose($fwTMFile);
			$result = SDCERR_SUCCESS;
		}
	} else if ($advanced->{'fwUpdateTM'} == 0){
		$result = SDCERR_FAIL;
		if (unlink(FW_TM_File)){
			$result = SDCERR_SUCCESS;
		}
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>
