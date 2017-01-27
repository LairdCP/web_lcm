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

	$priority = array(
				"WAPI_CERT" => 65536,
				"WAPI_PSK" => 32768,
				"WPA2_AES" => 64,
				"CCKM_AES" => 1024,
				"WPA_AES" => 4096,
				"WPA2_PSK" => 32,
				"WPA_PSK_AES" => 2048,
				"WPA2_TKIP" => 16384,
				"CCKM_TKIP" => 128,
				"WPA_TKIP" => 16,
				"WPA2_PSK_TKIP" => 8192,
				"WPA_PSK" => 8,
				"WEP_ON" => 2,
				"WEP_AUTO" => 4,
				"WEP_OFF" => 1,
				"WEP_AUTO_CKIP" => 512,
				"WEP_CKIP" => 256
			);
	$scanList = array();
	$result = new_SDCERRp();
	$numElements = new_intp();
	$numEntries = 150;
	intp_assign($numElements,$numEntries);
	$list = lrd_php_sdk::new_LRD_WF_PHP_GetBSSIDList($numElements,$result);
	if (SDCERRp_value($result) == SDCERR_INSUFFICIENT_MEMORY){ //  make one more try at scan
		lrd_php_sdk::delete_LRD_WF_PHP_GetBSSIDList($list);
		$numEntries += 25;
		intp_assign($numElements,$numEntries);
		$list = lrd_php_sdk::new_LRD_WF_PHP_GetBSSIDList($numElements,$result);
		if (SDCERRp_value($result) == SDCERR_INSUFFICIENT_MEMORY){
			SDCERRp_assign($result,SDCERR_SUCCESS); // override SDCERR_INSUFFICIENT_MEMORY, not trying again, list what we've got
		}
	}
	if (SDCERRp_value($result) == SDCERR_SUCCESS){
		for($h = 0; $h < intp_value($numElements); $h++){
			$scanListItem = array();
			unset($item);
			$item = lrd_php_sdk::LRD_WF_PHP_GetBSSIDList_get($list,$h);
			if ((1 <= $item->channel) && ($item->channel <= 165)){
				unset($SSID_Array);
				if ($item->ssid->len){
					for ($x = 0; $x <= ($item->ssid->len); $x++){
						$SSID_Array[$x] = uchar_array_getitem($item->ssid->val,$x);
					}
					$scanListItem["SSID"] = $SSID_Array;
				}
				unset($bssidVal);
				for ($y = 0; $y < LRD_WF_MAC_ADDR_LEN; $y++){
					$bssidVal[$y] = dechex(uchar_array_getitem($item->bssidMac,$y));
					if (strlen($bssidVal[$y]) == 1){
						$bssidVal[$y] = "0" . $bssidVal[$y];
					}
				}
				unset($ssidValFinal);
				$bssidValFinal = implode(':', $bssidVal);
				$scanListItem["BSSID"] = $bssidValFinal;
				$scanListItem["channel"] = $item->channel;
				$scanListItem["RSSI"] = $item->RSSI/100;
				$scanListItem["BSSType"] = ($item->bssType==INFRASTRUCTURE?"Infrastructure":"Adhoc");
				$scanListItem["security"] = "";
				foreach ($priority as $key => $value) {
					if (intval($item->securityMask) & $value){
						$scanListItem["security"][] = $key;
					}
				}
				array_push($scanList, $scanListItem);
			}
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, SDCERRp_value($result));
	$returnedResult['scanList'] = $scanList;

	echo json_encode($returnedResult);

	lrd_php_sdk::delete_LRD_WF_PHP_GetBSSIDList($list);
	delete_intp($numElements);
	delete_SDCERRp($result);
?>
