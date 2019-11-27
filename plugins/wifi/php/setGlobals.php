<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	require($_SERVER['DOCUMENT_ROOT'] . "/php/webLCM.php");
	require("wifi.php");
	$returnedResult['SESSION'] = verifyAuthentication(true);
	if ($returnedResult['SESSION'] != SDCERR_SUCCESS){
		echo json_encode($returnedResult);
		return;
	}
	$global = json_decode(stripslashes(file_get_contents("php://input")));

	$rcs = new_RADIOCHIPSETp();
	$result = LRD_WF_GetRadioChipSet($rcs);
	if($result == SDCERR_SUCCESS){

		$supportedGlobals = new SDCGlobalConfig();
		$result = LRD_WF_GetSupportedGlobals(RADIOCHIPSETp_value($rcs), $supportedGlobals);
		if($result == SDCERR_SUCCESS){
			$cfgs = new SDCGlobalConfig();
			$result = GetGlobalSettings($cfgs);

			if($result == SDCERR_SUCCESS){
				if ($supportedGlobals->aLRS){
					$cfgs->aLRS = $global->{'aLRS'};
				}

				if ($supportedGlobals->authServerType){
					$cfgs->authServerType = $global->{'authServerType'};
				}

				if ($supportedGlobals->autoProfile){
					$cfgs->autoProfile = $global->{'autoProfile'};
				}

				if ($supportedGlobals->bLRS){
					$cfgs->bLRS = $global->{'bLRS'};
				}

				if ($supportedGlobals->BeaconMissTimeout){
					$cfgs->BeaconMissTimeout = $global->{'BeaconMissTimeout'};
				}

				if ($supportedGlobals->BTcoexist){
					$cfgs->BTcoexist = $global->{'BTcoexist'};
				}

				if ($supportedGlobals->CCXfeatures){
					$cfgs->CCXfeatures = $global->{'CCXfeatures'};
				}

				if ($supportedGlobals->certPath){
					$cfgs->certPath = $global->{'certPath'};
				}

				if ($supportedGlobals->defAdhocChannel){
					$cfgs->defAdhocChannel = $global->{'defAdhocChannel'};
				}

				if ($supportedGlobals->DFSchannels){
					$cfgs->DFSchannels = $global->{'DFSchannels'};
				}

				if ($supportedGlobals->fragThreshold){
					$cfgs->fragThreshold = $global->{'fragThreshold'};
				}

				if ($supportedGlobals->PMKcaching){
					$cfgs->PMKcaching = $global->{'PMKcaching'};
				}

				if ($supportedGlobals->probeDelay){
					$cfgs->probeDelay = $global->{'probeDelay'};
				}

				if ($supportedGlobals->regDomain){
					$cfgs->regDomain = $global->{'regDomain'};
				}

				if ($supportedGlobals->roamPeriodms){
					$cfgs->roamPeriodms = $global->{'roamPeriodms'};
				}

				if ($supportedGlobals->roamTrigger){
					$cfgs->roamTrigger = $global->{'roamTrigger'};
				}

				if ($supportedGlobals->RTSThreshold){
					$cfgs->RTSThreshold = $global->{'RTSThreshold'};
				}

				if ($supportedGlobals->scanDFSTime){
					$cfgs->scanDFSTime = $global->{'scanDFSTime'};
				}

				if ($supportedGlobals->TTLSInnerMethod){
					$cfgs->TTLSInnerMethod = $global->{'TTLSInnerMethod'};
				}

				if ($supportedGlobals->uAPSD){
					$cfgs->uAPSD = $global->{'uAPSD'};
				}

				if ($supportedGlobals->WMEenabled){
					$cfgs->WMEenabled = $global->{'WMEenabled'};
				}

				if ($supportedGlobals->suppInfo & 4){
					if ($global->{'suppInfoDateCheck'} == 1){
						$cfgs->suppInfo |= SUPPINFO_TLS_TIME_CHECK;
					}elseif ($global->{'suppInfoDateCheck'} == 0){
						$cfgs->suppInfo &= ~SUPPINFO_TLS_TIME_CHECK;
					}
				}
				if ($supportedGlobals->suppInfo & 1){
					if ($global->{'fips'} == 1){
						$cfgs->suppInfo |= SUPPINFO_FIPS;
					}elseif ($global->{'fips'} == 0){
						$cfgs->suppInfo &= ~SUPPINFO_FIPS;
					}
				}

				$result = SetGlobalSettings($cfgs);
			}
		}
	}
	delete_RADIOCHIPSETp($rcs);

	$returnedResult['SDCERR'] = REPORT_RETURN_DBG(__DIR__, __FILE__ ,__LINE__, $result);

	echo json_encode($returnedResult);
?>
