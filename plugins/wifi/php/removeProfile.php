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
	$oldProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$cconfig = new SDCConfig();

	$result = GetConfig($oldProfile->{'profileName'}, $cconfig);
	if ($result == SDCERR_SUCCESS){
		$result = DeleteConfig(trim($oldProfile->{'profileName'}));
	}

	$returnedResult = [
		'SDCERR' => $result,
	];

	echo json_encode($returnedResult);

?>
