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

	$newProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();

	$result = CreateConfig($cfgs);

	if ($result == SDCERR_SUCCESS){
		if (isset($newProfile->{'profileName'})){
			$cfgs->configName = uchr($newProfile->{'profileName'});
		}
		if (isset($newProfile->{'SSID'})){
			$cfgs->SSID = uchr($newProfile->{'SSID'});
		}
		if (isset($newProfile->{'clientName'})){
			$cfgs->clientName = $newProfile->{'clientName'};
		}
		if (isset($newProfile->{'txPower'})){
			$cfgs->txPower = $newProfile->{'txPower'};
		}
		if (isset($newProfile->{'authType'})){
			$cfgs->authType = $newProfile->{'authType'};
		}
		if (isset($newProfile->{'powerSave'})){
			$cfgs->powerSave = $newProfile->{'powerSave'};
			if (isset($newProfile->{'pspDelay'})){
				$cfgs->powerSave = (($newProfile->{'pspDelay'} << 16) | (int)($cfgs->powerSave & 0x0000ffff));
			}
		}
		if (isset($newProfile->{'wepType'})){
			if(!is_int($newProfile->{'wepType'})){
				$wepStringToInt = array(
					"WEP_OFF" => WEP_OFF,
					"WEP_ON" => WEP_ON,
					"WEP_AUTO" => WEP_AUTO,
					"WPA_PSK" => WPA_PSK,
					"WPA_TKIP" => WPA_TKIP,
					"WPA2_PSK" => WPA2_PSK,
					"WPA2_AES" => WPA2_AES,
					"CCKM_TKIP" => CCKM_TKIP,
					"WEP_CKIP" => WEP_CKIP,
					"WEP_AUTO_CKIP" => WEP_AUTO_CKIP,
					"CCKM_AES" => CCKM_AES,
					"WPA_PSK_AES" => WPA_PSK_AES,
					"WPA_AES" => WPA_AES,
					"WPA2_PSK_TKIP" => WPA2_PSK_TKIP,
					"WPA2_TKIP" => WPA2_TKIP,
					"WAPI_PSK" => WAPI_PSK,
					"WAPI_CERT" => WAPI_CERT
				);
				syslog(LOG_WARNING, $wepStringToInt[$newProfile->{'wepType'}]);
				$cfgs->wepType = $wepStringToInt[$newProfile->{'wepType'}];
			}else{
				$cfgs->wepType = $newProfile->{'wepType'};
			}
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

					$result = setKey($cfgs,1,$newProfile->{'index1'},$newProfile->{'wepIndex'});
					$result = setKey($cfgs,2,$newProfile->{'index2'},$newProfile->{'wepIndex'});
					$result = setKey($cfgs,3,$newProfile->{'index3'},$newProfile->{'wepIndex'});
					$result = setKey($cfgs,4,$newProfile->{'index4'},$newProfile->{'wepIndex'});
					break;
				case WPA_PSK:
				case WPA2_PSK:
				case WPA_PSK_AES:
				case WPA2_PSK_TKIP:
					$cfgs->eapType = EAP_NONE;
					$result = SetPSK($cfgs,uchr($newProfile->{'psk'}));
					break;
				case WEP_AUTO:
				case WPA_TKIP:
				case WPA2_AES:
				case CCKM_TKIP:
				case CCKM_AES:
				case WPA_AES:
					$cfgs->eapType = $newProfile->{'eapType'};
					switch ($cfgs->eapType){
						case EAP_LEAP:
							$result = SetLEAPCred($cfgs,$newProfile->{'userName'},$newProfile->{'passWord'});
							break;
						case EAP_EAPFAST:
							SetEAPFASTCred($cfgs,$newProfile->{'userName'},$newProfile->{'passWord'},$newProfile->{'PACFilename'},$newProfile->{'PACPassword'});
							break;
						case EAP_PEAPMSCHAP:
							$certLocation = new_CERTLOCATIONp();
							CERTLOCATIONp_assign($certLocation,CERT_FILE);
							$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
							$caCert = substr_replace($caCertBuf,$newProfile->{'CACert'},0,strlen(trim($newProfile->{'CACert'})));
							$result = SetPEAPMSCHAPCred($cfgs,$newProfile->{'userName'},$newProfile->{'passWord'},CERTLOCATIONp_value($certLocation),$caCert);
							delete_CERTLOCATIONp($certLocation);
							break;
						case EAP_PEAPGTC:
							$certLocation = new_CERTLOCATIONp();
							CERTLOCATIONp_assign($certLocation,CERT_FILE);
							$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
							$caCert = substr_replace($caCertBuf,$newProfile->{'CACert'},0,strlen(trim($newProfile->{'CACert'})));
							$result = SetPEAPGTCCred($cfgs,$newProfile->{'userName'},$newProfile->{'passWord'},CERTLOCATIONp_value($certLocation),$caCert);
							delete_CERTLOCATIONp($certLocation);
							break;
						case EAP_EAPTLS:
							$certLocation = new_CERTLOCATIONp();
							CERTLOCATIONp_assign($certLocation,CERT_FILE);
							$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
							$caCert = substr_replace($caCertBuf,$newProfile->{'CACert'},0,strlen(trim($newProfile->{'CACert'})));
							$result = SetEAPTLSCred($cfgs,$newProfile->{'userName'},$newProfile->{'userCert'},CERTLOCATIONp_value($certLocation),$caCert);
							if ($result == SDCERR_SUCCESS){
								$result = SetUserCertPassword($cfgs,$newProfile->{'userCertPassword'});
							}
							delete_CERTLOCATIONp($certLocation);
							break;
						case EAP_EAPTTLS:
							$certLocation = new_CERTLOCATIONp();
							CERTLOCATIONp_assign($certLocation,CERT_FILE);
							$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
							$caCert = substr_replace($caCertBuf,$newProfile->{'CACert'},0,strlen(trim($newProfile->{'CACert'})));
							$result = SetEAPTTLSCred($cfgs,$newProfile->{'userName'},$newProfile->{'passWord'},CERTLOCATIONp_value($certLocation),$caCert);
							delete_CERTLOCATIONp($certLocation);
							break;
						case EAP_PEAPTLS:
							$certLocation = new_CERTLOCATIONp();
							CERTLOCATIONp_assign($certLocation,CERT_FILE);
							$caCertBuf = str_repeat("\0",CRED_CERT_SZ);
							$caCert = substr_replace($caCertBuf,$newProfile->{'CACert'},0,strlen(trim($newProfile->{'CACert'})));
							$result = SetPEAPTLSCred($cfgs,$newProfile->{'userName'},$newProfile->{'userCert'},CERTLOCATIONp_value($certLocation),$caCert);
							if ($result == SDCERR_SUCCESS){
								$result = SetUserCertPassword($cfgs,$newProfile->{'userCertPassword'});
							}
							delete_CERTLOCATIONp($certLocation);
							break;
						default:
							$cfgs->eapType = EAP_PEAPMSCHAP;
							break;
					}
					break;
				default:
					$result = SDCERR_FAIL;
					break;

			}
		}
		if (isset($newProfile->{'bitRate'})){
			$cfgs->bitRate = $newProfile->{'bitRate'};
		}
		if (isset($newProfile->{'radioMode'})){
			$cfgs->radioMode = $newProfile->{'radioMode'};
		}
		if ($result == SDCERR_SUCCESS){
			$result = AddConfig($cfgs);
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>
