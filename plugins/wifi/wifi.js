// Copyright (c) 2016, Laird
// Contact: ews-support@lairdtech.com

clickStatusPage(0);
// setTimeout(clickStatusPage(),5000);

function CARDSTATEtoString(CARDSTATE){
	switch(CARDSTATE) {
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_NOT_INSERTED:
			return "Not Inserted";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_NOT_ASSOCIATED:
			return "Not Associated";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_ASSOCIATED:
			return "Associated";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_AUTHENTICATED:
			return "Authenticated";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_FCCTEST:
			return "FCC Test";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_NOT_SDC:
			return "Not Laird";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_DISABLED:
			return "Disabled";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_ERROR:
			return "Error";
		case defines.PLUGINS.wifi.CARDSTATE.CARDSTATE_AP_MODE:
			return "AP Mode";
		default:
			return "Unknown card state";
	}
}

function onChangeSubmit(){
	$("#submitButton").removeClass("disabled");
}

function onChangePowersave(){
	var powerSave = parseInt(document.getElementById("powerSave").value);
	switch (powerSave){
		case defines.PLUGINS.wifi.PLUGINS.wifi.POWERSAVE.POWERSAVE_OFF:
		case defines.PLUGINS.wifi.PLUGINS.wifi.POWERSAVE.POWERSAVE_MAX:
			$("#pspDelayDisplay").addClass("hidden");
			break;
		case defines.PLUGINS.wifi.PLUGINS.wifi.POWERSAVE.POWERSAVE_FAST:
			$("#pspDelayDisplay").removeClass("hidden");
			break;
		default:
			$("#pspDelayDisplay").removeClass("hidden");
			break;
	}
}

function onChangeSecurity(){
	var wepType = parseInt(document.getElementById("wepType").value);
	var eapType = parseInt(document.getElementById("eapType").value);
	function clearCredsDisplay(){
		$("#eapTypeDisplay").addClass("hidden");
		$("#wepIndexDisplay").addClass("hidden");
		$("#wepTypeOnDisplay").addClass("hidden");
		$("#pskDisplay").addClass("hidden");
		$("#userNameDisplay").addClass("hidden");
		$("#passWordDisplay").addClass("hidden");
		$("#userCertDisplay").addClass("hidden");
		$("#userCertPasswordDisplay").addClass("hidden");
		$("#CACertDisplay").addClass("hidden");
		$("#PACFilenameDisplay").addClass("hidden");
		$("#PACPasswordDisplay").addClass("hidden");
	}
	function displayProperEAPCreds(){
		$("#eapTypeDisplay").removeClass("hidden");
		$("#userNameDisplay").removeClass("hidden");
		if (!(eapType == defines.PLUGINS.wifi.PLUGINS.wifi.EAPTYPE.EAP_EAPTLS || eapType == defines.PLUGINS.wifi.PLUGINS.wifi.EAPTYPE.EAP_PEAPTLS)){
			$("#passWordDisplay").removeClass("hidden");
		} else {
			$("#userCertDisplay").removeClass("hidden");
			$("#userCertPasswordDisplay").removeClass("hidden");
		}
		if (eapType > defines.PLUGINS.wifi.PLUGINS.wifi.EAPTYPE.EAP_EAPFAST && eapType < defines.PLUGINS.wifi.PLUGINS.wifi.EAPTYPE.EAP_WAPI_CERT){
			$("#CACertDisplay").removeClass("hidden");
		}
		if (eapType == defines.PLUGINS.wifi.PLUGINS.wifi.EAPTYPE.EAP_EAPFAST){
			$("#PACFilenameDisplay").removeClass("hidden");
			$("#PACPasswordDisplay").removeClass("hidden");
		}
	}
	switch (wepType){
		case defines.PLUGINS.wifi.PLUGINS.wifi.WEPTYPE.WEP_OFF:
			clearCredsDisplay();
			break;
		case defines.PLUGINS.wifi.PLUGINS.wifi.WEPTYPE.WEP_ON:
			clearCredsDisplay();
			$("#wepIndexDisplay").removeClass("hidden");
			$("#wepTypeOnDisplay").removeClass("hidden");
			break;
		case defines.PLUGINS.wifi.PLUGINS.wifi.WEPTYPE.WEP_AUTO:
			clearCredsDisplay();
			displayProperEAPCreds();
			break;
		case defines.PLUGINS.wifi.PLUGINS.wifi.WEPTYPE.WPA_PSK:
			clearCredsDisplay();
			$("#pskDisplay").removeClass("hidden");
			break;
		case defines.PLUGINS.wifi.WEPTYPE.WPA_TKIP:
			clearCredsDisplay();
			displayProperEAPCreds();
			break;
		case defines.PLUGINS.wifi.WEPTYPE.WPA2_PSK:
			clearCredsDisplay();
			$("#pskDisplay").removeClass("hidden");
			break;
		case defines.PLUGINS.wifi.WEPTYPE.WPA2_AES:
		case defines.PLUGINS.wifi.WEPTYPE.CCKM_TKIP:
		case defines.PLUGINS.wifi.WEPTYPE.WEP_CKIP:
		case defines.PLUGINS.wifi.WEPTYPE.WEP_AUTO_CKIP:
		case defines.PLUGINS.wifi.WEPTYPE.CCKM_AES:
			clearCredsDisplay();
			displayProperEAPCreds();
			break;
		case defines.PLUGINS.wifi.WEPTYPE.WPA_PSK_AES:
			clearCredsDisplay();
			$("#pskDisplay").removeClass("hidden");
			break;
		case defines.PLUGINS.wifi.WEPTYPE.WPA_AES:
			clearCredsDisplay();
			displayProperEAPCreds();
			break;
		default:
			break;
	}
}

