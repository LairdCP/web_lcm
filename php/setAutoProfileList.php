<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$profileList = json_decode(stripslashes(file_get_contents("php://input")));

	$Count = new_ulongp();
	$result = GetNumConfigs($Count);

	if($result == SDCERR_SUCCESS){
		$cfgs = new_SDCConfig_array(ulongp_value($Count));
		$result = GetAllConfigs($cfgs, $Count);
		if($result == SDCERR_SUCCESS){
			$gcfgs = new SDCGlobalConfig();
			$result = GetGlobalSettings($gcfgs);
			if($result == SDCERR_SUCCESS){
				$autoProfile = $gcfgs->autoProfile;
				$nBit = 1;
				for($i = 1;$i <= ulongp_value($Count);$i++) {
					$singleCFG = lrd_php_sdk::SDCConfig_array_getitem($cfgs,$i - 1);
					foreach ($profileList as $key => $value){
						if ($key == $singleCFG->configName && $value == true){
							$nProfileEnabled = 1;
							$nBit = 1 << $i;
							$autoProfile |= $nBit;
						} else if ($key == $singleCFG->configName && $value == false) {
							$nProfileDisabled = 1;
							$nBit = 1 << $i;
							$autoProfile &= ~$nBit;
						}
					}
				}
			}
		}
		delete_SDCConfig_array($cfgs);
	}

	$gcfgs->autoProfile = $autoProfile;

	if($result == SDCERR_SUCCESS){
		$result = SetGlobalSettings($gcfgs);
	}

	delete_ulongp($Count);

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

?>
