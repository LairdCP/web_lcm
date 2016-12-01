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

	$rcs = new_RADIOCHIPSETp();
	$result = LRD_WF_GetRadioChipSet($rcs);
	if($result == SDCERR_SUCCESS){
		$supportedGlobals = new SDCGlobalConfig();
		$result = LRD_WF_GetSupportedGlobals(RADIOCHIPSETp_value($rcs), $supportedGlobals);
		if($result == SDCERR_SUCCESS){
			$returnedResult['aLRS'] = $supportedGlobals->aLRS;
			$returnedResult['authServerType'] = $supportedGlobals->authServerType;
			$returnedResult['autoProfile'] = $supportedGlobals->autoProfile;
			$returnedResult['bLRS'] = $supportedGlobals->bLRS;
			$returnedResult['BeaconMissTimeout'] = $supportedGlobals->BeaconMissTimeout;
			$returnedResult['BTcoexist'] = $supportedGlobals->BTcoexist;
			$returnedResult['CCXfeatures'] = $supportedGlobals->CCXfeatures;
			if ($supportedGlobals->certPath){
				$returnedResult['certPath'] = 1;
			}else{
				$returnedResult['certPath'] = 0;
			}
			$returnedResult['suppInfo'] = $supportedGlobals->suppInfo;
			$returnedResult['defAdhocChannel'] = $supportedGlobals->defAdhocChannel;
			$returnedResult['DFSchannels'] = $supportedGlobals->DFSchannels;
			$returnedResult['fragThreshold'] = $supportedGlobals->fragThreshold;
			$returnedResult['PMKcaching'] = $supportedGlobals->PMKcaching;
			$returnedResult['probeDelay'] = $supportedGlobals->probeDelay;
			$returnedResult['regDomain'] = $supportedGlobals->regDomain;
			$returnedResult['roamPeriodms'] = $supportedGlobals->roamPeriodms;
			$returnedResult['roamTrigger'] = $supportedGlobals->roamTrigger;
			$returnedResult['RTSThreshold'] = $supportedGlobals->RTSThreshold;
			$returnedResult['scanDFSTime'] = $supportedGlobals->scanDFSTime;
			$returnedResult['TTLSInnerMethod'] = $supportedGlobals->TTLSInnerMethod;
			$returnedResult['uAPSD'] = $supportedGlobals->uAPSD;
			$returnedResult['WMEenabled'] = $supportedGlobals->WMEenabled;
			$returnedResult['suppInfoDateCheck'] = $supportedGlobals->suppInfo & SUPPINFO_TLS_TIME_CHECK; //special case
			$returnedResult['fips'] = $supportedGlobals->suppInfo & SUPPINFO_FIPS; //special case
		}
	}

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);

	delete_RADIOCHIPSETp($rcs);
?>
