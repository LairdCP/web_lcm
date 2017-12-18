<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");

	$creds = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
		'LOCKED' => false,
		'TIME_LEFT' => 0,
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

	// Find out how long we have been locked
	$timeLocked = time() - $_SESSION['LOCKED_START_TIME'];

	// Unlock if we have waited long enough
	if (isset($_SESSION['LOCKED_START_TIME']) && $_SESSION['FAIL_LOGIN_COUNTER'] >= $_SESSION['maxLoginAttempts'] && $timeLocked > $_SESSION['lockedOutTimeout']){
		// Unlocking system
		$_SESSION['LOCKED'] = false;
		$_SESSION['FAIL_LOGIN_COUNTER'] = 0;
	}

	if (!$_SESSION['LOCKED']){
		if (password_verify( $creds->{'username'},trim($lines[0])) == true && password_verify($creds->{'password'},trim($lines[1])) == true){
			$returnedResult['SDCERR'] = SDCERR_SUCCESS;
			// Update last activity time stamp
			$_SESSION['LAST_ACTIVITY'] = time();
		} else {
			$_SESSION['LOCKED_START_TIME'] = time();
		}
	}

	if ($returnedResult['SDCERR'] != SDCERR_SUCCESS){
		$_SESSION['FAIL_LOGIN_COUNTER'] += 1;
	}

	if ($_SESSION['FAIL_LOGIN_COUNTER'] >= $_SESSION['maxLoginAttempts']){
		// Locking system
		$_SESSION['LOCKED'] = true;
		$returnedResult['LOCKED'] = true;
		if (!isset($_SESSION['LOCKED_START_TIME'])){
			$_SESSION['LOCKED_START_TIME'] = time();
		}
	}

	$returnedResult['TIME_LEFT'] = $_SESSION['lockedOutTimeout'] - $timeLocked;

	echo json_encode($returnedResult);
?>
