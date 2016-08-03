
function restartUpdate(retry){
	$.ajax({
		url: "plugins/remote_update/php/restartUpdate.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function( msg ) {
		if (msg.SESSION == sdcsdk.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		clickAdvancedRemoteUpdate(0)
	})
	.fail(function() {
		console.log("Failed to restart update, retrying");
		if (retry < 5){
			retry++;
			restartUpdate(retry);
		} else {
			console.log("Retry max attempt reached");
		}
	});
}

function rebootDevice(retry){
	$.ajax({
		url: "plugins/remote_update/php/reboot.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function( msg ) {
		if (msg.SESSION == sdcsdk.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		$("#updateLogDisplay").addClass("hidden");
		$("#updateLogTextDisplay").addClass("hidden");
		$("#updateProgressDisplay").removeClass("hidden");
		$("#helpText").html("Device is now rebooting. The page will attempt to redirect in 20 seconds if IP address remains the same.");
		setTimeout("location.reload(true);", 20000);
	})
	.fail(function() {
		console.log("Failed to reboot device, retrying");
		if (retry < 5){
			retry++;
			rebootDevice(retry);
		} else {
			console.log("Retry max attempt reached");
		}
	});
}

function startUpdate(retry){
	var updateData = {
		remoteUpdate: document.getElementById("remoteUpdate").value,
	}
	console.log(updateData);
	$.ajax({
		url: "plugins/remote_update/php/startUpdate.php",
		type: "POST",
		data: JSON.stringify(updateData),
		contentType: "application/json",
	})
	.done(function( msg ) {
		if (msg.SESSION == sdcsdk.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		checkUpdateStarted(0);
	})
	.fail(function() {
		console.log("Failed to send update data, retrying");
		if (retry < 5){
			retry++;
			startUpdate(retry);
		} else {
			console.log("Retry max attempt reached");
		}
	});
}

function showUpdateLog(retry){
	$.ajax({
		url: "plugins/remote_update/php/getUpdateLog.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function( data ) {
		var logLength = Object.keys(data.log).length;
		document.getElementById("updateLogText").rows = logLength + 3;
		$("#updateProgressDisplay").addClass("hidden");
		$("#updateLogDisplay").removeClass("hidden");
		$("#updateLogTextDisplay").removeClass("hidden");
		$("#updateLogText").html(data.log);
		if (data.log[logLength - 1].trim() == "Done."){
			if (intervalId){
					clearInterval(intervalId);
					intervalId = 0;
			}
			if (data.log[logLength - 2].trim().substring(0,7) == "Errors:"){
				$("#startOverButtonDisplay").removeClass("hidden");
			}else{
				$("#rebootButtonDisplay").removeClass("hidden");
			}
		}else{
			if (!intervalId){
				setIntervalUpdate(showUpdateLog);
			}
		}
	})
	.fail(function() {
		console.log("Error, couldn't get update log..");
		if (retry < 5){
			retry++;
			clickAdvancedRemoteUpdate(retry);
		} else {
			console.log("Retry max attempt reached");
		}
	});
}

function checkUpdateStarted(retry){
	$.ajax({
		url: "plugins/remote_update/php/verifyUpdate.php",
		type: "POST",
		contentType: "application/json",
	})
	.done(function( data ) {
		if (data.SESSION == sdcsdk.SDCERR.SDCERR_FAIL){
			expiredSession();
			return;
		}
		if (data.fwUpdate == "running"){
			$("#remoteUpdateDisplay").addClass("hidden");
			$("#submitButton").addClass("hidden");
			showUpdateLog(0);
		}else{
			$("#updateProgressDisplay").addClass("hidden");
			$("#remoteUpdateDisplay").removeClass("hidden");
			$("#submitButton").removeClass("hidden");
		}
	})
	.fail(function() {
		console.log("Error, couldn't get verifyUpdate.php.. retrying");
		if (retry < 5){
			retry++;
			checkUpdateStarted(retry);
		} else {
			console.log("Retry max attempt reached");
		}
	});
}

function clickAdvancedRemoteUpdate(retry){
	$.ajax({
		url: "plugins/remote_update/html/remoteUpdate.html",
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
			$("#submenuRemoteUpdate").addClass("active");
			$("#helpText").html("Remotely update device");
		},
	})
	.done(function( data ) {
		checkUpdateStarted(0);
	})
	.fail(function() {
		console.log("Error, couldn't get remoteUpdate.html.. retrying");
		if (retry < 5){
			retry++;
			clickAdvancedRemoteUpdate(retry);
		} else {
			console.log("Retry max attempt reached");
		}
	});
}
