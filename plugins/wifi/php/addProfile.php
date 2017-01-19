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

	$newProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();

	$result = CreateConfig($cfgs);

	if ($result == SDCERR_SUCCESS){
		if (isset($newProfile->{'profileName'})){
			$cfgs->configName = $newProfile->{'profileName'};
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
					function getWepLength($wepKey){
						if (strlen($wepKey) == 5){
							return WEPLEN_40BIT;
						} else if (strlen($wepKey) == 13){
							return WEPLEN_128BIT;
						}
						return WEPLEN_NOT_SET;
					}
					function strToHex($key,$string)
					{
						for ($i=0; $i < 27; $i++)
						{
							uchar_array_setitem($key,$i,dechex(ord($string[$i])));
						}
					}
					$wepKey1 = new_uchar_array(27);
					$wepKey2 = new_uchar_array(27);
					$wepKey3 = new_uchar_array(27);
					$wepKey4 = new_uchar_array(27);
					strToHex($wepKey1,$newProfile->{'index1'});
					strToHex($wepKey2,$newProfile->{'index2'});
					strToHex($wepKey3,$newProfile->{'index3'});
					strToHex($wepKey4,$newProfile->{'index4'});
					$result = SetMultipleWEPKeys($cfgs,$newProfile->{'wepIndex'},
						getWepLength($newProfile->{'index1'}),$wepKey1,
						getWepLength($newProfile->{'index2'}),$wepKey2,
						getWepLength($newProfile->{'index3'}),$wepKey3,
						getWepLength($newProfile->{'index4'}),$wepKey4);
					delete_uchar_array($wepKey1);
					delete_uchar_array($wepKey2);
					delete_uchar_array($wepKey3);
					delete_uchar_array($wepKey4);
					break;
				case WPA_PSK:
				case WPA2_PSK:
				case WPA_PSK_AES:
				case WPA2_PSK_TKIP:
					$cfgs->eapType = EAP_NONE;
					$result = SetPSK($cfgs,$newProfile->{'psk'});
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
