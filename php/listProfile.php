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

	if (isset($_SESSION['LAST_ACTIVITY'])){
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60)) {
			// last request was more than 30 minutes ago
			session_unset();     // unset $_SESSION variable for the run-time
			session_destroy();   // destroy session data in storage
			echo json_encode($returnedResult);
			return;
		} else {
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
			$returnedResult['SESSION'] = SDCERR_SUCCESS;
		}
	} else {
		echo json_encode($returnedResult);
	}


	$rcs = new_RADIOCHIPSETp();
	$result = LRD_WF_GetRadioChipSet($rcs);
	if($result == SDCERR_SUCCESS){
		$gcfgs = new SDCGlobalConfig();
		$result = GetGlobalSettings($gcfgs);
		if($result == SDCERR_SUCCESS){
			$Count = new_ulongp();
			$currentConfig = str_repeat(" ",CONFIG_NAME_SZ);
			$result = GetNumConfigs($Count);

			if($result == SDCERR_SUCCESS){
				$cfgs = new_SDCConfig_array(ulongp_value($Count));
				$result = GetAllConfigs($cfgs, $Count);
				if($result == SDCERR_SUCCESS){
					$result = GetCurrentConfig(NULL, $currentConfig);
					$returnedResult['currentConfig'] = trim($currentConfig);
					$nBit = 1;
					for($i = 0;$i < ulongp_value($Count);$i++) {
						$singleCFG = lrd_php_sdk::SDCConfig_array_getitem($cfgs,$i);
						$profileList[$i] = $singleCFG->configName;

						$nBit = $nBit << 1;
						if (($gcfgs->autoProfile & $nBit) == $nBit){
							$autoProfileList[$singleCFG->configName] = true;
						}else{
							$autoProfileList[$singleCFG->configName] = false;
						}
					}
				}
				delete_SDCConfig_array($cfgs);
			}
		}
	}
	$returnedResult['NumConfigs'] = ulongp_value($Count);
	$returnedResult['profiles'] = $profileList;
	if (($gcfgs->autoProfile & ~1) or ($gcfgs->autoProfile & 1)){
		$returnedResult['autoProfiles'] = $autoProfileList;
	}
	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

	delete_RADIOCHIPSETp($rcs);
	delete_ulongp($Count);

?>
