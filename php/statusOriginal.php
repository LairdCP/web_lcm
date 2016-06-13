<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

include("../lrd_php_sdk.php");

if( !extension_loaded('lrd_php_sdk') )
{
	print "ERROR: failed to load lrd_php_sdk\n";
	exit();
}

/*Check if the segment of MAC address has any leading zero since printing an integer
with leading zero does not print the zero. e.g: '0e' will be printed as 'e',
so this function converts integer to string and adds a leading zero if it finds one.*/
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
	if($status->cardState == CARDSTATE_AP_MODE){
		print "AP Mode not implemented\n";
		exit();
	}
	if(GetCurrentConfig(NULL, $profileName)==SDCERR_SUCCESS){
		$cconfig = new SDCConfig();
		if (GetConfig($profileName, $cconfig)==SDCERR_SUCCESS){
			if ($cconfig->radioMode == RADIOMODE_ADHOC) {

				for ($x = 0; $x <= MAC_ADDR_SZ - 1; $x++){
					$macAddress[$x]=dechex(uchar_array_getitem($status->client_MAC,$x));
				}
				$MAC = checkLeadingZero($macAddress, MAC_ADDR_SZ);

				for ($x = 0; $x <= IPv4_ADDR_SZ - 1; $x++){
					$ipAddressClient[$x]=uchar_array_getitem($status->client_IP,$x);
				}

				print "Status: Adhoc\n";
				print "Profile name: $status->configName\n";
				print "SSID: $ssidValFinal\n";
				print "Channel: $status->channel\n";
				print "MAC: $MAC\n";

				print "IP: ";
				if($ipAddressClient[0] != 0){
					$IP = implode('.', $ipAddressClient);
					print $IP;
				}
				print "\n";

				print"Tx Power: $status->txPower"." mW\n";
				exit();
			}
		}
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

	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>Status: ";
	switch($status->cardState){
		case CARDSTATE_NOT_INSERTED:
			print  "Card not inserted";
			break;
		case CARDSTATE_NOT_ASSOCIATED:
			print "Not Associated\n";
			break;
		case CARDSTATE_ASSOCIATED:
			print "Associated\n";
			$showRSSI = True;
			break;
		case CARDSTATE_AUTHENTICATED:
			print  "Authenticated";
			$showRSSI = True;
			break;
		case CARDSTATE_FCCTEST:
			print "FCC Test\n";
			break;
		case CARDSTATE_NOT_SDC:
			print "Not SDC\n";
			break;
		case CARDSTATE_DISABLED:
			print "Disabled\n";
			break;
		case CARDSTATE_ERROR:
			print "Error\n";
			break;
		case CARDSTATE_AP_MODE:
			print "AP Mode\n";
			break;
	}
	print "</div>";

	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>Profile Name: $status->configName </div>";

	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>SSID: ";
	if (($ssid->len) || (($status->cardState == CARDSTATE_ASSOCIATED) ||
		    ($status->cardState == CARDSTATE_AUTHENTICATED))){
		print $ssidValFinal;
	}
	print "</div>";

	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>channel: $status->channel </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>RSSI: $status->rssi </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>Device Name: $status->clientName </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>MAC: $MAC </div>";

	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>IP: ";
	if($ipAddress[0] != 0){
		$IP = implode('.', $ipAddress);
		print $IP;
	}
	print "</div>";

	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>AP Name: $status->APName </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>AP MAC: $APMAC </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>AP IP: $APIP </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>Bit Rate: $bitRate"." Mbps </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>Tx Power: $status->txPower"." mW </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>Beacon Period: $status->beaconPeriod"." ms </div>";
	print "<div class='col-xs-6 col-sm-6 placeholder text-left'>DTIM: $status->DTIM </div>";

	print "<div class='col-xs-12 col-sm-12 placeholder'>";
	if ($showRSSI == True){
		$RSSIMeter = $status->rssi + 120;
		if ($status->rssi < -90){ //red
			print "<div class=progress>";
				print "<div class='progress-bar progress-bar-danger' role='progressbar' style='width: $RSSIMeter%'></div>";
			print "</div>";
		} elseif ($status->rssi < -70){ //yellow
			print "<div class=progress>";
				print "<div class='progress-bar progress-bar-warning' role='progressbar' style='width: $RSSIMeter%'></div>";
			print "</div>";
		} else {
			print "<div class=progress>";
				print "<div class='progress-bar progress-bar-success' role='progressbar' style='width: $RSSIMeter%'></div>";
			print "</div>";
		}
	} else{
		print "<div class=progress>";
			print "<div class='progress-bar progress-bar-danger' role='progressbar' style='width: 0%'></div>";
		print "</div>";
	}
	print "</div>";

} else{
	print "<div class='col-xs-6 col-sm-6 placeholder'>Failed to receive status </div>";
}

?>