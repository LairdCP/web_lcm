<?php
# Copyright (c) 2018, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	//Interface can be null, first default route found will be returned
	$interface = null;
	$DefaultRoute = new DEFAULT_ROUTE();
	$result = LRD_WF_GetDefaultRoute($DefaultRoute, LRD_ROUTE_FILE, $interface);

	if ($result == SDCERR_SUCCESS){
		#NOTE - The DEFAULT_ROUTE struct contains a char interface
		#'interface' is a PHP keyword, swig renames it to 'c_interface'
		$returnedResult['interface'] = $DefaultRoute->c_interface;
		$returnedResult['destination'] = $DefaultRoute->destination;
		$returnedResult['gateway'] = $DefaultRoute->gateway;
		$returnedResult['flags'] = $DefaultRoute->flags;
		$returnedResult['metric'] = $DefaultRoute->Metric;
		$returnedResult['subnet_mask'] = $DefaultRoute->subnet_mask;
		$returnedResult['mtu'] = $DefaultRoute->mtu;
		$returnedResult['window'] = $DefaultRoute->window;
		$returnedResult['irtt'] = $DefaultRoute->irtt;
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>