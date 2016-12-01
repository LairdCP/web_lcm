<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
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
	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	delete_RADIOCHIPSETp($rcs);
	delete_ulongp($Count);

	echo json_encode($returnedResult);
?>
