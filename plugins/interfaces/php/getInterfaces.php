<?php
# Copyright (c) 2016, Laird Connectivity
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS) {
		echo json_encode($returnedResult);
		return;
	}

	$returnedResult['Interfaces'] = [];
	$returnedResult['AutoInterfaces'] = [];
	$returnedResult['InterfaceState'] = [];

	$autoInterfaces = &$returnedResult['AutoInterfaces'];
	$InterfaceList = &$returnedResult['Interfaces'];
	$InterfaceState = &$returnedResult['InterfaceState'];

	$state = 'none';

	$lines = file('/etc/network/interfaces');
	if (!$lines) {
		$lines = [];
	}
	foreach ($lines as $line) {
		$line = trim($line);

		// Empty line, drop inet state and skip further processing
		if (empty($line)) {
			$state = 'none';
			continue;
		}

		// Comment line, skip further processing
		if (str_starts_with($line, '#')) {
			continue;
		}

		// Split space seprated line
		$strArray = preg_split('/\s+/', $line);

		// Line has only 1 token, skip further processing
		if (count($strArray) <= 1) {
			continue;
		}

		switch ($state)
		{
		case 'none':
			// System configuration processing
			switch ($strArray[0])
			{
			case 'auto':
				$autoInterfaces[$strArray[1]] = 1;
				break;

			case 'iface':
				$Interface = $strArray[1];

				if (is_readable('/sys/class/net/' . $Interface . '/uevent')) {
					$slines = file('/sys/class/net/' . $Interface . '/uevent');
					foreach ($slines as $sline) {
						$stateArray = explode('=', trim($sline));
						if ($stateArray[0] == "INTERFACE") {
							$InterfaceState[] = $strArray[1];
						}
					}
				}

				if (!isset($autoInterfaces[$Interface])) {
					$autoInterfaces[$Interface] = 0;
				}

				if ($strArray[2] == 'inet') {
					$IpType = "IPv4";
					$state = 'inet';
					$InterfaceList[$Interface]["IPv4"]["state"] = "1";
					$InterfaceList[$Interface]["IPv4"][$strArray[2]] = $strArray[3];
				} elseif (!isset($InterfaceList[$Interface]["IPv4"]["state"])) {
					$InterfaceList[$Interface]["IPv4"]["state"] = "0";
				}

				if ($strArray[2] == 'inet6') {
					$IpType = "IPv6";
					$state = 'inet6';
					$InterfaceList[$Interface]["IPv6"]["state"] = "1";
					$InterfaceList[$Interface]["IPv6"][$strArray[2]] = $strArray[3];
				} elseif (!isset($InterfaceList[$Interface]["IPv6"]["state"])) {
					$InterfaceList[$Interface]["IPv6"]["state"] = "0";
				}
				break;
			}
			break;

		case 'inet':
		case 'inet6':
			// Specific interface configuration processing
			switch ($strArray[0])
			{
			case 'bridge_ports':
				$InterfaceList[$Interface][$IpType]['br_port_1'] = $strArray[1];
				$InterfaceList[$Interface][$IpType]['br_port_2'] = $strArray[2];
				break;

			case 'nameserver':
				if (empty($InterfaceList[$Interface][$IpType]['nameserver_1'])) {
					$InterfaceList[$Interface][$IpType]['nameserver_1'] = $strArray[1];
				} else {
					$InterfaceList[$Interface][$IpType]['nameserver_2'] = $strArray[1];
				}
				break;

			default:
				$InterfaceList[$Interface][$IpType][$strArray[0]] = $strArray[1];
				break;
			}
			break;
		}
	}

	unset($lines);

	$returnedResult['SDCERR'] = count($InterfaceList) ? SDCERR_SUCCESS : SDCERR_FAIL;

	echo json_encode($returnedResult);
?>
