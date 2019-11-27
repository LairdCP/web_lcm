<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	$SDKVersion = new_ulongp();
	$result = GetSDKVersion($SDKVersion);
	$SDKVersionValue = ulongp_value($SDKVersion);
	$returnedResult['sdk'] = "";
	if($result == SDCERR_SUCCESS){
		$sdkVersionArray = array((($SDKVersionValue & 0xff000000) >> 24),(($SDKVersionValue & 0xff0000) >> 16),(($SDKVersionValue & 0xff00) >> 8),($SDKVersionValue & 0xff));
		$returnedResult['sdk'] = implode('.', $sdkVersionArray);
	}
	delete_ulongp($SDKVersion);

	$rcs = new_RADIOCHIPSETp();
	$result = LRD_WF_GetRadioChipSet($rcs);
	$returnedResult['chipset'] = "No Hardware Detected";
	if($result == SDCERR_SUCCESS){
		switch (RADIOCHIPSETp_value($rcs)) {
			case RADIOCHIPSET_SDC10:
				$returnedResult['chipset'] = "10";
				break;
			case RADIOCHIPSET_SDC15:
				$returnedResult['chipset'] = "15";
				break;
			case RADIOCHIPSET_SDC30:
				$returnedResult['chipset'] = "30";
				break;
			case RADIOCHIPSET_SDC40L:
				$returnedResult['chipset'] = "40L";
				break;
			case RADIOCHIPSET_SDC40NBT:
				$returnedResult['chipset'] = "40NBT";
				break;
			case RADIOCHIPSET_SDC45:
				$returnedResult['chipset'] = "45";
				break;
			case RADIOCHIPSET_SDC50:
				$returnedResult['chipset'] = "50";
				break;
			default:
				break;
		}
	}
	delete_RADIOCHIPSETp($rcs);

	$status = new CF10G_STATUS();
	$result = GetCurrentStatus($status);
	$returnedResult['driver'] = "Driver not loaded.  Unable to check driver version.";
	if($result == SDCERR_SUCCESS){
		$DriverVersion = $status->driverVersion;
		if ($DriverVersion & 0xff000000)
		{
			$driverVersionArray = array((($DriverVersion & 0xff000000) >> 24),(($DriverVersion & 0xff0000) >> 16),(($DriverVersion & 0xff00) >> 8),($DriverVersion & 0xff));
		}
		else
		{
			$driverVersionArray = array((($DriverVersion & 0xff0000) >> 16),(($DriverVersion & 0xff00) >> 8),($DriverVersion & 0xff));
		}
		$returnedResult['driver'] = implode('.', $driverVersionArray);
	}

	$returnedResult['supplicant'] = "";
	exec('sdcsupp -v',$SDCSuppOutput,$result);
	if($result == SDCERR_SUCCESS){
		$returnedResult['supplicant'] = $SDCSuppOutput[0];
	}

	$returnedResult['build'] = "";
	if(file_exists('/etc/laird-release') | file_exists('/etc/summit-release')){
		$BuildString = file_get_contents('/etc/laird-release');
		if($BuildString == false){
			$BuildString = file_get_contents('/etc/summit-release');
		}
		if($BuildString != false){
			$returnedResult['build'] = $BuildString;
		}
	}

	$returnedResult['php_sdk'] = LRD_PHP_SDK_VERSION_MAJOR . "." . LRD_PHP_SDK_VERSION_MINOR . "." . LRD_PHP_SDK_VERSION_REVISION . "." . LRD_PHP_VERSION_SUB_REVISION;

	$firmwareStringLength = new_intp();
	intp_assign($firmwareStringLength,80);
	$firmwareString = str_repeat(" ",intp_value($firmwareStringLength));
	$returnedResult['firmware'] = "Firmware not loaded.  Unable to check firmware version.";
	$firmwareSDCERR = LRD_WF_GetFirmwareVersionString($firmwareString, $firmwareStringLength);
	if ($firmwareSDCERR == SDCERR_SUCCESS){
		$returnedResult['firmware'] = $firmwareString;
	}else{
		$result = $firmwareSDCERR;
	}
	delete_intp($firmwareStringLength);

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

?>
