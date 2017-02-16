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
			$uploads_dir = nullTrim($cfgs->certPath);
			// Make sure path includes delimiter
			if (substr($uploads_dir, -1) != "/"){
				$uploads_dir .= "/";
			}

			if (($_FILES["file"]["type"] == "application/pkix-cert")
				|| ($_FILES["file"]["type"] == "application/x-pkcs7-certificates")
				|| ($_FILES["file"]["type"] == "application/octet-stream")
				|| ($_FILES["file"]["type"] == "application/x-pkcs12")
				|| ($_FILES["file"]["type"] == "application/x-x509-ca-cert"))
			{
				if ($_FILES["file"]["type"] == "application/octet-stream"){
					$fileType = substr($_FILES["file"]["name"], -4);
					if (($fileType != ".pac") && ($fileType != ".cnf") && ($fileType != ".pem")){
						$result = SDCERR_INVALID_FILE;
					}
				}
				if($result == SDCERR_SUCCESS){
					// ensure a safe filename
					$name = preg_replace("/[^A-Z0-9._-]/i", "_", $_FILES["file"]["name"]);
					$tmp_name = $_FILES["file"]["tmp_name"];

					$success = move_uploaded_file($tmp_name, "$uploads_dir/$name");
					if (!$success){
						$result = SDCERR_FAIL;
					} else {
						chmod(UPLOAD_DIR . $name, 0644);
					}
				}
			}else{
				$result = SDCERR_INVALID_FILE;
			}
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>
