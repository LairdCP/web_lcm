<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	header("Content-Type: application/json");
	$InterfaceData = json_decode(stripslashes(file_get_contents("php://input")));

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
	];

	switch (strtolower($InterfaceData->{'state'})){
		case "none":
			$result = LRD_ENI_DisableInterface("br0");
			if ($result == SDCERR_SUCCESS){
				$result = LRD_ENI_AutoStartOff("br0");
				if ($result == SDCERR_SUCCESS){
					exec("/usr/sbin/ifrc br0 stop");
					if ($InterfaceData->{'previousNAT'}){
						exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->{'int_2'}) . " stop");
						$result = LRD_ENI_DisableNat($InterfaceData->{'int_2'});
					}
				}
			}
			break;
		case "bridge":
			$result = LRD_ENI_EnableInterface("br0");
			if ($result == SDCERR_SUCCESS){
				$result = LRD_ENI_AutoStartOn("br0");
				if ($result == SDCERR_SUCCESS){
					$result = LRD_ENI_SetBridgePorts("br0", $InterfaceData->{'int_1'} . " " . $InterfaceData->{'int_2'});
					if ($result == SDCERR_SUCCESS){
						if ($InterfaceData->{'previousNAT'}){
							$result = LRD_ENI_DisableNat($InterfaceData->{'int_2'});
							exec("/usr/sbin/ifrc br0 restart", $output, $result);
						}
					}
				}
			}
			break;
		case "nat":
			$result = LRD_ENI_DisableInterface("br0");
			if ($result == SDCERR_SUCCESS){
				$result = LRD_ENI_AutoStartOff("br0");
				if ($result == SDCERR_SUCCESS){
					$result = LRD_ENI_AutoStartOn($InterfaceData->{'int_1'});
					if ($result == SDCERR_SUCCESS){
						$result = LRD_ENI_AutoStartOn($InterfaceData->{'int_2'});
						if ($result == SDCERR_SUCCESS){
							$result = LRD_ENI_EnableNat($InterfaceData->{'int_2'});
							exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->{'int_2'}) . " restart", $output, $result);
						}
					}
				}
			}
			break;
		default:
			$result = SDCERR_FAIL;
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

?>
