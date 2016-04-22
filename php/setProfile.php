<?php
	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$Profile = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	$cfgs = new SDCConfig();
	$result = GetConfig($Profile->{'profileName'},$cfgs);

	$cfgs->SSID = $Profile->{'SSID'};
	$cfgs->clientName = $Profile->{'clientName'};
	$cfgs->txPower = $Profile->{'txPower'};
	$cfgs->authType = $Profile->{'authType'};
	$cfgs->eapType = $Profile->{'eapType'};
	$cfgs->powerSave = ($Profile->{'powerSave'} & 0xf);
	$cfgs->wepType = $Profile->{'wepType'};
	$cfgs->bitRate = $Profile->{'bitRate'};
	$cfgs->radioMode = $Profile->{'radioMode'};

	$result = ModifyConfig(trim($Profile->{'profileName'}),$cfgs);

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>