function updateStatus(){
	var getStatusJSON = $.getJSON( "plugins/wifi/php/status.php", function( data ) {
		if (data.SESSION == defines.SDCERR.SDCERR_FAIL){
			$("#loggout").addClass("hidden");
			$("#loggin").removeClass("hidden");
			$(".locked").addClass("hidden");
		}
		$("#updateProgressDisplay").addClass("hidden");
		if (data.SDCERR == defines.SDCERR.SDCERR_NO_HARDWARE){
			$("#status-success").addClass("hidden");
			$("#status-hardware").removeClass("hidden");
		} else {
			$("#status-success").removeClass("hidden");
			var rssi = data.rssi;
			var rssiMeter = rssi + 120;
			$('#cardState').html(CARDSTATEtoString(data.cardState));
			$('#configName').html(data.configName);
			$('#ssid').html(data.ssid);
			$('#channel').html(data.channel);
			$('#rssi').html(rssi);
			$('#clientName').html(data.clientName);
			$('#client_MAC').html(data.client_MAC);
			$('#client_IP').html(data.client_IP);
			$('#APName').html(data.APName);
			$('#AP_MAC').html(data.AP_MAC);
			$('#AP_IP').html(data.AP_IP);
			$('#bitRate').html(data.bitRate);
			$('#txPower').html(data.txPower);
			$('#beaconPeriod').html(data.beaconPeriod);
			$('#DTIM').html(data.DTIM);

			$("#progressbar").removeClass("progress-bar-danger progress-bar-warning progress-bar-success");
			var rssiWidth = rssiMeter.toString().concat("%");
			if (rssi == 0){ //Not connected
				$("#progressbar").addClass("progress-bar-danger");
				$("#progressbar").css('width',rssiWidth);
			} else if (rssi < -90){ //red
				$("#progressbar").addClass("progress-bar-danger");
				$("#progressbar").css('width',rssiWidth);
			} else if (rssi < -70){ //yellow
				$("#progressbar").addClass("progress-bar-warning");
				$("#progressbar").css('width',rssiWidth);
			} else { //green
				$("#progressbar").addClass("progress-bar-success");
				$("#progressbar").css('width',rssiWidth);
			}
			document.getElementById("progressbar").innerHTML = rssi + " dBm";
		}
	})
	.fail(function(data) {
		consoleLog(data);
		consoleLog("Failed to get status.php, retrying.");
		setIntervalUpdate(updateStatus);
	});
}

function setIntervalUpdate(functionName){
	if (!intervalId){
		intervalId = setInterval(functionName, 1000);
	} else {
		consoleLog("Interval already set");
	}
}

function clickStatusPage(retry) {
	if ($("#wifi_status").hasClass("active")){
		consoleLog("Status already active");
	} else {
		$("li").removeClass("active");
		$("#wifi_status").addClass("active");
		clearReturnData();
		$("#helpText").html("This page shows the current state of WiFi");
		$.ajax({
			url: "plugins/wifi/html/status.html",
			data: {},
			type: "GET",
			dataType: "html",
		})
		.done(function( data ) {
			$('#main_section').html(data);
			setIntervalUpdate(updateStatus);
		})
		.fail(function() {
			consoleLog("Error, couldn't get status.html.. retrying");
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			if (retry < 5){
				retry++;
				$("#wifi_status").removeClass("active");
				clickStatusPage(retry);
			} else {
				consoleLog("Retry max attempt reached");
			}
		});
	}
}

