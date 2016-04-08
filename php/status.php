<?php
include("../lrd_php_sdk.php");

if(!extension_loaded('lrd_php_sdk')){
	syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
}

function checkLeadingZero($mac, $len){
	for($x = 0; $x <= MAC_ADDR_SZ - 1; $x++){
		if($mac[$x] < dechex(16)){
			$macChar[$x] = strval(0) . strval($mac[$x]);
		}else{
			$macChar[$x]= strval($mac[$x]);
		}
	}
	$MAC = implode(':', $macChar);
	return $MAC;
}

$status = new CF10G_STATUS();
$rval = GetCurrentStatus( $status );

$profileName = str_repeat(" ",CONFIG_NAME_SZ);

if($rval == SDCERR_SUCCESS){
	$ssid = new LRD_WF_SSID();
	LRD_WF_GetSSID($ssid);
	if ($ssid->len){
		for ($x = 0; $x <= ($ssid->len); $x++){
			$ssidVal[$x]= chr(uchar_array_getitem($ssid->val,$x));
		}
		$ssidValFinal = implode('', $ssidVal);
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
	}

	$CF10G_STATUS = [
		'cardState' => $status->cardState,
		'configName' => $status->configName,
		'client_MAC' => $MAC,
		'client_IP' => $IP,
		'clientName' => $status->clientName,
		'AP_MAC' => $APMAC,
		'AP_IP' => $APIP,
		'APName' => $status->APName,
		'eapType' => "",
		'channel' => $status->channel,
		'rssi' => $status->rssi,
		'bitRate' => $bitRate,
		'txPower' => $status->txPower,
		'driverVersion' => "",
		'radioType' => "",
		'DTIM' => $status->DTIM,
		'beaconPeriod' => $status->beaconPeriod,
		'beaconsReceived' => "",
		'ssid' => $ssidValFinal, //Custom item not in CF10G_STATUS structure.
		'currentRadioMode' => $cconfig->radioMode, //Custom item not in CF10G_STATUS structure.
	];

	echo json_encode($CF10G_STATUS);

} else{
	print "Failed to receive status</div>";
}

?>