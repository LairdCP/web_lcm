<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");

	$creds = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	if (!isset($_SESSION['passwordFile'])){
		readINI();
	}
	$lines = file($_SESSION['passwordFile']);
	if ($lines == false){
		$genCreds = generateCredentials();
		if ($genCreds == SDCERR_SUCCESS){
			$lines = file($_SESSION['passwordFile']);
		}
		$returnedResult['SDCERR'] = $genCreds;
	}
	if (password_verify( $creds->{'username'},trim($lines[0])) == true && password_verify($creds->{'password'},trim($lines[1])) == true){
		$returnedResult['SDCERR'] = SDCERR_SUCCESS;
		if (!isset($_SESSION['LAST_ACTIVITY'])){
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
		}
	}

	echo json_encode($returnedResult);
?>
