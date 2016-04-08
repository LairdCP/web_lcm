<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$Count = new_ulongp();
	$currentConfig = str_repeat(" ",CONFIG_NAME_SZ);
	$result = GetNumConfigs($Count);

	$returnedResult = [
		'SDCERR' => $result,
		'NumConfigs' => ulongp_value($Count),
	];

	if($result == SDCERR_SUCCESS){
		$cfgs = new_SDCConfig_array(ulongp_value($Count));
		$result = GetAllConfigs($cfgs, $Count);
		if($result == SDCERR_SUCCESS){
			$result = GetCurrentConfig(NULL, $currentConfig);
			$returnedResult['currentConfig'] = trim($currentConfig);
			for($i = 0;$i < ulongp_value($Count);$i++) {
				$singleCFG = lrd_php_sdk::SDCConfig_array_getitem($cfgs,$i);
				$profileList[$i] = $singleCFG->configName;
			}
		}
		delete_SDCConfig_array($cfgs);
	}
	$returnedResult['profiles'] = $profileList;

	echo json_encode($returnedResult);

	delete_ulongp($Count);

?>
