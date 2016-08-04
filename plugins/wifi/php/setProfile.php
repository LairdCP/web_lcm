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
	$Profile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();
	$result = GetConfig($Profile->{'profileName'},$cfgs);

	$cfgs->SSID = $Profile->{'SSID'};
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
			strToHex($wepKey1,$Profile->{'index1'});
			strToHex($wepKey2,$Profile->{'index2'});
			strToHex($wepKey3,$Profile->{'index3'});
			strToHex($wepKey4,$Profile->{'index4'});
			$result = SetMultipleWEPKeys($cfgs,$Profile->{'wepIndex'},
				getWepLength($Profile->{'index1'}),$wepKey1,
				getWepLength($Profile->{'index2'}),$wepKey2,
				getWepLength($Profile->{'index3'}),$wepKey3,
				getWepLength($Profile->{'index4'}),$wepKey4);
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
			$psk = str_repeat(" ",PSK_SZ);
			$result = GetPSK($cfgs,$psk);
			if($result == SDCERR_SUCCESS){
				if ($Profile->{'psk'} != "********"){
					$psk = $Profile->{'psk'};
				}
			}
			$result = SetPSK($cfgs,$psk);
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
		$result = ModifyConfig(trim($Profile->{'profileName'}),$cfgs);
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);
?>
