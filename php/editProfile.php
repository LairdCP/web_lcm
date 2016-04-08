<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$oldProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();
	$result = GetConfig($oldProfile->{'profileName'},$cfgs);

	$returnedResult = [
		'SDCERR' => $result,
	];

	if($result == SDCERR_SUCCESS){
		$returnedResult['configName'] = $cfgs->configName;
		$returnedResult['SSID'] = $cfgs->SSID;
		$returnedResult['clientName'] = $cfgs->clientName;
		$returnedResult['txPower'] = $cfgs->txPower;
		$returnedResult['authType'] = $cfgs->authType;
		$returnedResult['eapType'] = $cfgs->eapType;
		$returnedResult['powerSave'] = $cfgs->powerSave;
		$returnedResult['wepType'] = $cfgs->wepType;
		$returnedResult['bitRate'] = $cfgs->bitRate;
		$returnedResult['radioMode'] = $cfgs->radioMode;
	}


	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);

// typedef struct _SDCConfig {
// 	char        configName[CONFIG_NAME_SZ];
// 	char        SSID[SSID_SZ];
// 	char        clientName[CLIENT_NAME_SZ];
// 	int         txPower;
// 	AUTH        authType;
// 	EAPTYPE     eapType;
// 	POWERSAVE   powerSave; // POWERSAVE enum is 4 bytes.  Upper word used for pspDelay
// 	WEPTYPE     wepType;
// 	BITRATE     bitRate;
// 	RADIOMODE   radioMode;
// 	CRYPT       userName;
// 	CRYPT       userPwd;
// 	CRYPT       PSK;
// 	CRYPT       WEPKeys;
// } SDCConfig;

?>
