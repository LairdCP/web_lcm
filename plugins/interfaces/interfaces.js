// Copyright (c) 2016, Laird
// Contact: ews-support@lairdtech.com

function interfacesAUTORUN(retry){
	return;
}

function modifyInterface(option,retry){
	var InterfaceSelect = document.getElementById("InterfaceSelect");
	var InterfaceData = {
		interfaceName: document.getElementById("newInterfaceName").value,
		option: option,
	}
	if (option == "remove"){
		InterfaceData.interfaceName = InterfaceSelect.value;
	}
	if (InterfaceData.interfaceName != ""){
		$.ajax({
			url: "plugins/interfaces/php/setInterfaces.php",
			type: "POST",
			data: JSON.stringify(InterfaceData),
			contentType: "application/json",
		})
		.done(function( msg ) {
			consoleLog(msg);
			if (msg.SESSION == defines.SDCERR.SDCERR_FAIL){
				expiredSession();
				return;
			}
			SDCERRtoString(msg.SDCERR);
			if (document.getElementById("returnDataNav").innerHTML == "Success"){
				if (option == "add"){
					$("#newInterfaceNameDisplay").addClass("hidden");
					var newOption = document.createElement("option");
					newOption.text = InterfaceData.interfaceName;
					InterfaceSelect.add(newOption);
					InterfaceSelect.size = InterfaceSelect.size + 1;
				} else if (option == "remove"){
					for(var i = 0; i < InterfaceSelect.options.length; i++) {
						if (InterfaceData.interfaceName == InterfaceSelect.options[i].text){
							InterfaceSelect.options.remove(i);
							InterfaceSelect.size = InterfaceSelect.size - 1;
						}
					}
				}
			}
		})
		.fail(function() {
			consoleLog("Failed to set interface data, retrying");
			if (retry < 5){
				retry++;
				modifyInterface(option,retry)
			} else {
				consoleLog("Retry max attempt reached");
			}
		});
	}
}

function showAddInterface(){
	$("#newInterfaceNameDisplay").removeClass("hidden");
}

