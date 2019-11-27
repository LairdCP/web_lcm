<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}

	$creds = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult['SDCERR'] = updateCredentials($creds->{'currentUserName'}, $creds->{'currentPassWord'}, trim($creds->{'newUserName'}), trim($creds->{'newPassWord'}));

	echo json_encode($returnedResult);
?>