function submitProfile(retry){
	var profileData = {
		profileName: document.getElementById("profileName").value,
		SSID: document.getElementById("SSID").value,
		clientName: document.getElementById("clientName").value,
		txPower: parseInt($('#txSlider').val()),
		authType: parseInt(document.getElementById("authType").value),
		eapType: parseInt(document.getElementById("eapType").value),
		wepType: parseInt(document.getElementById("wepType").value),
		radioMode: parseInt(document.getElementById("radioMode").value),
		powerSave: parseInt(document.getElementById("powerSave").value),
		pspDelay: parseInt($('#pspDelaySlider').val()),
		wepIndex: parseInt(document.getElementById("wepIndex").value),
		index1: document.getElementById("index1").value,
		index2: document.getElementById("index2").value,
		index3: document.getElementById("index3").value,
		index4: document.getElementById("index4").value,
		psk: document.getElementById("psk").value,
		userName: document.getElementById("userName").value,
		passWord: document.getElementById("passWord").value,
		userCert: document.getElementById("userCert").value,
		userCertPassword: document.getElementById("userCertPassword").value,
		CACert: document.getElementById("CACert").value,
		PACFilename: document.getElementById("PACFilename").value,
		PACPassword: document.getElementById("PACPassword").value,
	}
	consoleLog(profileData);
	$.ajax({
		url: "plugins/wifi/php/setProfile.php",
		type: "POST",
		data: JSON.stringify(profileData),
		contentType: "application/json",
	})
	.done(function( msg ) {
		consoleLog(msg);
		SDCERRtoString(msg.SDCERR);
		$("#submitButton").addClass("disabled");
	})
	.fail(function() {
		consoleLog("Failed to get profile data, retrying");
		if (retry < 5){
			retry++;
			submitProfile(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function SelectedIndex(sel, val) {
	for(var i = 0, j = sel.options.length; i < j; ++i) {
		if(sel.options[i].value == val) {
		sel.selectedIndex = i;
		break;
		}
	}
}

function updateGetProfilePage(profileName,retry){
	var data = {
			profileName: profileName,
		}
	$.ajax({
		url: "plugins/wifi/php/getProfile.php",
		type: "POST",
		data: JSON.stringify(data),
		contentType: "application/json",
	})
	.done(function( msg ) {
		consoleLog(msg);
		document.getElementById("profileName").value = msg.configName;
		document.getElementById("SSID").value = msg.SSID;
		document.getElementById("clientName").value = msg.clientName;
		$('#txSlider').slider('setValue', msg.txPower);
		document.getElementById("authType").selectedIndex =  msg.authType;
		//If index does not start at 0 and run contiguously we must check against options value
		SelectedIndex(document.getElementById("wepType"),msg.wepType);
		SelectedIndex(document.getElementById("eapType"),msg.eapType);
		SelectedIndex(document.getElementById("radioMode"),msg.radioMode);
		document.getElementById("powerSave").selectedIndex =  msg.powerSave;
		if (msg.powerSave == defines.PLUGINS.wifi.POWERSAVE.POWERSAVE_FAST){
			$("#pspDelayDisplay").removeClass("hidden");
			$('#pspDelaySlider').slider('setValue', msg.pspDelay);
		}
		if (msg.wepType == defines.PLUGINS.wifi.WEPTYPE.WEP_ON){
			$("#wepIndexDisplay").removeClass("hidden");
			document.getElementById("wepIndex").selectedIndex =  msg.wepIndex - 1;
			$("#wepTypeOnDisplay").removeClass("hidden");
			document.getElementById("index1").value =  msg.WEPKey1;
			document.getElementById("index2").value =  msg.WEPKey2;
			document.getElementById("index3").value =  msg.WEPKey3;
			document.getElementById("index4").value =  msg.WEPKey4;
		} else if (msg.wepType == defines.PLUGINS.wifi.WEPTYPE.WPA_PSK || msg.wepType == defines.PLUGINS.wifi.WEPTYPE.WPA2_PSK || msg.wepType == defines.PLUGINS.wifi.WEPTYPE.WPA_PSK_AES || msg.wepType == defines.PLUGINS.wifi.WEPTYPE.WPA2_PSK_TKIP){
			$("#pskDisplay").removeClass("hidden");
			document.getElementById("psk").value =  msg.PSK;
		} else if (msg.eapType >= defines.PLUGINS.wifi.EAPTYPE.EAP_LEAP){
			$("#eapTypeDisplay").removeClass("hidden");
			$("#userNameDisplay").removeClass("hidden");
			document.getElementById("userName").value =  msg.userName;
			if (!(msg.eapType == defines.PLUGINS.wifi.EAPTYPE.EAP_EAPTLS || msg.eapType == defines.PLUGINS.wifi.EAPTYPE.EAP_PEAPTLS)){
				$("#passWordDisplay").removeClass("hidden");
				document.getElementById("passWord").value =  msg.passWord;
			} else {
				$("#userCertDisplay").removeClass("hidden");
				document.getElementById("userCert").value =  msg.userCert;
				$("#userCertPasswordDisplay").removeClass("hidden");
				document.getElementById("userCertPassword").value =  msg.userCertPassword;
			}
			if (msg.eapType > defines.PLUGINS.wifi.EAPTYPE.EAP_EAPFAST && msg.eapType < defines.PLUGINS.wifi.EAPTYPE.EAP_WAPI_CERT){
				$("#CACertDisplay").removeClass("hidden");
				document.getElementById("CACert").value =  msg.CACert;
			}
			if (msg.eapType == defines.PLUGINS.wifi.EAPTYPE.EAP_EAPFAST){
				$("#PACFilenameDisplay").removeClass("hidden");
				document.getElementById("PACFilename").value =  msg.PACFileName;
				$("#PACPasswordDisplay").removeClass("hidden");
				document.getElementById("PACPassword").value =  msg.PACPassword;
			}
		}
	})
	.fail(function() {
		consoleLog("Failed to get profile data, retrying");
		if (retry < 5){
			retry++;
			updateGetProfilePage(profileName,retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function selectedProfile(selectedProfile,retry){
	if(!selectedProfile){
		var selectedProfile = document.getElementById("profileSelect").value;
	}
	$.ajax({
		url: "plugins/wifi/html/getProfile.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$('#main_section').html(data);
			clearReturnData();
			$("#helpText").html("Adjust profile settings.");
		},
		error: function (xhr, status) {
			consoleLog("Error, couldn't get getProfile.html");
		},
	})
	.done(function( msg ) {
		updateGetProfilePage(selectedProfile,0);
		$('#txSlider').slider({
			formatter: function(value) {
				if (value != 0){
					return 'Current value: ' + value;
				} else {
					return 'Current value: Auto';
				}
			}
		});
		$('#pspDelaySlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
	})
	.fail(function() {
		consoleLog("Failed to get get Profile, retrying");
		if (retry < 5){
			retry++;
			selectedProfile(selectedProfile,retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function enableAutoProfileSubmit(){
	$("#applyAutoProfile").removeClass("disabled");
}

function updateSelectProfilePage(retry){
	$.ajax({
		url: "plugins/wifi/php/listProfile.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function( msg ) {
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		var x = document.getElementById("profileSelect");
		x.size = String(msg.NumConfigs);
		for (i = 0; i < msg.NumConfigs; i++) {
			var option = document.createElement("option");
			option.text = msg.profiles[i];
			x.add(option);
			if (msg.profiles[i] == msg.currentConfig){
				x.selectedIndex = i;
				option.id = "activeProfile";
				var helpText = document.getElementById("helpText").innerHTML;
				$("#helpText").html(helpText + " Profile " + msg.currentConfig + " is the active profile.");
			}
		}
		if (msg.autoProfiles){
			var table = document.getElementById("autoProfileTable").getElementsByTagName('tbody')[0];
			var row, cell1, cell2, checkbox,button,buttonText;
			for (var profile in msg.autoProfiles){
				row = table.insertRow(-1);
				cell1 = row.insertCell(0);
				cell2 = row.insertCell(1);
				checkbox = document.createElement('input');
				cell1.innerHTML = profile;
				checkbox.type = "checkbox";
				checkbox.name = profile;
				checkbox.value = profile;
				checkbox.setAttribute("onchange","enableAutoProfileSubmit()");
				if (msg.autoProfiles[profile] == true){
					checkbox.checked = true;
				}
				cell2.appendChild(checkbox);
			}
			row.style.borderBottom="1px solid rgb(211, 211, 211)";
			$("#activateButton").addClass("hidden");
			$("#autoProfileDisplay").removeClass("hidden");
		}
	})
	.fail(function() {
		consoleLog("Failed to get profiles, retrying");
		if (retry < 5){
			retry++;
			updateSelectProfilePage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function activateProfile(retry){
	var data = {
			profileName: document.getElementById("profileSelect").value,
	}
	consoleLog("Activating profile " + data.profileName);
	$.ajax({
		url: "plugins/wifi/php/activateProfile.php",
		type: "POST",
		data: JSON.stringify(data),
		contentType: "application/json",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
		},
		error: function (xhr, status) {
			consoleLog("Error, couldn't get activateProfile.php");
		},
	})
	.done(function( msg ) {
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(msg.SDCERR);
	})
	.fail(function() {
		consoleLog("Failed to activate profile, retrying");
		if (retry < 5){
			retry++;
			activateProfile(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function renameProfile(retry){
	var profileSelect = document.getElementById("profileSelect");
	var selectedProfile = profileSelect.options[profileSelect.selectedIndex].text;
	var profileName = {
			currentName: selectedProfile,
			newName: document.getElementById("newProfileName").value,
	}
	$.ajax({
		url: "plugins/wifi/php/renameProfile.php",
		type: "POST",
		data: JSON.stringify(profileName),
		contentType: "application/json",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
		},
		error: function (xhr, status) {
			consoleLog("Error, couldn't get renameProfile.php");
		},
	})
	.done(function( msg ) {
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(msg.SDCERR);
		if (document.getElementById("returnDataNav").innerHTML == "Success"){
			for(var i = 0; i < profileSelect.options.length; i++) {
				if (profileName.currentName == profileSelect.options[i].text){
					profileSelect.options[i].text = profileName.newName;
				}
			}
			$("#newProfileNameDisplay").addClass("hidden");
		}
	})
	.fail(function() {
		consoleLog("Failed to rename profile, retrying");
		if (retry < 5){
			retry++;
			renameProfile(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function showRenameProfile(){
	$("#newProfileNameDisplay").removeClass("hidden");
}

function removeProfile(){
	var profileSelect = document.getElementById("profileSelect");
	var activeProfile = document.getElementById("activeProfile");
	var oldProfile = {
		profileName: profileSelect.value
	}
	if (oldProfile.profileName != activeProfile.text){
		$.ajax({
			url: "plugins/wifi/php/removeProfile.php",
			type: "POST",
			data: JSON.stringify(oldProfile),
			contentType: "application/json",
		})
		.done(function( msg ) {
			if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
				expiredSession();
				return;
			}
			SDCERRtoString(msg.SDCERR);
			if (document.getElementById("returnDataNav").innerHTML == "Success"){
				for(var i = 0; i < profileSelect.options.length; i++) {
					if (oldProfile.profileName == profileSelect.options[i].text){
						profileSelect.options.remove(i);
						profileSelect.size = profileSelect.size - 1;
					}
				}
			}
		});
	} else {
		CustomErrMsg("Cant delete active profile");
	}
};

function submitAutoProfile(retry){
	var rows,index,profile,value;
	autoProfileList = {};
	rows = document.getElementById("autoProfileTable").rows;
	for (index = 1; index < rows.length; index++){
		profile = rows[index].cells[0].innerHTML;
		value = rows[index].cells[1].children[0].checked;
		autoProfileList[profile] = value;
	}
	consoleLog(autoProfileList);
	$.ajax({
		url: "plugins/wifi/php/setAutoProfileList.php",
		type: "POST",
		data: JSON.stringify(autoProfileList),
		contentType: "application/json",
	})
	.done(function( msg ) {
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(msg.SDCERR);
		$("#applyAutoProfile").addClass("disabled");
	});
}

function clickProfileEditPage(retry) {
	$.ajax({
		url: "plugins/wifi/html/selectProfile.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$("li").removeClass("active");
			$("#wifi_edit").addClass("active");
			$('#main_section').html(data);
			clearReturnData();
			$("#helpText").html("These are the current WiFi profiles.");
			setTimeout(updateSelectProfilePage(retry),1000);
		},
	})
	.fail(function() {
		consoleLog("Error, couldn't get selectProfile.html.. retrying");
		if (retry < 5){
			retry++;
			clickProfileEditPage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function clickAddProfilePage(retry) {
	$.ajax({
		url: "plugins/wifi/html/addProfile.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$('#main_section').html(data);
			clearReturnData();
			$("li").removeClass("active");
			$("#wifi_Add").addClass("active");
			$("#helpText").html("Enter the name of the profile you would like to add.");
			$('#txSlider').slider({
				formatter: function(value) {
					if (value != 0){
						return 'Current value: ' + value;
					} else {
						return 'Current value: Auto';
					}
				}
			});
			$('#pspDelaySlider').slider({
				formatter: function(value) {
					return 'Current value: ' + value;
				}
			});
		},
	})
	.fail(function() {
		consoleLog("Error, couldn't get addProfile.html.. retrying");
		if (retry < 5){
			retry++;
			clickAddProfilePage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function updateAddProfileSlider(){
	//https://github.com/seiyria/bootstrap-slider/issues/523
	setTimeout(function() {
		$('#pspDelaySlider').slider("relayout");
		$('#txSlider').slider("relayout");
	}, 500);
}

function addProfile(){
	if (profileName != ""){
		var newProfile = {
			profileName: document.getElementById("profileName").value,
			SSID: document.getElementById("SSID").value,
			clientName: document.getElementById("clientName").value,
			txPower: parseInt($('#txSlider').val()),
			authType: parseInt(document.getElementById("authType").value),
			eapType: parseInt(document.getElementById("eapType").value),
			wepType: parseInt(document.getElementById("wepType").value),
			radioMode: parseInt(document.getElementById("radioMode").value),
			powerSave: parseInt(document.getElementById("powerSave").value),
			pspDelay: parseInt($('#pspDelaySlider').val()),
			wepIndex: parseInt(document.getElementById("wepIndex").value),
			index1: document.getElementById("index1").value,
			index2: document.getElementById("index2").value,
			index3: document.getElementById("index3").value,
			index4: document.getElementById("index4").value,
			psk: document.getElementById("psk").value,
			userName: document.getElementById("userName").value,
			passWord: document.getElementById("passWord").value,
			userCert: document.getElementById("userCert").value,
			userCertPassword: document.getElementById("userCertPassword").value,
			CACert: document.getElementById("CACert").value,
			PACFilename: document.getElementById("PACFilename").value,
			PACPassword: document.getElementById("PACPassword").value,
		}
		consoleLog(newProfile);
		$.ajax({
			url: "plugins/wifi/php/addProfile.php",
			type: "POST",
			data: JSON.stringify(newProfile),
			contentType: "application/json",
		})
		.done(function( msg ) {
			consoleLog(msg);
			consoleLog(msg.SDCERR);
			if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
				expiredSession();
				return;
			}
			SDCERRtoString(msg.SDCERR);
		});
	} else {
		consoleLog("Name is null");
	}
};

function getSupportedGlobals(retry){
	$.ajax({
		url: "plugins/wifi/php/supportedGlobals.php",
		type: "GET",
		contentType: "application/json",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			consoleLog(data);
			if (data.SESSION == defines.SDCERR.SDCERR_FAIL){
				expiredSession();
				return;
			}
			for (var key in data) {
				if (data[key] != 0 && key != "SDCERR"){
					$("#" + key + "Display").removeClass("hidden");
				}
				$("#submitButton").removeClass("hidden");
			}
		},
	})
	.fail(function() {
		consoleLog("Error, couldn't get supportedGlobals.php.. retrying");
		if (retry < 5){
			retry++;
			getSupportedGlobals(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function isSlider(id){
	switch(id){
		case "BeaconMissTimeout":
		case "fragThreshold":
		case "probeDelay":
		case "roamPeriodms":
		case "roamTrigger":
		case "RTSThreshold":
		case "scanDFSTime":
			return true;
			break;
		default:
			return false
			break;
	}
}

function getGlobals(retry){
	$.ajax({
		url: "plugins/wifi/php/getGlobals.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function(msg) {
		if (intervalId){
			clearInterval(intervalId);
			intervalId = 0;
		}
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		for (var key in msg) {
			if (key != "SDCERR" && key != "SESSION"){
				if (isSlider(key)){
					$("#" + key + "Slider").slider('setValue', msg[key]);
				} else{
					if (key != "suppInfo"){
						if (key == "aLRS"){
							for (var aChannel in msg[key]){
								document.getElementById("channel" + msg[key][aChannel]).checked = true;
							}
						}else if(key == "bLRS"){
							for (var bChannel in msg[key]){
								document.getElementById("channel" + msg[key][bChannel]).checked = true;
							}
						}else if(key == "fipsStatus"){
							switch (msg[key])
							{
								case 0: //FIPS_INACTIVE
									document.getElementById("fipsStatus").innerHTML = "FIPS disabled and inactive:";
									break;
								case 1: //FIPS_INACTIVE_ENABLED
									document.getElementById("fipsStatus").innerHTML =  "FIPS Mode inactive, enabled on next start:";
									break;
								case 2: //FIPS_ACTIVE_DISABLED
									document.getElementById("fipsStatus").innerHTML =  "FIPS Mode active, disabled on next start:";
									break;
								case 3: //FIPS_ACTIVE
									document.getElementById("fipsStatus").innerHTML =  "FIPS Mode enabled and active:";
									break;
								default:
									break;
							}
						}else{
							document.getElementById(key).value = msg[key];
						}
					}
				}
			}
		}
	})
	.fail(function() {
		consoleLog("Error, couldn't get getGlobals.php.. retrying");
		if (retry < 5){
			retry++;
			getGlobals(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function clickGlobalsPage(retry){
	$.ajax({
		url: "plugins/wifi/html/getGlobals.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$('#main_section').html(data);
			clearReturnData();
			$("li").removeClass("active");
			$("#wifi_global").addClass("active");
			$("#helpText").html("Here are the global configuration options for WiFi");
		},
	})
	.done(function( data ) {
		getGlobals(0);
		getSupportedGlobals(0);
		$('#BeaconMissTimeoutSlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
		$('#fragThresholdSlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
		$('#probeDelaySlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
		$('#roamPeriodmsSlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
		$('#roamTriggerSlider').slider({
			formatter: function(value) {
				return 'Current value: -' + value;
			}
		});
		$('#RTSThresholdSlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
		$('#scanDFSTimeSlider').slider({
			formatter: function(value) {
				return 'Current value: ' + value;
			}
		});
		//https://github.com/seiyria/bootstrap-slider/issues/523
		setTimeout(function() {
			$('#BeaconMissTimeoutSlider').slider("relayout");
			$('#fragThresholdSlider').slider("relayout");
			$('#probeDelaySlider').slider("relayout");
			$('#roamPeriodmsSlider').slider("relayout");
			$('#roamTriggerSlider').slider("relayout");
			$('#RTSThresholdSlider').slider("relayout");
			$('#scanDFSTimeSlider').slider("relayout");
		}, 500);
	})
	.fail(function() {
		consoleLog("Error, couldn't get getGlobals.html.. retrying");
		if (retry < 5){
			retry++;
			clickGlobalsPage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function submitGlobals(retry){
	var totalAChannelValue = 0;
	AchannelArray = [36,40,44,48,52,56,60,64,100,104,108,112,116,120,124,128,132,136,140,149,153,157,161,165];
	for (var channel in AchannelArray){
		if(document.getElementById('channel' + AchannelArray[channel]).checked){
			totalAChannelValue += parseInt(document.getElementById('channel' + AchannelArray[channel]).value);
		}
	}
	var totalBGChannelValue = 0;
	for (j = 1; j <= 14; j++){
		if(document.getElementById('channel' + j).checked){
			totalBGChannelValue += parseInt(document.getElementById('channel' + j).value);
		}
	}
	var globalData = {
		aLRS: totalAChannelValue,
		authServerType: document.getElementById("authServerType").value,
		autoProfile: document.getElementById("autoProfile").value,
		bLRS: totalBGChannelValue,
		BeaconMissTimeout: parseInt($('#BeaconMissTimeoutSlider').val()),
		BTcoexist: document.getElementById("BTcoexist").value,
		CCXfeatures: document.getElementById("CCXfeatures").value,
		certPath: document.getElementById("certPath").value,
// 				suppInfo: document.getElementById("suppInfo").value,
		defAdhocChannel: document.getElementById("defAdhocChannel").value,
		DFSchannels: document.getElementById("DFSchannels").value,
		fragThreshold: parseInt($('#fragThresholdSlider').val()),
		PMKcaching: document.getElementById("PMKcaching").value,
		probeDelay: parseInt($('#probeDelaySlider').val()),
		regDomain: document.getElementById("regDomain").value,
		roamPeriodms: parseInt($('#roamPeriodmsSlider').val()),
		roamTrigger: parseInt($('#roamTriggerSlider').val()),
		RTSThreshold: parseInt($('#RTSThresholdSlider').val()),
		scanDFSTime: parseInt($('#scanDFSTimeSlider').val()),
		TTLSInnerMethod: document.getElementById("TTLSInnerMethod").value,
		uAPSD: document.getElementById("uAPSD").value,
		WMEenabled: document.getElementById("WMEenabled").value,
		suppInfoDateCheck: document.getElementById("suppInfoDateCheck").value,
		fips: document.getElementById("fips").value,
	}
	consoleLog(globalData);
	$.ajax({
		url: "plugins/wifi/php/setGlobals.php",
		type: "POST",
		data: JSON.stringify(globalData),
		contentType: "application/json",
	})
	.done(function( msg ) {
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(msg.SDCERR);
		$("#submitButton").addClass("disabled");
	})
	.fail(function() {
		consoleLog("Failed to send global data, retrying");
		if (retry < 5){
			retry++;
			submitGlobals(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}
function scanToProfile(){
	var profileName = document.getElementById("profileNameHidden").value;
	selectedProfile(profileName,0);
	$("li").removeClass("active");
	$("#submenuEdit").addClass("active");

}

function addScanProfile(){
	var newProfile = {
		profileName: document.getElementById("profileName").value,
		SSID: document.getElementById("newSSID").value,
		wepType: document.getElementById("security").value,
	}
	if (newProfile.profileName == ""){
		$("#profileNameDisplay").addClass("has-error");
		$("#profileName").popover({content:'Please enter profile name',placement:'bottom'});
		$("#profileName").popover('show')
		return;
	}
	$.ajax({
		url: "plugins/wifi/php/addProfile.php",
		type: "POST",
		data: JSON.stringify(newProfile),
		contentType: "application/json",
	})
	.done(function( msg ) {
		consoleLog(msg);
		consoleLog(msg.SDCERR);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(msg.SDCERR);
		if (document.getElementById("returnDataNav").innerHTML == "Success"){
			document.getElementById("goToProfile").textContent = "Edit Profile " + newProfile.profileName;
			$("#goToProfileDisplay").removeClass("hidden");
			document.getElementById("profileNameHidden").value = newProfile.profileName;
			document.getElementById("addTable").reset();
		}
	});
}

function allowDrop(ev){
	ev.preventDefault();
}

function drag(ev){
	var table = document.getElementById("scanTable");
	var index = $(ev.currentTarget).index() + 1;
	if (index > 1){
		ev.dataTransfer.setData("ssid", table.rows[index].cells[0].innerHTML);
		ev.dataTransfer.setData("security",table.rows[index].cells[4].innerHTML);
	}
}

function drop(ev){
	ev.preventDefault();
	var table = document.getElementById("addTable");
	document.getElementById("newSSID").value = ev.dataTransfer.getData("ssid");
	document.getElementById("security").value = ev.dataTransfer.getData("security");
	$("#profileNameDisplay").removeClass("has-error");
	$("#addScanDisplay").removeClass("hidden");
}

function getScan(retry){
	$.ajax({
		url: "plugins/wifi/php/scan.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function(msg) {
		if (intervalId){
			clearInterval(intervalId);
			intervalId = 0;
		}
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		if (msg.SDCERR == 12){
			$("#updateProgressDisplay").addClass("hidden");
			$("#status-hardware").removeClass("hidden");
		} else {
			var table = document.getElementById("scanTable");
			for (var scanItem in msg["scanList"]){
				var row = table.insertRow(-1);
				row.setAttribute('draggable', true);
				row.setAttribute('ondragstart', 'drag(event)');
				var cell0 = row.insertCell(0);
				var cell1 = row.insertCell(1);
				var cell2 = row.insertCell(2);
				var cell3 = row.insertCell(3);
				var cell4 = row.insertCell(4);
				cell0.innerHTML = msg["scanList"][scanItem].SSID;
				cell1.innerHTML = msg["scanList"][scanItem].BSSID;
				cell2.innerHTML = msg["scanList"][scanItem].channel;
				cell3.innerHTML = msg["scanList"][scanItem].RSSI;
				cell4.innerHTML = msg["scanList"][scanItem].security[0];
				row.onclick=function(){
					document.getElementById("newSSID").value = this.cells[0].innerHTML;
					document.getElementById("security").value = this.cells[4].innerHTML;
					$("#profileNameDisplay").removeClass("has-error");
					$("#addScanDisplay").removeClass("hidden");
				};
			}
			$("#updateProgressDisplay").addClass("hidden");
			$("#scanTableDisplay").removeClass("hidden");
			$(function(){
				$("#scanTable").tablesorter( {sortList: [[0,0], [1,0]]} );
			});
		}
	})
	.fail(function() {
		consoleLog("Error, couldn't get scan.php.. retrying");
		if (retry < 5){
			retry++;
			getScan(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function clickScanPage(retry){
	$.ajax({
		url: "plugins/wifi/html/scan.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$('#main_section').html(data);
			clearReturnData();
			$("li").removeClass("active");
			$("#wifi_scan").addClass("active");
			$("#helpText").html("Scan for wireless networks");
		},
	})
	.done(function( data ) {
		getScan(0);
	})
	.fail(function() {
		consoleLog("Error, couldn't get scan.html.. retrying");
		if (retry < 5){
			retry++;
			clickScanPage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function uploadCert(retry){
	var file_data = $('#fileToUpload').prop('files')[0];
	var form_data = new FormData();
	form_data.append('file', file_data);
	$.ajax({
		url: 'plugins/wifi/php/upload.php', // point to server-side PHP script
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,
		type: 'POST',
	})
	.done(function( data ) {
		consoleLog(data);
		SDCERRtoString(data.SDCERR);
	})
}

function generateLog(retry){
	$.ajax({
		url: "plugins/wifi/php/generateLog.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function(msg) {
		if (intervalId){
			clearInterval(intervalId);
			intervalId = 0;
		}
		setIntervalUpdate(checkLog);
	})
	.fail(function() {
		consoleLog("Error, couldn't get generateLog.php.. retrying");
		if (retry < 5){
			retry++;
			generateLog(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function removeLog(retry){
	$.ajax({
		url: "plugins/wifi/php/removeLog.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function(msg) {
		if (intervalId){
			clearInterval(intervalId);
			intervalId = 0;
		}
		document.getElementById("generateLog").textContent = "Generate";
		$("#generateLog").removeClass("disabled");
		$("#downloadLog").addClass("disabled");
	})
	.fail(function() {
		consoleLog("Error, couldn't get removeLog.php.. retrying");
		if (retry < 5){
			retry++;
			removeLog(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function checkLog(){
	var getStatusJSON = $.getJSON( "plugins/wifi/php/checkLog.php", function( data ) {
		if (data.state == "finished"){
			$("#generateLog").addClass("disabled");
			$("#downloadLog").removeClass("disabled");
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
		} else if (data.state == "stopped"){
			$("#generateLog").removeClass("disabled");
			$("#downloadLog").addClass("disabled");
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
		} else if (data.state == "running"){
			document.getElementById("generateLog").textContent = "Generating";
			$("#generateLog").addClass("disabled");
			$("#downloadLog").addClass("disabled");
		}
	})
	.fail(function(data) {
		consoleLog("Error, couldn't get checkLog.php.. retrying");
		setIntervalUpdate(checkLog);
	});
}

function submitAdvanced(retry){
	var AdvancedData = {
		suppDebugLevel: parseInt(document.getElementById("suppDebugLevel").value),
		driverDebugLevel: parseInt(document.getElementById("driverDebugLevel").value),
	}
	$.ajax({
		url: "plugins/wifi/php/setAdvanced.php",
		type: "POST",
		data: JSON.stringify(AdvancedData),
		contentType: "application/json",
	})
	.done(function( msg ) {
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(msg.SDCERR);
		$("#submitButton").addClass("disabled");
	})
	.fail(function() {
		consoleLog("Failed to send advanced data, retrying");
		if (retry < 5){
			retry++;
			submitAdvanced(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function getAdvanced(retry){
	$.ajax({
		url: "plugins/wifi/php/getAdvanced.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function(msg) {
		if (intervalId){
			clearInterval(intervalId);
			intervalId = 0;
		}
		consoleLog(msg);
		if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		document.getElementById("suppDebugLevel").value = msg.suppDebugLevel;
		document.getElementById("driverDebugLevel").value = msg.driverDebugLevel;
		setIntervalUpdate(checkLog);
	})
	.fail(function() {
		consoleLog("Error, couldn't get getAdvanced.php.. retrying");
		if (retry < 5){
			retry++;
			getAdvanced(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function clickAdvancedPage(retry){
	$.ajax({
		url: "plugins/wifi/html/advanced.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$('#main_section').html(data);
			clearReturnData();
			$("li").removeClass("active");
			$("#wifi_advanced").addClass("active");
			$("#helpText").html("Advanced configuration options");
		},
	})
	.done(function( data ) {
		$('input[type=file]').bootstrapFileInput();
		$('.file-inputs').bootstrapFileInput();
		getAdvanced(0);
	})
	.fail(function() {
		consoleLog("Error, couldn't get advanced.html.. retrying");
		if (retry < 5){
			retry++;
			clickAdvancedPage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function getVersion(retry){
	$.ajax({
		url: "plugins/wifi/php/version.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function(msg) {
		if (intervalId){
			clearInterval(intervalId);
			intervalId = 0;
		}
		consoleLog(msg);
		document.getElementById("sdk").innerHTML = msg.sdk;
		document.getElementById("chipset").innerHTML = msg.chipset;
		document.getElementById("driver").innerHTML = msg.driver;
		document.getElementById("firmware").innerHTML = msg.firmware;
		document.getElementById("supplicant").innerHTML = msg.supplicant;
		document.getElementById("php_sdk").innerHTML = msg.php_sdk;
		document.getElementById("build").innerHTML = msg.build;
	})
	.fail(function() {
		consoleLog("Error, couldn't get version.php.. retrying");
		if (retry < 5){
			retry++;
			getVersion(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function clickVersionPage(retry){
	$.ajax({
		url: "plugins/wifi/html/version.html",
		data: {},
		type: "GET",
		dataType: "html",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
			$('#main_section').html(data);
			clearReturnData();
			$("li").removeClass("active");
			$("#wifi_version").addClass("active");
			$("#helpText").html("System version information");
		},
	})
	.done(function( data ) {
		getVersion(0);
	})
	.fail(function() {
		consoleLog("Error, couldn't get version.html.. retrying");
		if (retry < 5){
			retry++;
			clickGlobalsPage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}
