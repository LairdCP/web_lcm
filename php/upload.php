<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];
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

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);
?>