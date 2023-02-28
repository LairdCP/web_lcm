<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require("webLCM.php");

	$creds = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
		'LOCKED' => false,
		'TIME_LEFT' => 0,
	];

	if (!isset($_SESSION['passwordFile'])) {
		readINI();
	}

	if (!is_readable($_SESSION['passwordFile'])) {
		$returnedResult['SDCERR'] = generateCredentials();
	}

	$lines = file($_SESSION['passwordFile']);

	if (!isset($_SESSION['LOCKED'])) {
		$_SESSION['LOCKED'] = false;
	}

	if ($_SESSION['LOCKED']) {
		// Find out how long we have been locked
		$timeLocked = time() - $_SESSION['LOCKED_START_TIME'];

		// Unlock if we have waited long enough
		if ($timeLocked > $_SESSION['lockedOutTimeout']) {
			// Unlocking system
			$_SESSION['LOCKED'] = false;
			$_SESSION['FAIL_LOGIN_COUNTER'] = 0;
		} else {
			$returnedResult['LOCKED'] = true;
			$returnedResult['TIME_LEFT'] = $_SESSION['lockedOutTimeout'] - $timeLocked;
		}
	}

	if (!$_SESSION['LOCKED']) {
		if (password_verify($creds->{'username'}, trim($lines[0])) && password_verify($creds->{'password'}, trim($lines[1]))) {
			$returnedResult['SDCERR'] = SDCERR_SUCCESS;
			// Update last activity time stamp
			$_SESSION['LAST_ACTIVITY'] = time();
		}

		if ($returnedResult['SDCERR'] != SDCERR_SUCCESS) {
			if (!isset($_SESSION['FAIL_LOGIN_COUNTER'])) {
				$_SESSION['FAIL_LOGIN_COUNTER'] = 0;
			}

			$_SESSION['FAIL_LOGIN_COUNTER'] += 1;

			if ($_SESSION['FAIL_LOGIN_COUNTER'] >= $_SESSION['maxLoginAttempts']) {
				// Locking system
				$_SESSION['LOCKED'] = true;
				$_SESSION['LOCKED_START_TIME'] = time();
				$returnedResult['LOCKED'] = true;
				$returnedResult['TIME_LEFT'] = $_SESSION['lockedOutTimeout'];
			}
		}
	}

	echo json_encode($returnedResult);
?>
