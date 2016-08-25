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

	$result = SDCERR_FAIL;
	$safeFile = TRUE;
	$uploads_dir = '/etc/ssl/';

	if (($_FILES["file"]["type"] == "application/pkix-cert")
		|| ($_FILES["file"]["type"] == "application/x-pkcs7-certificates")
		|| ($_FILES["file"]["type"] == "application/octet-stream")
		|| ($_FILES["file"]["type"] == "application/x-pkcs12")
		|| ($_FILES["file"]["type"] == "application/x-x509-ca-cert"))
	{
		if ($_FILES["file"]["type"] == "application/octet-stream"){
			$fileType = substr($_FILES["file"]["name"], -4);
			if (($fileType != ".pac") && ($fileType != ".cnf")){
				$safeFile = FALSE;
			}
		}
		if ($safeFile){
			// ensure a safe filename
			$name = preg_replace("/[^A-Z0-9._-]/i", "_", $_FILES["file"]["name"]);
			$tmp_name = $_FILES["file"]["tmp_name"];

			$success = move_uploaded_file($tmp_name, "$uploads_dir/$name");
			if (!$success){
				$result = SDCERR_FAIL;
			}
			chmod(UPLOAD_DIR . $name, 0644);
			$result = SDCERR_SUCCESS;
		}
	}else{
		$result = SDCERR_INVALID_FILE;
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>
