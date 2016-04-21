<?php
	include("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$newProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();

	$result = CreateConfig($cfgs);

	if ($result == SDCERR_SUCCESS){
		$cfgs->configName = $newProfile->{'profileName'};
		$cfgs->SSID = $newProfile->{'SSID'};
		$cfgs->clientName = $newProfile->{'clientName'};
		$cfgs->txPower = $newProfile->{'txPower'};
		$cfgs->authType = $newProfile->{'authType'};
		$cfgs->eapType = $newProfile->{'eapType'};
		$cfgs->powerSave = $newProfile->{'powerSave'};
		$cfgs->wepType = $newProfile->{'wepType'};
		$cfgs->bitRate = $newProfile->{'bitRate'};
		$cfgs->radioMode = $newProfile->{'radioMode'};
		$result = AddConfig($cfgs);
	}

	$returnedResult = [
		'SDCERR' => $result,
	];

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>
