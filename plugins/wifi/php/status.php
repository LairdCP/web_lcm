<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(false);

	function checkLeadingZero($mac, $len){
		for($x = 0; $x <= MAC_ADDR_SZ - 1; $x++){
			if($mac[$x] < dechex(16)){
				$macChar[$x] = strval(0) . strval($mac[$x]);
			}else{
				$macChar[$x]= strval($mac[$x]);
			}
			if (strlen($macChar[$x]) == 1){
				$macChar[$x] = "0" . $macChar[$x];
			}
		}
		$MAC = implode(':', $macChar);
		return $MAC;
	}

	$status = new CF10G_STATUS();
	$result = GetCurrentStatus( $status );

	$profileName = str_repeat(" ",CONFIG_NAME_SZ);

	if($result == SDCERR_SUCCESS){
		$ssid = new LRD_WF_SSID();
		LRD_WF_GetSSID($ssid);
		if ($ssid->len){
			for ($x = 0; $x <= ($ssid->len); $x++){
				$SSID_Array[$x] = uchar_array_getitem($ssid->val,$x);
			}
		}else{
			$SSID_Array = [];
		}

		if(GetCurrentConfig(NULL, $profileName)==SDCERR_SUCCESS){
			$cconfig = new SDCConfig();
			GetConfig($profileName, $cconfig);
		}

		for ($x = 0; $x <= MAC_ADDR_SZ - 1; $x++){
			$macAddress[$x]=dechex(uchar_array_getitem($status->client_MAC,$x));
		}
		$MAC = checkLeadingZero($macAddress, MAC_ADDR_SZ);

		for ($x = 0; $x <= IPv4_ADDR_SZ - 1; $x++){
			$ipAddress[$x]=uchar_array_getitem($status->client_IP,$x);
		}

		for ($x = 0; $x <= MAC_ADDR_SZ - 1; $x++){
			$APmacAddress[$x]=dechex(uchar_array_getitem($status->AP_MAC,$x));
		}
		$APMAC = checkLeadingZero($APmacAddress, MAC_ADDR_SZ);

		for ($x = 0; $x <= IPv4_ADDR_SZ - 1; $x++){
			$APipAddress[$x]=uchar_array_getitem($status->AP_IP,$x);
		}
		$APIP = implode('.', $APipAddress);

		$bitRate = $status->bitRate/2;

		if($ipAddress[0] != 0){
			$IP = implode('.', $ipAddress);
		}else{
			$IP = '';
		}

		$resultp = new_SDCERRp();
		$numElements = new_size_tp();
		$numEntries = 0;
		size_tp_assign($numElements,$numEntries);
		$list = lrd_php_sdk::new_LRD_WF_PHP_GetIpV6Address($numElements,$resultp);
		$max_insufficient_rtns = 5;
		while (SDCERRp_value($resultp)==SDCERR_INSUFFICIENT_MEMORY && $max_insufficient_rtns--)
		{
			lrd_php_sdk::delete_LRD_WF_PHP_GetIpV6Address($list);
			$numEntries += 1;
			size_tp_assign($numElements,$numEntries);
			$list = lrd_php_sdk::new_LRD_WF_PHP_GetIpV6Address($numElements,$resultp);
		}
		if (SDCERRp_value($resultp) == SDCERR_SUCCESS){
			for($h = 0; $h < size_tp_value($numElements); $h++){
				unset($item);
				$item = lrd_php_sdk::LRD_WF_PHP_GetIpV6Address_get($list,$h);
				$IPv6Array[$h] = $item;
			}
			$IPv6Array['size'] = $numEntries;
		}

		lrd_php_sdk::delete_LRD_WF_PHP_GetIpV6Address($list);
		delete_size_tp($numElements);
		delete_SDCERRp($resultp);

		$returnedResult['SDCERR'] = $result;
		$returnedResult['cardState'] = $status->cardState;
		$returnedResult['configName'] = $status->configName;
		$returnedResult['client_MAC'] = $MAC;
		$returnedResult['client_IP'] = $IP;
		$returnedResult['IPv6'] = $IPv6Array;
		$returnedResult['clientName'] = $status->clientName;
		$returnedResult['AP_MAC'] = $APMAC;
		$returnedResult['AP_IP'] = $APIP;
		$returnedResult['APName'] = $status->APName;
		$returnedResult['channel'] = $status->channel;
		$returnedResult['rssi'] = $status->rssi;
		$returnedResult['bitRate'] = $bitRate;
		$returnedResult['txPower'] = $status->txPower;
		$returnedResult['DTIM'] = $status->DTIM;
		$returnedResult['beaconPeriod'] = $status->beaconPeriod;
		$returnedResult['ssid'] = $SSID_Array; //Custom item not in CF10G_STATUS structure.
		$returnedResult['currentRadioMode'] = $cconfig->radioMode; //Custom item not in CF10G_STATUS structure.

		echo json_encode($returnedResult);

	} else{
		$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);
		$returnedResult['cardState'] = $status->cardState;
		echo json_encode($returnedResult);
	}

?>
