<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$interface = json_decode(stripslashes(file_get_contents("php://input")));

	exec("/usr/sbin/ifrc " . escapeshellcmd($interface->{'interface'}) . " " . escapeshellcmd($interface->{'action'}), $output, $result);

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

?>
