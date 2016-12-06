<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

function wifi(){
	$wifi_defines = array(
		'DEFINES' => array(
			'CONFIG_NAME_SZ' => CONFIG_NAME_SZ,
			'SSID_SZ' => SSID_SZ,
			'CLIENT_NAME_SZ' => CLIENT_NAME_SZ,
			'MAC_AS_ASCII_SZ' => MAC_AS_ASCII_SZ,
			'USER_NAME_SZ' => USER_NAME_SZ,
			'USER_PWD_SZ' => USER_PWD_SZ,
			'PSK_SZ' => PSK_SZ,
			'MAX_CFGS' => MAX_CFGS,
			'NUM_WEP_KEYS' => NUM_WEP_KEYS,
			'MAC_ADDR_SZ' => MAC_ADDR_SZ,
			'IPv4_ADDR_SZ' => IPv4_ADDR_SZ,
			'PDELAY_LOW' => PDELAY_LOW,
			'PDELAY_HIGH' => PDELAY_HIGH,
			'PTIME_LOW' => PTIME_HIGH,
			'BEACONMISSTIME_LOW' => BEACONMISSTIME_LOW,
			'BEACONMISSTIME_HIGH' => BEACONMISSTIME_HIGH,
			'FRAG_LOW' => FRAG_LOW,
			'FRAG_HIGH' => FRAG_HIGH,
			'RTS_LOW' => RTS_LOW,
			'RTS_HIGH' => RTS_HIGH,
			'AUTH_LOW' => AUTH_LOW,
			'AUTH_HIGH' => AUTH_HIGH,
			'PROBE_DELAY_LOW' => PROBE_DELAY_LOW,
			'PROBE_DELAY_HIGH' => PROBE_DELAY_HIGH,
			'ROAM_DELTA_LOW' => ROAM_DELTA_LOW,
			'ROAM_DELTA_HIGH' => ROAM_DELTA_HIGH,
			'ROAM_PERIOD_LOW' => ROAM_PERIOD_LOW,
			'ROAM_PERIOD_HIGH' => ROAM_PERIOD_HIGH,
			'ROAM_PERIOD_MS_LOW' => ROAM_PERIOD_MS_LOW,
			'ROAM_PERIOD_MS_HIGH' => ROAM_PERIOD_MS_HIGH,
			'ROAM_TRIGGER_LOW' => ROAM_TRIGGER_LOW,
			'ROAM_TRIGGER_HIGH' => ROAM_TRIGGER_HIGH,
			'SCANDFSTIME_LOW' => SCANDFSTIME_LOW,
			'SCANDFSTIME_HIGH' => SCANDFSTIME_HIGH,
			'TX_MAX_LOW' => TX_MAX_LOW,
			'TX_MAX_HIGH' => TX_MAX_HIGH,
			'MAX_CERT_PATH' => MAX_CERT_PATH,
			'CRED_CA_POS' => CRED_CA_POS,
			'CRED_UCA_POS' => CRED_UCA_POS,
			'CRED_PFILE_POS' => CRED_PFILE_POS,
			'CRED_CERT_SZ' => CRED_CERT_SZ,
			'CRED_PFILE_SZ' => CRED_PFILE_SZ,
			'USER_CERT_PW_SZ' => USER_CERT_PW_SZ,
			'LRS_MAX_CHAN' => LRS_MAX_CHAN,
			'RADIOTYPE_BCM_OFFSET' => RADIOTYPE_BCM_OFFSET,
			'RADIOTYPE_AR_OFFSET' => RADIOTYPE_AR_OFFSET,
			'LRD_SYS_FAM_WB' => LRD_SYS_FAM_WB,
			'LRD_SYS_FAM_MSD_SSD' => LRD_SYS_FAM_MSD_SSD,
			'XMITBIT' => XMITBIT,
			'CRYPT_BUFFER_SIZE' => CRYPT_BUFFER_SIZE,
			'MIN_PSP_DELAY' => MIN_PSP_DELAY,
			'MAX_PSP_DELAY' => MAX_PSP_DELAY,
			'DEFAULT_PSP_DELAY' => DEFAULT_PSP_DELAY,
			'SUPPINFO_FIPS' => SUPPINFO_FIPS,
			'SUPPINFO_TLS_TIME_CHECK' => SUPPINFO_TLS_TIME_CHECK,
			'SUPPINFO_WPA1_ORIGINAL_OPERATION' => SUPPINFO_WPA1_ORIGINAL_OPERATION,
			'securityField_username' => securityField_username,
			'securityField_password' => securityField_password,
			'securityField_usercert' => securityField_usercert,
			'securityField_usercertpw' => securityField_usercertpw,
			'securityField_psk' => securityField_psk,
			'securityField_wepkeys' => securityField_wepkeys,
		),
		'AUTH' => array(
			'AUTH_OPEN' => AUTH_OPEN,
			'AUTH_SHARED' => AUTH_SHARED,
			'AUTH_NETWORK_EAP' => AUTH_NETWORK_EAP,
		),
		'EAPTYPE' => array(
			'EAP_NONE' => EAP_NONE,
			'EAP_LEAP' => EAP_LEAP,
			'EAP_EAPFAST' => EAP_EAPFAST,
			'EAP_PEAPMSCHAP' => EAP_PEAPMSCHAP,
			'EAP_PEAPGTC' => EAP_PEAPGTC,
			'EAP_EAPTLS' => EAP_EAPTLS,
			'EAP_EAPTTLS' => EAP_EAPTTLS,
			'EAP_PEAPTLS' => EAP_PEAPTLS,
			'EAP_WAPI_CERT' => EAP_WAPI_CERT,
		),
		'POWERSAVE' => array(
			'POWERSAVE_OFF' => POWERSAVE_OFF,
			'POWERSAVE_MAX' => POWERSAVE_MAX,
			'POWERSAVE_FAST' => POWERSAVE_FAST,
		),
		'WEPTYPE' => array(
			'WEP_OFF' => 	WEP_OFF,
			'WEP_ON' => 	WEP_ON,
			'WEP_AUTO' => 	WEP_AUTO,
			'WPA_PSK' => 	WPA_PSK,
			'WPA_TKIP' => 	WPA_TKIP,
			'WPA2_PSK' => 	WPA2_PSK,
			'WPA2_AES' => 	WPA2_AES,
			'CCKM_TKIP' => 	CCKM_TKIP,
			'WEP_CKIP' => 	WEP_CKIP,
			'WEP_AUTO_CKIP' => 	WEP_AUTO_CKIP,
			'CCKM_AES' => 	CCKM_AES,
			'WPA_PSK_AES' => 	WPA_PSK_AES,
			'WPA_AES' => 	WPA_AES,
			'WPA2_PSK_TKIP' => 	WPA2_PSK_TKIP,
			'WPA2_TKIP' => 	WPA2_TKIP,
			'WAPI_PSK' => 	WAPI_PSK,
			'WAPI_CERT' => 	WAPI_CERT,
		),
		'RADIOMODE' => array(
			'RADIOMODE_B_ONLY' => RADIOMODE_B_ONLY,
			'RADIOMODE_BG' => RADIOMODE_BG,
			'RADIOMODE_G_ONLY' => RADIOMODE_G_ONLY,
			'RADIOMODE_BG_LRS' => RADIOMODE_BG_LRS,
			'RADIOMODE_A_ONLY' => RADIOMODE_A_ONLY,
			'RADIOMODE_ABG' => RADIOMODE_ABG,
			'RADIOMODE_BGA' => RADIOMODE_BGA,
			'RADIOMODE_ADHOC' => RADIOMODE_ADHOC,
			'RADIOMODE_GN' => RADIOMODE_GN,
			'RADIOMODE_AN' => RADIOMODE_AN,
			'RADIOMODE_ABGN' => RADIOMODE_ABGN,
			'RADIOMODE_BGAN' => RADIOMODE_BGAN,
			'RADIOMODE_BGN' => RADIOMODE_BGN
		),
		'TXPOWER' => array(
			'TXPOWER_MAX' => TXPOWER_MAX,
			'TXPOWER_1' => TXPOWER_1,
			'TXPOWER_5' => TXPOWER_5,
			'TXPOWER_10' => TXPOWER_10,
			'TXPOWER_20' => TXPOWER_20,
			'TXPOWER_30' => TXPOWER_30,
			'TXPOWER_50' => TXPOWER_50,
		),
		'BITRATE' => array(
			'BITRATE_AUTO' => BITRATE_AUTO,
			'BITRATE_1' => BITRATE_1,
			'BITRATE_2' => BITRATE_2,
			'BITRATE_5_5' => BITRATE_5_5,
			'BITRATE_11' => BITRATE_11,
			'BITRATE_6' => BITRATE_6,
			'BITRATE_9' => BITRATE_9,
			'BITRATE_12' => BITRATE_12,
			'BITRATE_18' => BITRATE_18,
			'BITRATE_24' => BITRATE_24,
			'BITRATE_36' => BITRATE_36,
			'BITRATE_48' => BITRATE_48,
			'BITRATE_54' => BITRATE_54,
			'BITRATE_6_5' => BITRATE_6_5,
			'BITRATE_13' => BITRATE_13,
			'BITRATE_19_5' => BITRATE_19_5,
			'BITRATE_26' => BITRATE_26,
			'BITRATE_39' => BITRATE_39,
			'BITRATE_52' => BITRATE_52,
			'BITRATE_58_5' => BITRATE_58_5,
			'BITRATE_78' => BITRATE_78,
			'BITRATE_104' => BITRATE_104,
			'BITRATE_117' => BITRATE_117,
			'BITRATE_130' => BITRATE_130,
			'BITRATE_7_2' => BITRATE_7_2,
			'BITRATE_14_4' => BITRATE_14_4,
			'BITRATE_21_7' => BITRATE_21_7,
			'BITRATE_28_9' => BITRATE_28_9,
			'BITRATE_43_3' => BITRATE_43_3,
			'BITRATE_57_8' => BITRATE_57_8,
			'BITRATE_65' => BITRATE_65,
			'BITRATE_72' => BITRATE_72,
			'BITRATE_86_7' => BITRATE_86_7,
			'BITRATE_115_6' => BITRATE_115_6,
			'BITRATE_144_4' => BITRATE_144_4,
			'BITRATE_13_5' => BITRATE_13_5,
			'BITRATE_27' => BITRATE_27,
			'BITRATE_40_5' => BITRATE_40_5,
			'BITRATE_81' => BITRATE_81,
			'BITRATE_108' => BITRATE_108,
			'BITRATE_121_5' => BITRATE_121_5,
			'BITRATE_135' => BITRATE_135,
			'BITRATE_162' => BITRATE_162,
			'BITRATE_216' => BITRATE_216,
			'BITRATE_243' => BITRATE_243,
			'BITRATE_270' => BITRATE_270,
			'BITRATE_15' => BITRATE_15,
			'BITRATE_30' => BITRATE_30,
			'BITRATE_45' => BITRATE_45,
			'BITRATE_60' => BITRATE_60,
			'BITRATE_90' => BITRATE_90,
			'BITRATE_120' => BITRATE_120,
			'BITRATE_150' => BITRATE_150,
			'BITRATE_180' => BITRATE_180,
			'BITRATE_240' => BITRATE_240,
			'BITRATE_300' => BITRATE_300,
		),
		'PREAMBLE' => array(
			'PRE_AUTO' => PRE_AUTO,
			'PRE_SHORT' => PRE_SHORT,
			'PRE_LONG' => PRE_LONG,
		),
		'GSHORTSLOT' => array(
			'GSHORT_AUTO' => GSHORT_AUTO,
			'GSHORT_OFF' => GSHORT_OFF,
			'GSHORT_ON' => GSHORT_ON,
		),
		'BT_COEXIST' => array(
			'BT_OFF' => BT_OFF,
			'BT_ON' => BT_ON,
		),
		'REG_DOMAIN' => array(
			'REG_FCC' => REG_FCC,
			'REG_ETSI' => REG_ETSI ,
			'REG_TELEC' => REG_TELEC,
			'REG_WW' => REG_WW,
			'REG_KCC' => REG_KCC,
			'REG_CA' => REG_CA,
			'REG_FR' => REG_FR,
			'REG_GB' => REG_GB,
			'REG_AU' => REG_AU,
			'REG_NZ' => REG_NZ,
			'REG_CN' => REG_CN,
			'REG_BR' => REG_BR,
			'REG_RU' => REG_RU,
		),
		'PING_PAYLOAD' => array(
			'PP_32' => PP_32,
			'PP_64' => PP_64,
			'PP_128' => PP_128,
			'PP_256' => PP_256,
			'PP_512' => PP_512,
			'PP_1024' => PP_1024,
		),
		'RX_DIV' => array(
			'RXDIV_MAIN' => RXDIV_MAIN,
			'RXDIV_AUX' => RXDIV_AUX,
			'RXDIV_START_AUX' => RXDIV_START_AUX,
			'RXDIV_START_MAIN' => RXDIV_START_MAIN,
		),
		'TX_DIV' => array(
			'TXDIV_MAIN' => TXDIV_MAIN,
			'TXDIV_AUX' => TXDIV_AUX,
			'TXDIV_ON' => TXDIV_ON,
		),
		'ROAM_TRIG' => array(
			'RTRIG_50' => RTRIG_50,
			'RTRIG_55' => RTRIG_55,
			'RTRIG_60' => RTRIG_60,
			'RTRIG_65' => RTRIG_65,
			'RTRIG_70' => RTRIG_70,
			'RTRIG_75' => RTRIG_75,
			'RTRIG_80' => RTRIG_80,
			'RTRIG_85' => RTRIG_85,
			'RTRIG_90' => RTRIG_90,
		),
		'ROAM_DELTA' => array(
			'RDELTA_0' => RDELTA_0,
			'RDELTA_5' => RDELTA_5,
			'RDELTA_10' => RDELTA_10,
			'RDELTA_15' => RDELTA_15,
			'RDELTA_20' => RDELTA_20,
			'RDELTA_25' => RDELTA_25,
			'RDELTA_30' => RDELTA_30,
			'RDELTA_35' => RDELTA_35,
			'RDELTA_40' => RDELTA_40,
			'RDELTA_45' => RDELTA_45,
			'RDELTA_50' => RDELTA_50,
			'RDELTA_55' => RDELTA_55,
		),
		'ROAM_PERIOD' => array(
			'RPERIOD_0' => RPERIOD_0,
			'RPERIOD_5' => RPERIOD_5,
			'RPERIOD_10' => RPERIOD_10,
			'RPERIOD_15' => RPERIOD_15,
			'RPERIOD_20' => RPERIOD_20,
			'RPERIOD_25' => RPERIOD_25,
			'RPERIOD_30' => RPERIOD_30,
			'RPERIOD_35' => RPERIOD_35,
			'RPERIOD_40' => RPERIOD_40,
			'RPERIOD_45' => RPERIOD_45,
			'RPERIOD_50' => RPERIOD_50,
			'RPERIOD_55' => RPERIOD_55,
			'RPERIOD_60' => RPERIOD_60,
		),
		'CCX_FEATURES' => array(
			'CCX_OPTIMIZED' => CCX_OPTIMIZED,
			'CCX_FULL' => CCX_FULL,
			'CCX_OFF' => CCX_OFF,
		),
		'WEPLEN' => array(
			'WEPLEN_NOT_SET' => WEPLEN_NOT_SET,
			'WEPLEN_40BIT' => WEPLEN_40BIT,
			'WEPLEN_128BIT' => WEPLEN_128BIT,
		),
		'FCC_TEST' => array(
			'FCCTEST_OFF' => FCCTEST_OFF,
			'FCCTEST_TX' => FCCTEST_TX,
			'FCCTEST_RX' => FCCTEST_RX,
			'FCCTEST_FREQ' => FCCTEST_FREQ,
		),
		'CARDSTATE' => array(
			'CARDSTATE_NOT_INSERTED' => CARDSTATE_NOT_INSERTED,
			'CARDSTATE_NOT_ASSOCIATED' => CARDSTATE_NOT_ASSOCIATED,
			'CARDSTATE_ASSOCIATED' => CARDSTATE_ASSOCIATED,
			'CARDSTATE_AUTHENTICATED' => CARDSTATE_AUTHENTICATED,
			'CARDSTATE_FCCTEST' => CARDSTATE_FCCTEST,
			'CARDSTATE_NOT_SDC' => CARDSTATE_NOT_SDC,
			'CARDSTATE_DISABLED' => CARDSTATE_DISABLED,
			'CARDSTATE_ERROR' => CARDSTATE_ERROR,
			'CARDSTATE_AP_MODE' => CARDSTATE_AP_MODE,
		),
		'RADIOTYPE' => array(
			'RADIOTYPE_BG' => RADIOTYPE_BG,
			'RADIOTYPE_ABG' => RADIOTYPE_ABG,
			'RADIOTYPE_NBG' => RADIOTYPE_NBG,
			'RADIOTYPE_NABG' => RADIOTYPE_NABG,
			'RADIOTYPE_AR_BG' => RADIOTYPE_AR_BG,
			'RADIOTYPE_AR_ABG' => RADIOTYPE_AR_ABG,
			'RADIOTYPE_AR_NBG' => RADIOTYPE_AR_NBG,
			'RADIOTYPE_AR_NABG' => RADIOTYPE_AR_NABG,
			'RADIOTYPE_NOT_SDC' => RADIOTYPE_NOT_SDC,
			'RADIOTYPE_NOT_SDC_1' => RADIOTYPE_NOT_SDC_1, //reserved
		),
		'RADIOCHIPSET' => array(
			'RADIOCHIPSET_NONE' => RADIOCHIPSET_NONE,
			'RADIOCHIPSET_SDC10' => RADIOCHIPSET_SDC10,
			'RADIOCHIPSET_SDC15' => RADIOCHIPSET_SDC15,
			'RADIOCHIPSET_SDC30' => RADIOCHIPSET_SDC30,
			'RADIOCHIPSET_SDC40L' => RADIOCHIPSET_SDC40L,
			'RADIOCHIPSET_SDC40NBT' => RADIOCHIPSET_SDC40NBT,
			'RADIOCHIPSET_SDC45' => RADIOCHIPSET_SDC45,
			'RADIOCHIPSET_SDC50' => RADIOCHIPSET_SDC50,
		),
		'CERTLOCATION' => array(
			'CERT_NONE' => CERT_NONE,
			'CERT_FILE' => CERT_FILE,
			'CERT_FULL_STORE' => CERT_FULL_STORE,
			'CERT_IN_STORE' => CERT_IN_STORE,
		),
		'INTERFERENCE' => array(
			'INTER_NONE' => INTER_NONE,
			'INTER_NONWLAN' => INTER_NONWLAN,
			'INTER_WLAN' => INTER_WLAN,
			'INTER_AUTO' => INTER_AUTO,
		),
		'TTLS_INNER_METHOD' => array(
			'TTLS_AUTO' => TTLS_AUTO,
			'TTLS_MSCHAPV2' => TTLS_MSCHAPV2,
			'TTLS_MSCHAP' => TTLS_MSCHAP,
			'TTLS_PAP' => TTLS_PAP,
			'TTLS_CHAP' => TTLS_CHAP,
			'TTLS_EAP_MSCHAPV2' => TTLS_EAP_MSCHAPV2,
		),
		'DFS_CHANNELS' => array(
			'DFS_OFF' => DFS_OFF,
			'DFS_FULL' => DFS_FULL,
			'DFS_OPTIMIZED' => DFS_OPTIMIZED,
		),
		'uAPSD' => array(
			'UAPSD_AC_VO' => UAPSD_AC_VO,
			'UAPSD_AC_VI' => UAPSD_AC_VI,
			'UAPSD_AC_BK' => UAPSD_AC_BK,
			'UAPSD_AC_BE' => UAPSD_AC_BE,
		),
		'LRD_WF_BSSTYPE' => array(
			'INFRASTRUCTURE' => INFRASTRUCTURE,
		    'ADHOC' => ADHOC,
		),
		'WF_SUPP_LOGLEVEL' => array(
			'WF_SUPP_DBG_NONE' => WF_SUPP_DBG_NONE,
			'WF_SUPP_DBG_ERROR' => WF_SUPP_DBG_ERROR,
			'WF_SUPP_DBG_WARNING' => WF_SUPP_DBG_WARNING,
			'WF_SUPP_DBG_INFO' => WF_SUPP_DBG_INFO,
			'WF_SUPP_DBG_DEBUG' => WF_SUPP_DBG_DEBUG,
			'WF_SUPP_DBG_MSGDUMP' => WF_SUPP_DBG_MSGDUMP,
			'WF_SUPP_DBG_EXCESSIVE' => WF_SUPP_DBG_EXCESSIVE,
		),
		'LRD_WF_DRV_DEBUG' => array(
			'LRD_WF_DRV_DEBUG_NONE' => LRD_WF_DRV_DEBUG_NONE,
			'LRD_WF_DRV_DEBUG_LOW' => LRD_WF_DRV_DEBUG_LOW,
			'LRD_WF_DRV_DEBUG_MED' => LRD_WF_DRV_DEBUG_MED,
			'LRD_WF_DRV_DEBUG_HIGH' => LRD_WF_DRV_DEBUG_HIGH,
			'LRD_WF_DRV_DEBUG_RADIO_SPECIFIC' => LRD_WF_DRV_DEBUG_RADIO_SPECIFIC,
		),
		'ATH6K_DEBUG_MASK' => array(
			'ATH6KL_DBG_CREDIT' => ATH6KL_DBG_CREDIT,
			'ATH6KL_DBG_WLAN_TX' => ATH6KL_DBG_WLAN_TX,
			'ATH6KL_DBG_WLAN_RX' => ATH6KL_DBG_WLAN_RX,
			'ATH6KL_DBG_BMI' => ATH6KL_DBG_BMI,
			'ATH6KL_DBG_HTC' => ATH6KL_DBG_HTC,
			'ATH6KL_DBG_HIF' => ATH6KL_DBG_HIF,
			'ATH6KL_DBG_IRQ' => ATH6KL_DBG_IRQ,
			'ATH6KL_DBG_WMI' => ATH6KL_DBG_WMI,
			'ATH6KL_DBG_TRC' => ATH6KL_DBG_TRC,
			'ATH6KL_DBG_SCATTER' => ATH6KL_DBG_SCATTER,
			'ATH6KL_DBG_WLAN_CFG' => ATH6KL_DBG_WLAN_CFG,
			'ATH6KL_DBG_RAW_BYTES' => ATH6KL_DBG_RAW_BYTES,
			'ATH6KL_DBG_AGGR' => ATH6KL_DBG_AGGR,
			'ATH6KL_DBG_SDIO' => ATH6KL_DBG_SDIO,
			'ATH6KL_DBG_SDIO_DUMP' => ATH6KL_DBG_SDIO_DUMP,
			'ATH6KL_DBG_BOOT' => ATH6KL_DBG_BOOT,
			'ATH6KL_DBG_WMI_DUMP' => ATH6KL_DBG_WMI_DUMP,
			'ATH6KL_DBG_SUSPEND' => ATH6KL_DBG_SUSPEND,
			'ATH6KL_DBG_USB' => ATH6KL_DBG_USB,
			'ATH6KL_DBG_USB_BULK' => ATH6KL_DBG_USB_BULK,
			'ATH6KL_DBG_RECOVERY' => ATH6KL_DBG_RECOVERY,
			'ATH6KL_DBG_ANY' => ATH6KL_DBG_ANY,
		),
	);

	return $wifi_defines;
}

function nullTrim($string){
	for( $i = 0; $i <= strlen(trim($string)); $i++ ) {
		$char = substr($string,$i,1);
		if ($char == "\0"){
			break;
		}
		$return_string .= $char;
	}
	return $return_string;
}

?>
