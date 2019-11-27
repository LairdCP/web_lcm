<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$interface = json_decode(stripslashes(file_get_contents("php://input")));

	exec("/usr/sbin/ifrc " . escapeshellcmd($interface->{'interface'}) . " " . escapeshellcmd($interface->{'action'}), $output, $result);

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

?>
