<?php
# Copyright (c) 2017, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}

	$result = SDCERR_FAIL;
	$rcs = new_RADIOCHIPSETp();
	$result = LRD_WF_GetRadioChipSet($rcs);
	if($result == SDCERR_SUCCESS){
		$cfgs = new SDCGlobalConfig();
		$result = GetGlobalSettings($cfgs);
		if($result == SDCERR_SUCCESS){
			$directory = nullTrim($cfgs->certPath);
			// Make sure path includes delimiter
			if (substr($directory, -1) != "/"){
				$directory .= "/";
			}

			function filename_if_file($file){
				global $directory;
				$full_path = $directory . $file;
				if(is_file($full_path)){
					return 1;
				}
			}
			$scanned_directory = array_filter(scandir($directory), "filename_if_file");
			$reordered_directory = array_combine(range(1, count($scanned_directory)), array_values($scanned_directory));
			$returnedResult['certs'] = $reordered_directory;
		}
	}
	delete_RADIOCHIPSETp($rcs);

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>
