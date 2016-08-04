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
	$oldProfile = json_decode(stripslashes(file_get_contents("php://input")));

	$cfgs = new SDCConfig();
	$result = GetConfig($oldProfile->{'profileName'},$cfgs);

	if($result == SDCERR_SUCCESS){
		$returnedResult['configName'] = $cfgs->configName;
		$returnedResult['SSID'] = $cfgs->SSID;
		$returnedResult['clientName'] = $cfgs->clientName;
		$returnedResult['txPower'] = $cfgs->txPower;
		$returnedResult['authType'] = $cfgs->authType;
		$returnedResult['eapType'] = $cfgs->eapType;
		$returnedResult['powerSave'] = ($cfgs->powerSave & 0xf);
		$returnedResult['wepType'] = $cfgs->wepType;
		$returnedResult['bitRate'] = $cfgs->bitRate;
		$returnedResult['radioMode'] = $cfgs->radioMode;
		$pspDelay = (($cfgs->powerSave & 0xffff0000) >>16);
		if ($pspDelay == 0){
			$pspDelay = DEFAULT_PSP_DELAY;
		}
		$returnedResult['pspDelay'] = $pspDelay;//Non default struct SDCConfig

		//Security Credentials
		if ($cfgs->wepType == WEP_ON || $cfgs->wepType == WEP_CKIP){
			$wepIndex = new_intp();
			$wepLen = new_WEPLENp();
			$wepLen1 = new_WEPLENp();
			$wepLen2 = new_WEPLENp();
			$wepLen3 = new_WEPLENp();
			$wepLen4 = new_WEPLENp();

			$wepKey1 = new_uchar_array(27);
			$wepKey2 = new_uchar_array(27);
			$wepKey3 = new_uchar_array(27);
			$wepKey4 = new_uchar_array(27);

			$result = GetMultipleWEPKeys($cfgs,$wepIndex,$wepLen1,$wepKey1,$wepLen2,$wepKey2,$wepLen3,$wepKey3,$wepLen4,$wepKey4);
			if($result == SDCERR_SUCCESS){
				for($i=1;$i<5;$i++)
				{
					$wepString = '';
					if($i == 1){
						WEPLENp_assign($wepLen,WEPLENp_value($wepLen1));
						if(WEPLENp_value($wepLen) == WEPLEN_40BIT){
							$wepString = "*****";
						}
						else if(WEPLENp_value($wepLen) == WEPLEN_128BIT){
							$wepString = "****************";
						}
						$returnedResult['WEPKey1'] = $wepString;
					}
					if($i == 2){
						WEPLENp_assign($wepLen,WEPLENp_value($wepLen2));
						if(WEPLENp_value($wepLen) == WEPLEN_40BIT){
							$wepString = "*****";
						}
						else if(WEPLENp_value($wepLen) == WEPLEN_128BIT){
							$wepString = "****************";
						}
						$returnedResult['WEPKey2'] = $wepString;
					}
					if($i == 3){
						WEPLENp_assign($wepLen,WEPLENp_value($wepLen3));
						if(WEPLENp_value($wepLen) == WEPLEN_40BIT){
							$wepString = "*****";
						}
						else if(WEPLENp_value($wepLen) == WEPLEN_128BIT){
							$wepString = "****************";
						}
						$returnedResult['WEPKey3'] = $wepString;
					}
					if($i == 4){
						WEPLENp_assign($wepLen,WEPLENp_value($wepLen4));
						if(WEPLENp_value($wepLen) == WEPLEN_40BIT){
							$wepString = "*****";
						}
						else if(WEPLENp_value($wepLen) == WEPLEN_128BIT){
							$wepString = "****************";
						}
						$returnedResult['WEPKey4'] = $wepString;
					}
				}
				$returnedResult['wepIndex'] = intp_value($wepIndex);
			}

			delete_intp($wepIndex);
			delete_WEPLENp($wepLen);
			delete_WEPLENp($wepLen1);
			delete_WEPLENp($wepLen2);
			delete_WEPLENp($wepLen3);
			delete_WEPLENp($wepLen4);
			delete_uchar_array($wepKey1);
			delete_uchar_array($wepKey2);
			delete_uchar_array($wepKey3);
			delete_uchar_array($wepKey4);
		}

		if($cfgs->wepType == WPA_PSK || $cfgs->wepType == WPA2_PSK || $cfgs->wepType == WPA_PSK_AES || $cfgs->wepType == WPA2_PSK_TKIP){
			$psk = str_repeat(" ",PSK_SZ);
			$result = GetPSK($cfgs,$psk);
			if($result == SDCERR_SUCCESS){
				$returnedResult['PSK'] = "********";
			}
		}

		if($cfgs->eapType != EAP_NONE)
		{
			switch ($cfgs->eapType){
				case EAP_LEAP:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$result = GetLEAPCred($cfgs,$userName,$passWord);
					if($result == SDCERR_SUCCESS){
						$returnedResult['userName'] = trim($userName);
						$returnedResult['passWord'] = "********";
					}
					break;
				case EAP_EAPFAST:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$PACFileName = str_repeat(" ",CRED_PFILE_SZ);
					$PACPassWord = str_repeat(" ",CRED_PFILE_SZ);
					$result = GetEAPFASTCred($cfgs,$userName,$passWord,$PACFileName,$PACPassWord);
					if($result == SDCERR_SUCCESS){
						$returnedResult['userName'] = trim($userName);
						$returnedResult['passWord'] = "********";
						$returnedResult['PACFileName'] = $PACFileName;
						$returnedResult['PACPassWord'] = "********";
					}
					break;
				case EAP_PEAPMSCHAP:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetPEAPMSCHAPCred($cfgs,$userName,$passWord,$certLocation,$caCert);
					if($result == SDCERR_SUCCESS){
						$returnedResult['userName'] = trim($userName);
						$returnedResult['passWord'] = "********";
						$returnedResult['CACert'] = trim($caCert);
					}
					delete_CERTLOCATIONp($certLocation);
					break;
				case EAP_PEAPGTC:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetPEAPGTCCred($cfgs,$userName,$passWord,$certLocation,$caCert);
					if($result == SDCERR_SUCCESS){
						$returnedResult['userName'] = trim($userName);
						$returnedResult['passWord'] = "********";
						$returnedResult['CACert'] = trim($caCert);
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
						$returnedResult['userName'] = trim($userName);
						$returnedResult['userCert'] = trim($userCert);
						$result = GetUserCertPassword($cfgs,$userCertPassword);
						$returnedResult['userCertPassword'] = "********";
						$returnedResult['CACert'] = trim($caCert);
					}
					delete_CERTLOCATIONp($certLocation);
					break;
				case EAP_EAPTTLS:
					$userName = str_repeat(" ",USER_NAME_SZ);
					$passWord = str_repeat(" ",USER_PWD_SZ);
					$certLocation = new_CERTLOCATIONp();
					$caCert = str_repeat(" ",CRED_CERT_SZ);
					$result = GetEAPTTLSCred($cfgs,$userName,$passWord,$certLocation,$caCert);
					if($result == SDCERR_SUCCESS){
						$returnedResult['userName'] = trim($userName);
						$returnedResult['passWord'] = "********";
						$returnedResult['CACert'] = trim($caCert);
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
						$returnedResult['userName'] = trim($userName);
						$returnedResult['userCert'] = trim($userCert);
						$result = GetUserCertPassword($cfgs,$userCertPassword);
						$returnedResult['userCertPassword'] = "********";
						$returnedResult['CACert'] = trim($caCert);
					}
					delete_CERTLOCATIONp($certLocation);
					break;
				default:
					$returnedResult['SDCERR'] = $result;
			}
		}
	}
	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

	free_SDCConfig($cfgs);

?>
