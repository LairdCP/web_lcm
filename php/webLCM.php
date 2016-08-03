<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/lrd_php_sdk.php");

	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	session_start();
	header("Content-Type: application/json");

	define("WebLCM_INI","/var/www/docs/webLCM.ini");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
		'SESSION' => SDCERR_FAIL,
	];

	function readINI(){
		$iniFile = parse_ini_file(WebLCM_INI,true,INI_SCANNER_TYPED);
		foreach ($iniFile["settings"] as $key => $value) {
			$_SESSION[$key] = $value;
		}
		return $iniFile;
	}

	function generatePlugins($parsedINI){
		if (!$parsedINI["plugins"]){
			syslog(LOG_WARNING, "NO PLUGINS TO PARSE");
			$parsedINI = parse_ini_file(WebLCM_INI,true,INI_SCANNER_TYPED);
		}
		foreach ($parsedINI["plugins"] as $key => $value) {
			$plugins['list'][$key] = $value;
			if ($value == true){
				$pluginPath = "../plugins/" . $key . "/php/" . $key . ".php";
				require($pluginPath);
				$plugins[$key] = $key();
			}
		}
		return $plugins;
	}

	function skipLogin(){
		if (!isset($_SESSION['ignoreLogin'])){
			readINI();
		}
		if ($_SESSION['ignoreLogin'] == true){
			return SDCERR_SUCCESS;
		}

		return SDCERR_FAIL;
	}

	function verifyAuthentication($level){
		if (skipLogin() == SDCERR_SUCCESS){
			return SDCERR_SUCCESS;
		}
		if (isset($_SESSION['LAST_ACTIVITY'])){
			if (!isset($_SESSION['timeoutLogin'])){
				readINI();
			}
			if (time() - $_SESSION['LAST_ACTIVITY'] > $_SESSION['timeoutLogin']) {
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

	function generateCredentials(){
		$iniFile = readINI();
		if ($iniFile["settings"]["passwordFile"] && $iniFile["default_credentials"]["userName"] && $iniFile["default_credentials"]["passWord"]){
			if (!file_exists($iniFile["settings"]["passwordFile"])){
				$userName = password_hash($iniFile["default_credentials"]["userName"],PASSWORD_DEFAULT);
				$passWord = password_hash($iniFile["default_credentials"]["passWord"],PASSWORD_DEFAULT);
				if ($userName && $passWord){
					$credentialFile = fopen($iniFile["settings"]["passwordFile"], "w");
					if ($credentialFile != false){
						fwrite($credentialFile, $userName . "\n");
						fwrite($credentialFile, $passWord . "\n");
						fclose($credentialFile);

						return SDCERR_SUCCESS;
					}
				}
			}
			return SDCERR_FAIL;
		} else {
			return SDCERR_INVALID_PARAMETER;
		}
	}
?>
