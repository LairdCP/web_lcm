<?php
# Copyright (c) 2017, Laird
# Contact: ews-support@lairdtech.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$Profile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();
	$result = GetConfig(uchr($Profile->{'profileName'}),$cfgs);
	$cfgs->SSID = uchr($Profile->{'SSID'});
	$cfgs->clientName = $Profile->{'clientName'};
	$cfgs->txPower = $Profile->{'txPower'};
	$cfgs->authType = $Profile->{'authType'};
	$cfgs->eapType = $Profile->{'eapType'};
	$cfgs->powerSave = ($Profile->{'powerSave'} & 0xf);
	$cfgs->powerSave = (($Profile->{'pspDelay'} << 16) | (int)($cfgs->powerSave & 0x0000ffff));
	$cfgs->wepType = $Profile->{'wepType'};
	switch ($cfgs->wepType){
		case WEP_OFF:
			$cfgs->eapType = EAP_NONE;
			break;
		case WEP_ON:
			$cfgs->eapType = EAP_NONE;

			function strToHex($key,$string,$length){
				for ($i=0; $i < $length; $i++) {
					uchar_array_setitem($key,$i,ord($string[$i]));
				}
			}

			function setKey($config,$index,$key,$txKey){
				define("KEYLENGTH_NOT_SET", 0);
				define("KEYLENGTH_ASCII_64BIT", 5);
				define("KEYLENGTH_ASCII_128BIT", 13);
				define("KEYLENGTH_HEX_64BIT", 10);
				define("KEYLENGTH_HEX_128BIT",26);

				//Array to hold largest possible key
				$wepKey = new_uchar_array(KEYLENGTH_HEX_128BIT + 1);

				if ($index == $txKey){
					$setTX = 1;
				}

				if ((substr_count($key,"*") == strlen($key)) and (strlen($key))){
					if ($setTX){
						$currentKey = new_uchar_array(KEYLENGTH_HEX_128BIT + 1);
						$wepLen = new_WEPLENp();
						$result = GetWEPKey($config,$index,$wepLen,$currentKey,NULL);
						if ($result == SDCERR_SUCCESS){
							$result = SetWEPKey($config,$index,WEPLENp_value($wepLen),$currentKey,$setTX);
						}
						delete_uchar_array($currentKey);
						delete_WEPLENp($wepLen);

						return $result;
					} else {
						return SDCERR_SUCCESS;
					}
				}

				switch (strlen($key)){
					case KEYLENGTH_NOT_SET:
						$length = WEPLEN_NOT_SET;
						break;
					case KEYLENGTH_ASCII_64BIT:
						$length = WEPLEN_40BIT;
						strToHex($wepKey,$key,KEYLENGTH_ASCII_64BIT);
						break;
					case KEYLENGTH_HEX_64BIT:
						$length = WEPLEN_40BIT;
						$hexToString = pack('H*',$key);
						strToHex($wepKey,$hexToString,KEYLENGTH_ASCII_64BIT);
						break;
					case KEYLENGTH_ASCII_128BIT:
						$length = WEPLEN_128BIT;
						strToHex($wepKey,$key,KEYLENGTH_ASCII_128BIT);
						break;
					case KEYLENGTH_HEX_128BIT:
						$length = WEPLEN_128BIT;
						$hexToString = pack('H*',$key);
						strToHex($wepKey,$hexToString,KEYLENGTH_ASCII_128BIT);
						break;
					default:
						return SDCERR_FAIL;
						break;
				}
				$result = SetWEPKey($config,$index,$length,$wepKey,$setTX);
				delete_uchar_array($wepKey);

				return $result;
			}

			$result = setKey($cfgs,1,$Profile->{'index1'},$Profile->{'wepIndex'});
			$result = setKey($cfgs,2,$Profile->{'index2'},$Profile->{'wepIndex'});
			$result = setKey($cfgs,3,$Profile->{'index3'},$Profile->{'wepIndex'});
			$result = setKey($cfgs,4,$Profile->{'index4'},$Profile->{'wepIndex'});
			break;
		case WPA_PSK:
		case WPA2_PSK:
		case WPA_PSK_AES:
		case WPA2_PSK_TKIP:
			$cfgs->eapType = EAP_NONE;
			$psk = str_repeat(" ",PSK_SZ);
			$result = GetPSK($cfgs,$psk);
			if($result == SDCERR_SUCCESS){
				if ($Profile->{'psk'} != "********"){
					$psk = $Profile->{'psk'};
				}
			}
			$result = SetPSK($cfgs,uchr($psk));
			break;
		case WEP_AUTO:
		case WPA_TKIP:
		case WPA2_AES:
		case CCKM_TKIP:
		case CCKM_AES:
		case WPA_AES:
			$cfgs->eapType = $Profile->{'eapType'};
			switch ($cfgs->eapType){
				case EAP_LEAP:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$result = GetLEAPCred($cfgs,$userName,$passWord);
					if ($result == SDCERR_SUCCESS){
						$userName = $Profile->{'userName'};
						if ($Profile->{'passWord'} != "********"){
							$passWord = $Profile->{'passWord'};
						}
						$result = SetLEAPCred($cfgs,$userName,$passWord);
					}
					break;
				case EAP_EAPFAST:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$PACFilename = str_repeat(" ",CRED_PFILE_SZ);
					$PACPassword = str_repeat(" ",CRED_PFILE_SZ);
					$result = GetEAPFASTCred($cfgs,$userName,$passWord,$PACFilename,$PACPassword);
					if ($result == SDCERR_SUCCESS){
						$userName = $Profile->{'userName'};
						if ($Profile->{'passWord'} != "********"){
							$passWord = $Profile->{'passWord'};
						}
						$PACFilename = $Profile->{'PACFilename'};
						if ($Profile->{'PACPassword'} != "********"){
							$PACPassword = $Profile->{'PACPassword'};
						}
						$result = SetEAPFASTCred($cfgs,$userName,$passWord,$PACFilename,$PACPassword);
					}
					break;
				case EAP_PEAPMSCHAP:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetPEAPMSCHAPCred($cfgs,$userName,$passWord,$certLocation,$caCert);
					if ($result == SDCERR_SUCCESS){
						$userName = $Profile->{'userName'};
						if ($Profile->{'passWord'} != "********"){
							$passWord = $Profile->{'passWord'};
						}
						CERTLOCATIONp_assign($certLocation,CERT_FILE);
						$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
						$caCert = substr_replace($caCertBuf,$Profile->{'CACert'},0,strlen($Profile->{'CACert'}));
						$result = SetPEAPMSCHAPCred($cfgs,$userName,$passWord,CERTLOCATIONp_value($certLocation),$caCert);
					}
					delete_CERTLOCATIONp($certLocation);
					break;
				case EAP_PEAPGTC:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetPEAPGTCCred($cfgs,$userName,$passWord,$certLocation,$caCert);
					if ($result == SDCERR_SUCCESS){
						$userName = $Profile->{'userName'};
						if ($Profile->{'passWord'} != "********"){
							$passWord = $Profile->{'passWord'};
						}
						CERTLOCATIONp_assign($certLocation,CERT_FILE);
						$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
						$caCert = substr_replace($caCertBuf,$Profile->{'CACert'},0,strlen($Profile->{'CACert'}));
						$result = SetPEAPGTCCred($cfgs,$userName,$passWord,CERTLOCATIONp_value($certLocation),$caCert);
					}
					delete_CERTLOCATIONp($certLocation);
					break;
				case EAP_EAPTLS:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$userCert = str_repeat(" ",CRED_CERT_SZ);
					$userCertPassword = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetEAPTLSCred($cfgs,$userName,$userCert,$certLocation,$caCert);
					if($result == SDCERR_SUCCESS){
						if (trim($userName) != trim($Profile->{'userName'})){
							$result = GetUserCertPassword($cfgs,$userCertPassword);
							$resetNewUserCertPassword = true;
						}
						$userName = $Profile->{'userName'};
						$userCert = $Profile->{'userCert'};
						CERTLOCATIONp_assign($certLocation,CERT_FILE);
						$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
						$caCert = substr_replace($caCertBuf,$Profile->{'CACert'},0,strlen($Profile->{'CACert'}));
						$result = SetEAPTLSCred($cfgs,$userName,$userCert,CERTLOCATIONp_value($certLocation),$caCert);
						if($result == SDCERR_SUCCESS){
							if ($Profile->{'userCertPassword'} != "********"){
								$result = SetUserCertPassword($cfgs,$Profile->{'userCertPassword'});
							} else {
								if ($resetNewUserCertPassword){
									$result = SetUserCertPassword($cfgs,$userCertPassword);
								}
							}
						}
					}
					break;
				case EAP_EAPTTLS:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetEAPTTLSCred($cfgs,$userName,$passWord,$certLocation,$caCert);
					if ($result == SDCERR_SUCCESS){
						$userName = $Profile->{'userName'};
						if ($Profile->{'passWord'} != "********"){
							$passWord = $Profile->{'passWord'};
						}
						CERTLOCATIONp_assign($certLocation,CERT_FILE);
						$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
						$caCert = substr_replace($caCertBuf,$Profile->{'CACert'},0,strlen($Profile->{'CACert'}));
						$result = SetEAPTTLSCred($cfgs,$userName,$passWord,CERTLOCATIONp_value($certLocation),$caCert);
					}
					delete_CERTLOCATIONp($certLocation);
					break;
				case EAP_PEAPTLS:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$userCert = str_repeat(" ",CRED_CERT_SZ);
					$userCertPassword = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetPEAPTLSCred($cfgs,$userName,$userCert,$certLocation,$caCert);
					if($result == SDCERR_SUCCESS){
						if (trim($userName) != trim($Profile->{'userName'})){
							$result = GetUserCertPassword($cfgs,$userCertPassword);
							$resetNewUserCertPassword = true;
						}
						$userName = $Profile->{'userName'};
						$userCert = $Profile->{'userCert'};
						CERTLOCATIONp_assign($certLocation,CERT_FILE);
						$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
						$caCert = substr_replace($caCertBuf,$Profile->{'CACert'},0,strlen($Profile->{'CACert'}));
						$result = SetPEAPTLSCred($cfgs,$userName,$userCert,CERTLOCATIONp_value($certLocation),$caCert);
						if($result == SDCERR_SUCCESS){
							if ($Profile->{'userCertPassword'} != "********"){
								$result = SetUserCertPassword($cfgs,$Profile->{'userCertPassword'});
							} else {
								if ($resetNewUserCertPassword){
									$result = SetUserCertPassword($cfgs,$userCertPassword);
								}
							}
						}
					}
					break;
				default:
					$result = SDCERR_FAIL;
					break;
			}
			break;
		default:
			$result = SDCERR_FAIL;
			break;

	}
	$cfgs->bitRate = $Profile->{'bitRate'};
	$cfgs->radioMode = $Profile->{'radioMode'};

	if ($result == SDCERR_SUCCESS){
		$result = ModifyConfig(trim(uchr($Profile->{'profileName'})),$cfgs);
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>
