<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");

	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	session_start();
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
		'SESSION' => SDCERR_FAIL,
	];
	function skipLogin(){
		// return SDCERR_SUCCESS;
		return SDCERR_FAIL;
	}

	function verifyAuthentication($level){
		if (skipLogin() == SDCERR_SUCCESS){
			return SDCERR_SUCCESS;
		}
		if (isset($_SESSION['LAST_ACTIVITY'])){
			if (time() - $_SESSION['LAST_ACTIVITY'] > 60) {
				// last request was more than 30 minutes ago
				session_unset();     // unset $_SESSION variable for the run-time
				session_destroy();   // destroy session data in storage
			} else {
				if ($level){
					$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
				}
				return SDCERR_SUCCESS;
			}
		}
		return SDCERR_FAIL;
	}

?>
