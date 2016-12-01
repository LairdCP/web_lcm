<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../../../php/webLCM.php");
	require("remote_update.php");

	if (file_exists(FW_LOGFILE)){
		$returnedResult['log'] = file(FW_LOGFILE);
	} else {
		$returnedResult['SDCERR'] = SDCERR_FAIL;
	}

	if (trim(end($returnedResult['log'])) == "Done." && !file_exists(FW_LOGFILE_LOCK)){
		$logToSyslogLock = fopen(FW_LOGFILE_LOCK, "w");
		fclose($logToSyslogLock);
		exec("logger -t webLCM -f " . FW_LOGFILE);
	}

	echo json_encode($returnedResult);

?>
