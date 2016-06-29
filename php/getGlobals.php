<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	require("../lrd_php_sdk.php");
	if(!extension_loaded('lrd_php_sdk')){
		syslog(LOG_WARNING, "ERROR: failed to load lrd_php_sdk");
	}
	session_start();
	header("Content-Type: application/json");

	$returnedResult = [
		'SDCERR' => SDCERR_FAIL,
		'SESSION' => SDCERR_FAIL,
	];

	if (isset($_SESSION['LAST_ACTIVITY'])){
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60)) {
			// last request was more than 30 minutes ago
			session_unset();     // unset $_SESSION variable for the run-time
			session_destroy();   // destroy session data in storage
			echo json_encode($returnedResult);
			return;
		} else {
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
			$returnedResult['SESSION'] = SDCERR_SUCCESS;
		}
	} else {
		echo json_encode($returnedResult);
	}

	$rcs = new_RADIOCHIPSETp();
	$result = LRD_WF_GetRadioChipSet($rcs);
	if($result == SDCERR_SUCCESS){
		$cfgs = new SDCGlobalConfig();
		$result = GetGlobalSettings($cfgs);
		if($result == SDCERR_SUCCESS){
			$aLRSnumChannels = new_ulongp();
			$aChannels = new LRD_WF_LRSChannels();
			$result = LRD_WF_GetaLRSChannels($aLRSnumChannels, $aChannels, $cfgs->aLRS);
			if($result == SDCERR_SUCCESS){
				$aLRS = [];
				for($a = 0; $a < ulongp_value($aLRSnumChannels); $a++) {
					array_push($aLRS,ulong_array_getitem($aChannels->chan,$a));
				}
			}
			delete_ulongp($aLRSnumChannels);

			$bLRSnumChannels = new_ulongp();
			$bChannels = new LRD_WF_LRSChannels();
			$result = LRD_WF_GetbLRSChannels($bLRSnumChannels, $bChannels, $cfgs->bLRS);
			if($result == SDCERR_SUCCESS){
				$bLRS = [];
				for($b = 0; $b < ulongp_value($bLRSnumChannels); $b++) {
					array_push($bLRS,ulong_array_getitem($bChannels->chan,$b));
				}
			}
			delete_ulongp($bLRSnumChannels);

			$returnedResult['aLRS'] = $aLRS;
			$returnedResult['authServerType'] = $cfgs->authServerType;
			$returnedResult['autoProfile'] = $cfgs->autoProfile & 1;
			$returnedResult['bLRS'] = $bLRS;
			$returnedResult['BeaconMissTimeout'] = $cfgs->BeaconMissTimeout;
			$returnedResult['BTcoexist'] = $cfgs->BTcoexist;
			$returnedResult['CCXfeatures'] = $cfgs->CCXfeatures;
			$returnedResult['certPath'] = $cfgs->certPath;
			$returnedResult['suppInfo'] = $cfgs->suppInfo;
			$returnedResult['defAdhocChannel'] = $cfgs->defAdhocChannel;
			$returnedResult['DFSchannels'] = $cfgs->DFSchannels;
			$returnedResult['fragThreshold'] = $cfgs->fragThreshold;
			$returnedResult['PMKcaching'] = $cfgs->PMKcaching;
			$returnedResult['probeDelay'] = $cfgs->probeDelay;
			$returnedResult['regDomain'] = $cfgs->regDomain;
			$returnedResult['roamPeriodms'] = $cfgs->roamPeriodms;
			$returnedResult['roamTrigger'] = $cfgs->roamTrigger;
			$returnedResult['RTSThreshold'] = $cfgs->RTSThreshold;
			$returnedResult['scanDFSTime'] = $cfgs->scanDFSTime;
			$returnedResult['TTLSInnerMethod'] = $cfgs->TTLSInnerMethod;
			$returnedResult['uAPSD'] = $cfgs->uAPSD;
			$returnedResult['WMEenabled'] = $cfgs->WMEenabled;
			if ($cfgs->suppInfo & SUPPINFO_TLS_TIME_CHECK){
				$suppInfoDateCheck = 1;
			} else {
				$suppInfoDateCheck = 0;
			}
			$returnedResult['suppInfoDateCheck'] = $suppInfoDateCheck; //special case
			$returnedResult['fips'] = $cfgs->suppInfo & SUPPINFO_FIPS; //special case
			$current = str_repeat(" ",1);
			$next = str_repeat(" ",1);
			$combined = 4;
			$result = LRD_WF_GetFipsStatus($current, $next);
			if ($result == SDCERR_SUCCESS){
				$combined = ((boolval(trim($current)) << 1) | boolval(trim($next)));
			}
			$returnedResult['fipsStatus'] = $combined;
		}
	}

	$returnedResult['SDCERR'] = $result;

	echo json_encode($returnedResult);

	delete_RADIOCHIPSETp($rcs);
?>
