<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$InterfaceData = json_decode(stripslashes(file_get_contents("php://input")));

	//IPv4
	switch (strtolower($InterfaceData->IPv4->{'state'})){
		case "none":
			$result = LRD_ENI_AutoStartOff("br0");
			if ($result == SDCERR_SUCCESS){
				// Be safe and stop br0
				exec("/usr/sbin/ifrc br0 stop");
				if ($InterfaceData->IPv4->{'previousNAT'}){
					// Clear previous NATs
					$result = LRD_ENI_DisableNat($InterfaceData->IPv4->{'previousNAT'});
				}
				// Put both interfaces in clean state
				exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->IPv4->{'int_1'}) . " restart");
				exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->IPv4->{'int_2'}) . " restart");
			}
			break;
		case "bridge":
			$result = LRD_ENI_EnableInterface("br0");
			if ($result == SDCERR_SUCCESS){
				$result = LRD_ENI_AutoStartOn("br0");
				if ($result == SDCERR_SUCCESS){
					$result = LRD_ENI_SetBridgePorts("br0", $InterfaceData->IPv4->{'int_1'} . " " . $InterfaceData->IPv4->{'int_2'});
					if ($result == SDCERR_SUCCESS){
						if ($InterfaceData->IPv4->{'previousNAT'}){
							// Clear previous NATs
							$result = LRD_ENI_DisableNat($InterfaceData->IPv4->{'previousNAT'});
						}
						// Enable bridge, handles starting of bridged interfaces
						exec("/usr/sbin/ifrc br0 restart", $output, $result);
					}
				}
			}
			break;
		case "nat":
			$result = LRD_ENI_AutoStartOff("br0");
			if ($result == SDCERR_SUCCESS){
				// Be safe and stop br0
				exec("/usr/sbin/ifrc br0 stop");
				$result = LRD_ENI_AddInterface($InterfaceData->IPv4->{'int_2'});
				if ($result == SDCERR_SUCCESS){
					$result = LRD_ENI_EnableInterface($InterfaceData->IPv4->{'int_2'});
					if ($result == SDCERR_SUCCESS){
						$result = LRD_ENI_AutoStartOn($InterfaceData->IPv4->{'int_1'});
						if ($result == SDCERR_SUCCESS){
							$result = LRD_ENI_AutoStartOn($InterfaceData->IPv4->{'int_2'});
							if ($result == SDCERR_SUCCESS){
								if ($InterfaceData->IPv4->{'previousNAT'}){
									// Clear previous NATs
									$result = LRD_ENI_DisableNat($InterfaceData->IPv4->{'previousNAT'});
								}
								$result = LRD_ENI_EnableNat($InterfaceData->IPv4->{'int_2'});
								if ($result == SDCERR_SUCCESS){
									// Make sure int 1/wlan0 is started
									exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->IPv4->{'int_1'}) . " start", $output, $result);
									// Restart int 2 to get desired state
									exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->IPv4->{'int_2'}) . " restart", $output, $result);
								}
							}
						}
					}
				}
			}
			break;
		default:
			$result = SDCERR_FAIL;
	}

	//IPv6
	if ($result == SDCERR_SUCCESS){
		switch (strtolower($InterfaceData->IPv6->{'state'})){
			case "none":
				if ($InterfaceData->IPv6->{'previousNAT'}){
					// Clear previous NATs
					$result = LRD_ENI_DisableNat6($InterfaceData->IPv6->{'previousNAT'});
					// Put interface in clean state
					exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->IPv6->{'int_2'}) . " restart");
				}
				break;
			case "nat":
				$result = LRD_ENI_AddInterface6($InterfaceData->IPv6->{'int_2'});
				if ($result == SDCERR_SUCCESS){
					$result = LRD_ENI_EnableInterface6($InterfaceData->IPv6->{'int_2'});
					if ($result == SDCERR_SUCCESS){
						$result = LRD_ENI_AutoStartOn($InterfaceData->IPv6->{'int_1'});
						if ($result == SDCERR_SUCCESS){
							$result = LRD_ENI_AutoStartOn($InterfaceData->IPv6->{'int_2'});
							if ($result == SDCERR_SUCCESS){
								if ($InterfaceData->IPv6->{'previousNAT'}){
									// Clear previous NATs
									$result = LRD_ENI_DisableNat6($InterfaceData->IPv6->{'previousNAT'});
								}
								$result = LRD_ENI_EnableNat6($InterfaceData->IPv6->{'int_2'});
								// Restart int 2 to get desired state
								exec("/usr/sbin/ifrc " . escapeshellcmd($InterfaceData->IPv6->{'int_2'}) . " restart", $output, $result);
							}
						}
					}
				}
				break;
			default:
				$result = SDCERR_FAIL;
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

?>
