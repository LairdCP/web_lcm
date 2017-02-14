<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$interface = json_decode(stripslashes(file_get_contents("php://input")));

	if (isset($interface->{'interfaceName'})){
		if (isset($interface->{'option'})){
			switch($interface->{'option'}){
				case "add":
					$result = LRD_ENI_AddInterface($interface->{'interfaceName'});
					break;
				case "remove":
					$result = LRD_ENI_RemoveInterface($interface->{'interfaceName'});
					break;
			}
			$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);
			echo json_encode($returnedResult);
			return;
		}

		if (isset($interface->{'auto'})){
			if ($interface->{'auto'} == "no"){
				$result = LRD_ENI_AutoStartOff($interface->{'interfaceName'});
			}elseif($interface->{'auto'} == "yes"){
				$result = LRD_ENI_AutoStartOn($interface->{'interfaceName'});
			}
		}

		//IPv4
		if ($result == SDCERR_SUCCESS && isset($interface->IPv4->{'state'})){
			if ($interface->IPv4->{'state'} == '1'){
				$result = LRD_ENI_AddInterface($interface->{'interfaceName'});
				$result = LRD_ENI_EnableInterface($interface->{'interfaceName'});
			}
			else {
				$result = LRD_ENI_AddInterface($interface->{'interfaceName'});
				$result = LRD_ENI_DisableInterface($interface->{'interfaceName'});
			}
		}

		if ($result == SDCERR_SUCCESS && isset($interface->IPv4->{'method'})){
			$result = LRD_ENI_SetMethod($interface->{'interfaceName'},$interface->IPv4->{'method'});
		}

		if ($result == SDCERR_SUCCESS && isset($interface->IPv4->{'address'})){
			$result == LRD_ENI_SetAddress($interface->{'interfaceName'},$interface->IPv4->{'address'});
		}

		if ($result == SDCERR_SUCCESS && isset($interface->IPv4->{'netmask'})){
			$result == LRD_ENI_SetNetmask($interface->{'interfaceName'},$interface->IPv4->{'netmask'});
		}

		if ($result == SDCERR_SUCCESS && isset($interface->IPv4->{'gateway'})){
			$result == LRD_ENI_SetGateway($interface->{'interfaceName'},$interface->IPv4->{'gateway'});
		}

		if ($result == SDCERR_SUCCESS && isset($interface->IPv4->{'broadcast'})){
			$result == LRD_ENI_SetBroadcastAddress($interface->{'interfaceName'},$interface->IPv4->{'broadcast'});
		}

		if ($result == SDCERR_SUCCESS && (isset($interface->IPv4->{'nameserver_1'}) || isset($interface->IPv4->{'nameserver_2'}))){
			$result == LRD_ENI_SetNameserver($interface->{'interfaceName'},"");
			if ($result == SDCERR_SUCCESS){
				$nameserver = $interface->IPv4->{'nameserver_1'} . " " . $interface->IPv4->{'nameserver_2'};
				$result == LRD_ENI_SetNameserver($interface->{'interfaceName'},$nameserver);
			}
		}

		//IPv6
		if ($interface->{'interfaceName'} != "br0"){
			if ($result == SDCERR_SUCCESS && isset($interface->IPv6->{'state'})){
				if ($interface->IPv6->{'state'} == '1'){
					$result = LRD_ENI_AddInterface6($interface->{'interfaceName'});
					$result = LRD_ENI_EnableInterface6($interface->{'interfaceName'});
				}
				else {
					$result = LRD_ENI_AddInterface6($interface->{'interfaceName'});
					$result = LRD_ENI_DisableInterface6($interface->{'interfaceName'});
				}
			}

			if ($result == SDCERR_SUCCESS && isset($interface->IPv6->{'method'})){
				$result = LRD_ENI_SetMethod6($interface->{'interfaceName'},$interface->IPv6->{'method'});
			}

			if ($result == SDCERR_SUCCESS && isset($interface->IPv6->{'address'})){
				$result == LRD_ENI_SetAddress6($interface->{'interfaceName'},$interface->IPv6->{'address'});
			}

			if ($result == SDCERR_SUCCESS && isset($interface->IPv6->{'netmask'})){
				$result == LRD_ENI_SetNetmask6($interface->{'interfaceName'},$interface->IPv6->{'netmask'});
			}

			if ($result == SDCERR_SUCCESS && isset($interface->IPv6->{'gateway'})){
				$result == LRD_ENI_SetGateway6($interface->{'interfaceName'},$interface->IPv6->{'gateway'});
			}

			if ($result == SDCERR_SUCCESS && (isset($interface->IPv6->{'nameserver_1'}) || isset($interface->IPv6->{'nameserver_2'}))){
				$result == LRD_ENI_SetNameserver6($interface->{'interfaceName'},"");
				if ($result == SDCERR_SUCCESS){
					$nameserver = $interface->IPv6->{'nameserver_1'} . " " . $interface->IPv6->{'nameserver_2'};
					$result == LRD_ENI_SetNameserver6($interface->{'interfaceName'},$nameserver);
				}
			}

			if ($result == SDCERR_SUCCESS && isset($interface->IPv6->{'dhcp'})){
				$result == LRD_ENI_SetDhcp6($interface->{'interfaceName'},$interface->IPv6->{'dhcp'});
			}
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

?>
