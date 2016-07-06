<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$result = session_start();
	$creds = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	if ($result == true){
		if (!isset($_SESSION['passwordFile'])){
			readINI();
		}
		$lines = file($_SESSION['passwordFile']);
		if ($lines != false){
			if (password_verify( $creds->{'username'},trim($lines[0])) == true && password_verify($creds->{'password'},trim($lines[1])) == true){
				$returnedResult['SDCERR'] = SDCERR_SUCCESS;
				if (!isset($_SESSION['LAST_ACTIVITY'])){
					$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
				}
			}
		}
	}

	echo json_encode($returnedResult);
?>
