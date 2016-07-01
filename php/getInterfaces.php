<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("webLCM.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}

	$autoInterfaces = [];
	$InterfaceList = [];
	$InterfaceState = [];
	$ENABLE = 0;
	$DISABLE = -1;
	$EMPTY = -2;

	function checkLine($line){
		global $ENABLE, $DISABLE, $EMPTY;
		$trimmed = trim($line);
		$pos = strpos($trimmed, '#');
		$line = explode(" ", $trimmed);
		$result = count($line);
		if($pos === 0){
			return $DISABLE;
		} elseif($result <= 1){
			return $EMPTY;
		} else{
			return $ENABLE;
		}
	}

	if(file_exists('/etc/network/interfaces')){
		global $ENABLE, $DISABLE, $EMPTY;
		$handle = fopen("/etc/network/interfaces", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$pos = strpos($line, 'iface');
				if((checkLine($line) == $ENABLE) && ($pos !== FALSE)){
					$strArray = explode(' ',trim($line));
					$Interface = $strArray[1];
					if(file_exists('/sys/class/net/' . $Interface . '/uevent')){
						$stateHandle = fopen('/sys/class/net/' . $Interface . '/uevent', "r");
						if ($stateHandle) {
							while (($line = fgets($stateHandle)) !== false) {
								$stateArray = explode('=',trim($line));
								if ($stateArray[0] == "INTERFACE"){
									$InterfaceState[] = $strArray[1];
								}
							}
						}
					}
					$InterfaceList[$Interface][$strArray[2]] = $strArray[3];
					while ((($line = fgets($handle)) !== false) && (checkLine($line) != $EMPTY)) {
						if(checkLine($line) == $DISABLE){
						}else {
							$strArray = explode(' ',trim($line));
							if ($strArray[0] == 'bridge_ports'){
								$InterfaceList[$Interface]['br_port_1'] = $strArray[1];
								$InterfaceList[$Interface]['br_port_2'] = $strArray[2];
							}else{
								$InterfaceList[$Interface][$strArray[0]] = $strArray[1];
							}
							if ($strArray[0] == 'post-cfg-do'){
								$InterfaceList[$Interface]['post-cfg-do'] = $strArray[2];
							}
							if ($strArray[0] == 'pre-dcfg-do'){
								$InterfaceList[$Interface]['pre-dcfg-do'] = $strArray[2];
							}
						}
					}
				}
			}
			fclose($handle);
			$handle = fopen("/etc/network/interfaces", "r");
			if ($handle) {
				while (($line = fgets($handle)) !== false) {
					$line = trim($line);
					$pos = strpos($line, 'auto');
					if((checkLine($line) == $ENABLE) && ($pos !== FALSE)){
						$autoInterfaces[substr($line,5)] = 1;
					}elseif($pos !== FALSE){
						$autoInterfaces[substr($line,6)] = 0;
					}
				}
				fclose($handle);
				$returnedResult['SDCERR'] = SDCERR_SUCCESS;
			}
		}
	}

	$returnedResult['Interfaces'] = $InterfaceList;
	$returnedResult['AutoInterfaces'] = $autoInterfaces;
	$returnedResult['InterfaceState'] = $InterfaceState;

	echo json_encode($returnedResult);
?>
