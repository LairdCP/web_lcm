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

	function debugLevel(){
		if (!isset($_SESSION['debug'])){
			readINI();
		}
		return $_SESSION['debug'];
	}

	function generatePlugins($parsedINI){
		if (!$parsedINI["plugins"]){
			syslog(LOG_WARNING, "NO PLUGINS TO PARSE");
			$parsedINI = parse_ini_file(WebLCM_INI,true,INI_SCANNER_TYPED);
		}
		$plugins['count'] = 0;
		foreach ($parsedINI["plugins"] as $key => $value) {
			$plugins['list'][$key] = $value;
			if ($value == true){
				$plugins['count']++;
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

	function SDCERRtoString($SDCERR){
		switch($SDCERR) {
			case SDCERR_SUCCESS:
				return "SDCERR_SUCCESS";
				break;
			case SDCERR_FAIL:
				return "SDCERR_FAIL";
				break;
			case SDCERR_INVALID_NAME:
				return "SDCERR_INVALID_NAME";;
				break;
			case SDCERR_INVALID_CONFIG:
				return "SDCERR_INVALID_CONFIG";;
				break;
			case SDCERR_INVALID_DELETE:
				return "SDCERR_INVALID_DELETE";;
				break;
			case SDCERR_POWERCYCLE_REQUIRED:
				return "SDCERR_POWERCYCLE_REQUIRED";;
				break;
			case SDCERR_INVALID_PARAMETER:
				return "SDCERR_INVALID_PARAMETER";;
				break;
			case SDCERR_INVALID_EAP_TYPE:
				return "SDCERR_INVALID_EAP_TYPE";;
				break;
			case SDCERR_INVALID_WEP_TYPE:
				return "SDCERR_INVALID_WEP_TYPE";;
				break;
			case SDCERR_INVALID_FILE:
				return "SDCERR_INVALID_FILE";;
				break;
			case SDCERR_INSUFFICIENT_MEMORY:
				return "SDCERR_INSUFFICIENT_MEMORY";;
				break;
			case SDCERR_NOT_IMPLEMENTED:
				return "SDCERR_NOT_IMPLEMENTED";;
				break;
			case SDCERR_NO_HARDWARE:
				return "SDCERR_NO_HARDWARE";;
				break;
			case SDCERR_INVALID_VALUE:
				return "SDCERR_INVALID_VALUE";;
				break;
			default:
				return "UNKNOWN VALUE";
		}
	}

	function REPORT_RETURN_DBG($dir, $file, $line, $returnCode){
		if ($_SESSION['debug'] >= 2){
			syslog(LOG_WARNING, str_replace($dir . "/", '', $file) . ":" . $line . " returned " . SDCERRtoString($returnCode));
		}
		return $returnCode;
	}
?>