function submitInterface(retry){
	var InterfaceData = {
		interfaceName: document.getElementById("interfaceName").value,
		auto: document.getElementById("auto").value,
		IPv4: {
			state: document.getElementById("stateIPv4").value,
			method: document.getElementById("method").value,
			address: document.getElementById("address").value,
			netmask: document.getElementById("netmask").value,
			gateway: document.getElementById("gateway").value,
			broadcast: document.getElementById("broadcast").value,
			nameserver_1: document.getElementById("nameserver_1").value,
			nameserver_2: document.getElementById("nameserver_2").value,
		},
		IPv6: {
			state: document.getElementById("stateIPv6").value,
			method: document.getElementById("method6").value,
			address: document.getElementById("address6").value,
			netmask: document.getElementById("netmask6").value,
			gateway: document.getElementById("gateway6").value,
			nameserver_1: document.getElementById("nameserver6_1").value,
			nameserver_2: document.getElementById("nameserver6_2").value,
			dhcp: document.getElementById("DHCP6").value,
		},
	}
	consoleLog(InterfaceData);
	$.ajax({
		url: "plugins/interfaces/php/setInterfaces.php",
		type: "POST",
		data: JSON.stringify(InterfaceData),
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
		consoleLog("Failed to set interface data, retrying");
		if (retry < 5){
			retry++;
			submitInterface(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function displayDHCPv4(method){
	switch (method){
		case "dhcp":
			$("#addressDisplay").addClass("hidden");
			$("#netmaskDisplay").addClass("hidden");
			$("#gatewayDisplay").addClass("hidden");
			$("#broadcastDisplay").addClass("hidden");
			$("#nameserver_1Display").addClass("hidden");
			$("#nameserver_2Display").addClass("hidden");
			break;
		case "static":
		case "manual":
			$("#addressDisplay").removeClass("hidden");
			$("#netmaskDisplay").removeClass("hidden");
			$("#gatewayDisplay").removeClass("hidden");
			$("#broadcastDisplay").removeClass("hidden");
			$("#nameserver_1Display").removeClass("hidden");
			$("#nameserver_2Display").removeClass("hidden");
			break;
		default:
			break;
	}
}

function updateGetInterfacePage(interfaceName,retry){
	$.ajax({
		url: "plugins/interfaces/php/getInterfaces.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function( data ) {
		consoleLog(data);
		if (data.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		for (var iface in data.Interfaces){
			if (iface == interfaceName){
				//Hide IPv6 for bridge interface
				if (interfaceName == "br0"){
					$("#IPv6List").addClass("hidden");
				}
				document.getElementById("interfaceName").value = iface;
				if (data.AutoInterfaces[iface]){
					document.getElementById("auto").selectedIndex = 1;
				}
				//IPv4
				if (data.Interfaces[iface].IPv4.state){
					document.getElementById("stateIPv4").value = data.Interfaces[iface].IPv4.state;
				}
				displayDHCPv4(data.Interfaces[iface].IPv4.inet);
				switch (data.Interfaces[iface].IPv4.inet){
					case "dhcp":
						document.getElementById("method").selectedIndex = 0;
						break;
					case "static":
						document.getElementById("method").selectedIndex = 1;
						break;
					case "manual":
						document.getElementById("method").selectedIndex = 2;
						break;
					default:
						break;
				}
				if (data.Interfaces[iface].IPv4.address){
					document.getElementById("address").value = data.Interfaces[iface].IPv4.address;
				}
				if (data.Interfaces[iface].IPv4.netmask){
					document.getElementById("netmask").value = data.Interfaces[iface].IPv4.netmask;
				}
				if (data.Interfaces[iface].IPv4.gateway){
					document.getElementById("gateway").value = data.Interfaces[iface].IPv4.gateway;
				}
				if (data.Interfaces[iface].IPv4.broadcast){
					document.getElementById("broadcast").value = data.Interfaces[iface].IPv4.broadcast;
				}
				if (data.Interfaces[iface].IPv4.nameserver_1){
					document.getElementById("nameserver_1").value = data.Interfaces[iface].IPv4.nameserver_1;
				}
				if (data.Interfaces[iface].IPv4.nameserver_2){
					document.getElementById("nameserver_2").value = data.Interfaces[iface].IPv4.nameserver_2;
				}
				//IPv6
				if (data.Interfaces[iface].IPv6.state){
					document.getElementById("stateIPv6").value = data.Interfaces[iface].IPv6.state;
				}
				switch (data.Interfaces[iface].IPv6.inet6){
					case "dhcp":
						document.getElementById("method6").selectedIndex = 0;
						break;
					case "auto":
						document.getElementById("method6").selectedIndex = 1;
						break;
					case "static":
						document.getElementById("method6").selectedIndex = 2;
						break;
					default:
						break;
				}
				switch (data.Interfaces[iface].IPv6.dhcp){
					case "stateless":
						document.getElementById("DHCP6").selectedIndex = 0;
						break;
					case "slaac":
						document.getElementById("DHCP6").selectedIndex = 1;
						break;
					default:
						break;
				}
				if (data.Interfaces[iface].IPv6.address){
					document.getElementById("address6").value = data.Interfaces[iface].IPv6.address;
				}
				if (data.Interfaces[iface].IPv6.netmask){
					document.getElementById("netmask6").value = data.Interfaces[iface].IPv6.netmask;
				}
				if (data.Interfaces[iface].IPv6.gateway){
					document.getElementById("gateway6").value = data.Interfaces[iface].IPv6.gateway;
				}
				if (data.Interfaces[iface].IPv6.nameserver_1){
					document.getElementById("nameserver6_1").value = data.Interfaces[iface].IPv6.nameserver_1;
				}
				if (data.Interfaces[iface].IPv6.nameserver_2){
					document.getElementById("nameserver6_2").value = data.Interfaces[iface].IPv6.nameserver_2;
				}
			}
		}
	})
	.fail(function() {
		consoleLog("Failed to get interface data, retrying");
		if (retry < 5){
			retry++;
			updateGetInterfacePage(interfaceName,retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function selectedInterface(retry){
	var selectedInterface = document.getElementById("InterfaceSelect").value;
	if (!selectedInterface){
		return;
	}
	$.ajax({
		url: "plugins/interfaces/html/getInterface.html",
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
			$("#helpText").html("Adjust interface settings.");
		},
		error: function (xhr, status) {
			consoleLog("Error, couldn't get getInterface.html");
		},
	})
	.done(function( msg ) {
		updateGetInterfacePage(selectedInterface,0);
	})
	.fail(function() {
		consoleLog("Failed to get get interface, retrying");
		if (retry < 5){
			retry++;
			selectedInterface(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function setInterfaceState(ev,retry){
	var actionFromID = ev.target.id.split("-");
	var InterfaceData = {
		interface: actionFromID[0],
		action: ev.target.value.toLowerCase(),
	}
	if (!$("#" + ev.target.id).hasClass("disabled")){
		$("#" + ev.target.id).val("Working");
		$.ajax({
			url: "plugins/interfaces/php/setInterfaceState.php",
			type: "POST",
			data: JSON.stringify(InterfaceData),
			contentType: "application/json",
			success: function (data) {
				if (intervalId){
					clearInterval(intervalId);
					intervalId = 0;
				}
			},
		})
		.done(function( data ) {
			consoleLog(data);
			if (data.SESSION == defines.SDCERR.SDCERR_FAIL){
				expiredSession();
				return;
			}
			SDCERRtoString(data.SDCERR);
			if (document.getElementById("returnDataNav").innerHTML == "Success"){
				var startID = $("#" + actionFromID[0] + "-Start");
				var stopID = $("#" + actionFromID[0] + "-Stop");
				if (actionFromID[1] == "Start"){
					startID.val("Start");
					startID.addClass("disabled active");
					stopID.removeClass("disabled active");
				} else {
					stopID.val("Stop");
					stopID.addClass("disabled active");
					startID.removeClass("disabled active");
				}
			}
		})
		.fail(function() {
			consoleLog("Error, couldn't get setInterfaceState.php.. retrying");
			if (retry < 5){
				retry++;
				setInterfaceState(ev,retry);
			} else {
				consoleLog("Retry max attempt reached");
			}
		});
	}
}

function onChangeSPLInterface(mode){
	var nat6 = document.getElementById("SpecialInterfaceNAT6");
	var bridge = document.getElementById("SpecialInterfaceBridge");

	switch (mode){
		case "None_IPv4":
			nat6.disabled = false;
			break;
		case "Bridge":
			nat6.disabled = true;
			break;
		case "NAT_IPv4":
			nat6.disabled = false;
			break;
		case "None_IPv6":
			bridge.disabled = false;
			break;
		case "NAT_IPv6":
			bridge.disabled = true;
			break;
		default:
			break;
	}
}

function modifySpecialInterface(retry){
	var submitButton = $("#submitButton");
	var SpecialState_IPv4,int_1_IPv4,int_2_IPv4,previousNAT_IPv4;
	var SpecialState_IPv6,int_1_IPv6,int_2_IPv6,previousNAT_IPv6;

	if (document.getElementById("SpecialInterfaceNone").checked){
		SpecialState_IPv4 = document.getElementById("SpecialInterfaceNone").value;
	} else if (document.getElementById("SpecialInterfaceBridge").checked){
		SpecialState_IPv4 = document.getElementById("SpecialInterfaceBridge").value;
	} else if (document.getElementById("SpecialInterfaceNAT").checked){
		SpecialState_IPv4 = document.getElementById("SpecialInterfaceNAT").value;
	}

	int_1_IPv4 = document.getElementById("br_port_1").value;
	int_2_IPv4 = document.getElementById("br_port_2").value;
	previousNAT_IPv4 = document.getElementById("submitButton").getAttribute("data-previous-nat-ipv4");

	if (document.getElementById("SpecialInterfaceNone6").checked){
		SpecialState_IPv6 = document.getElementById("SpecialInterfaceNone6").value;
	} else if (document.getElementById("SpecialInterfaceNAT6").checked){
		SpecialState_IPv6 = document.getElementById("SpecialInterfaceNAT6").value;
	}

	int_1_IPv6 = document.getElementById("br_port_1_IPv6").value;
	int_2_IPv6 = document.getElementById("br_port_2_IPv6").value;
	previousNAT_IPv6 = document.getElementById("submitButton").getAttribute("data-previous-nat-ipv6");

	var PreviousButtonText = submitButton.text();
	submitButton.text("Working");
	submitButton.addClass("disabled");

	var InterfaceData = {
		IPv4: {
			state: SpecialState_IPv4,
			int_1: int_1_IPv4,
			int_2: int_2_IPv4,
			previousNAT: previousNAT_IPv4,
		},
		IPv6: {
			state: SpecialState_IPv6,
			int_1: int_1_IPv6,
			int_2: int_2_IPv6,
			previousNAT: previousNAT_IPv6,
		},
	}
	$.ajax({
		url: "plugins/interfaces/php/setSpecialInterfaces.php",
		type: "POST",
		data: JSON.stringify(InterfaceData),
		contentType: "application/json",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
		},
	})
	.done(function( data ) {
		submitButton.text(PreviousButtonText);
		if (data.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		SDCERRtoString(data.SDCERR);
		if (data.SDCERR == defines.SDCERR.SDCERR_SUCCESS){
			if (InterfaceData.IPv4.state == "NAT"){
				document.getElementById("submitButton").setAttribute("data-previous-nat-ipv4", InterfaceData.IPv4.int_2);
			}
			if (InterfaceData.IPv6.state == "NAT"){
				document.getElementById("submitButton").setAttribute("data-previous-nat-ipv6", InterfaceData.IPv6.int_2);
			}
		}

	})
	.fail(function() {
		consoleLog("Error, couldn't get setSpecialInterfaces.php.. retrying");
		if (retry < 5){
			retry++;
			modifySpecialInterface(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function updateSelectInterfacePage(retry){
	$.ajax({
		url: "plugins/interfaces/php/getInterfaces.php",
		data: {},
		type: "POST",
		contentType: "application/json",
		success: function (data) {
			if (intervalId){
				clearInterval(intervalId);
				intervalId = 0;
			}
		},
	})
	.done(function( data ) {
		consoleLog(data);
		if (data.SESSION == defines.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		var isBridged = false;
		var isNAT = false;
		var isNAT6 = false;
		var x = document.getElementById("InterfaceSelect");
		x.size = Object.keys(data.Interfaces).length;
		var table = document.getElementById("interfaceStateTable").getElementsByTagName('tbody')[0];
		var row, cell1, cell2,cell3,startBtn,length,found,index = 1;
		for (var iface in data.Interfaces) {
			//Interface Select options
			var option = document.createElement("option");
			option.text = iface;
			x.add(option);

			//Special Interface setInterface
			if (iface == "br0"){
				for (var iface_num in data.InterfaceState){
					if (data.InterfaceState[iface_num] == iface){
						isBridged = true;
					}
				}
			}
			if (data.Interfaces[iface].IPv4["post-cfg-do"] && data.Interfaces[iface].IPv4["pre-dcfg-do"]){
				isNAT = iface;
			}

			if (data.Interfaces[iface].IPv6["post-cfg6-do"] && data.Interfaces[iface].IPv6["pre-dcfg6-do"] && data.Interfaces[iface].IPv6["state"] == "1"){
				isNAT6 = iface;
			}

			//Interface State table
			row = table.insertRow(-1);
			cell1 = row.insertCell(0);
			cell2 = row.insertCell(1);
			cell3 = row.insertCell(2);
			cell1.innerHTML = iface;
			startBtn = document.createElement('input');
			startBtn.type = "button";
			startBtn.className = "btn btn-default";
			startBtn.value = "Start";
			startBtn.id = iface + "-" + startBtn.value;
			startBtn.onclick=function(event){setInterfaceState(event,0)};
			stopBtn = document.createElement('input');
			stopBtn.type = "button";
			stopBtn.className = "btn btn-default";
			stopBtn.value = "Stop";
			stopBtn.id = iface + "-" + stopBtn.value;
			stopBtn.onclick=function(event){setInterfaceState(event,0)};
			cell2.appendChild(startBtn);
			cell3.appendChild(stopBtn);
			for (var ifaceState in data.InterfaceState) {
				if (data.InterfaceState[ifaceState] == iface){
					startBtn.className = startBtn.className + " disabled active";
					found = 1;
					break;
				}
				else{
					found = 0;
				}
			}
			if (!found){
				stopBtn.className = stopBtn.className + " disabled active";
			}
			index++;
		}

		//Special Interface setInterface
		$("#br_port_1_Display").removeClass("hidden");
		$("#br_port_2_Display").removeClass("hidden");
		var y = document.getElementById("br_port_1");
		// y.size = Object.keys(data.AutoInterfaces).length;
		var z = document.getElementById("br_port_2");
		// z.size = Object.keys(data.AutoInterfaces).length;
		for (var Autoiface in data.AutoInterfaces) {
			if (Autoiface != "br0" && Autoiface != "lo"){
				var br_port_1 = document.createElement("option");
				var br_port_2 = document.createElement("option");
				if (Autoiface == "wlan0"){
					br_port_1.text = Autoiface;
					y.add(br_port_1);
					y.selectedIndex = br_port_1.index;
				}
				if (Autoiface != "wlan0"){
					br_port_2.text = Autoiface;
					z.add(br_port_2);
				}
				if (isBridged == true){
					y.selectedIndex = br_port_1.index;
					z.selectedIndex = br_port_2.index;
				} else if (isNAT == Autoiface){
					z.selectedIndex = br_port_2.index;
					document.getElementById("submitButton").setAttribute("data-previous-nat-ipv4", Autoiface);
				}
			}
		}
		$("#br_port_1_Display6").removeClass("hidden");
		$("#br_port_2_Display6").removeClass("hidden");
		var j = document.getElementById("br_port_1_IPv6");
		var k = document.getElementById("br_port_2_IPv6");
		for (var Autoiface in data.AutoInterfaces) {
			if (Autoiface != "br0" && Autoiface != "lo"){
				var br_port_1_IPv6 = document.createElement("option");
				var br_port_2_IPv6 = document.createElement("option");
				if (Autoiface == "wlan0"){
					br_port_1_IPv6.text = Autoiface;
					j.add(br_port_1_IPv6);
					j.selectedIndex = br_port_1.index;
				}
				if (Autoiface != "wlan0"){
					br_port_2_IPv6.text = Autoiface;
					k.add(br_port_2_IPv6);
				}
				if (br_port_1_IPv6.text == data.Interfaces[iface].IPv4.br_port_1){
					j.selectedIndex = br_port_1_IPv6.index;
				}
				if (br_port_2_IPv6.text == data.Interfaces[iface].IPv4.br_port_2){
					k.selectedIndex = br_port_2_IPv6.index;
				}else if (isNAT6 == Autoiface){
					k.selectedIndex = br_port_2_IPv6.index;
					document.getElementById("submitButton").setAttribute("data-previous-nat-ipv6", Autoiface);
				}
			}
		}
		if(!isBridged && !isNAT){
			document.getElementById("SpecialInterfaceNone").checked = true;
		} else if (isBridged && !isNAT){
			document.getElementById("SpecialInterfaceBridge").checked = true;
		} else if (!isBridged && isNAT){
			document.getElementById("SpecialInterfaceNAT").checked = true;
		}

		if(isNAT6){
			document.getElementById("SpecialInterfaceNAT6").checked = true;
		} else {
			document.getElementById("SpecialInterfaceNone6").checked = true;
		}
	})
	.fail(function() {
		consoleLog("Error, couldn't get getInterfaces.php.. retrying");
		if (retry < 5){
			retry++;
			updateSelectInterfacePage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}

function clickInterfacePage(retry){
	$.ajax({
		url: "plugins/interfaces/html/selectInterface.html",
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
			$("#interfaces_main_menu").addClass("active");
			$("#helpText").html("Interface information");
		},
	})
	.done(function( data ) {
		updateSelectInterfacePage(0);
	})
	.fail(function() {
		consoleLog("Error, couldn't get selectInterface.html.. retrying");
		if (retry < 5){
			retry++;
			clickInterfacePage(retry);
		} else {
			consoleLog("Retry max attempt reached");
		}
	});
}
