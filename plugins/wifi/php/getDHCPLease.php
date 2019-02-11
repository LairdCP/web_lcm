<?php
# Copyright (c) 2018, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");

	$input = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	//Default to wlan0, update if specified.
	$interface = "wlan0";
	if (isset($input->{'interface'})){
		$interface = uchr($input->{'interface'});
	}

	$DHCPLease = new DHCP_LEASE();
	$result = LRD_WF_GetDHCPIPv4Lease($DHCPLease, $interface);

	if ($result == SDCERR_SUCCESS){
		#NOTE - The DEFAULT_ROUTE struct contains a char interface
		#'interface' is a PHP keyword, swig renames it to 'c_interface'
		$returnedResult['interface'] = $DHCPLease->c_interface;
		$returnedResult['address'] = $DHCPLease->address;
		$returnedResult['subnet_mask'] = $DHCPLease->subnet_mask;
		$returnedResult['routers'] = $DHCPLease->routers;
		$returnedResult['lease_time'] = $DHCPLease->lease_time;
		$returnedResult['dns_servers'] = $DHCPLease->dns_servers;
		$returnedResult['dhcp_server'] = $DHCPLease->dhcp_server;
		$returnedResult['domain_name'] = $DHCPLease->domain_name;
		$returnedResult['renew'] = $DHCPLease->renew;
		$returnedResult['rebind'] = $DHCPLease->rebind;
		$returnedResult['expire'] = $DHCPLease->expire;
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>