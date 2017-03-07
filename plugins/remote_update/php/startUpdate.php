<?php
# Copyright (c) 2017, Laird
# Contact: ews-support@lairdtech.com

	require("../../../php/webLCM.php");
	require("remote_update.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$update = json_decode(stripslashes(file_get_contents("php://input")));

	if($update->{'fwUpdateTM'} == 1){
		mkdir(FW_TM_Directory);
		$fwTMFile = fopen(FW_TM_Directory . FW_TM_File, "w");
		fwrite($fwTMFile,"on \n");
		fclose($fwTMFile);
		$result = SDCERR_SUCCESS;
		if (!file_exists(FW_TM_Directory . FW_TM_File)){
			$result = SDCERR_FAIL;
		}
	} else if ($update->{'fwUpdateTM'} == 0){
		if (unlink(FW_TM_Directory . FW_TM_File)){
			$result = SDCERR_SUCCESS;
		}
	}

	if ($result == SDCERR_SUCCESS){
		if ($update->{'remoteUpdate'} != null){
			$fwTRANSFile = fopen(FW_TRANSFER_LIST, "w");
			if (isset($_SESSION["passwordFile"])){
				fwrite($fwTRANSFile,$_SESSION["passwordFile"] . " \n");
			}
			fwrite($fwTRANSFile,WebLCM_INI . " \n");
			fclose($fwTRANSFile);
			if (file_exists(FW_TRANSFER_LIST)){
				exec('fw_update -f -xnr ' . escapeshellcmd($update->{'remoteUpdate'}) . ' &> ' . FW_LOGFILE . ' &');
				syslog(LOG_INFO, "fw_update started from WebLCM with options: -f -xnr");
				syslog(LOG_INFO, "fw_update update path: " . escapeshellcmd($update->{'remoteUpdate'}));
			} else {
				$result = SDCERR_FAIL;
			}
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>
