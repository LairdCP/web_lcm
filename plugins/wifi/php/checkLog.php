<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	header("Content-Type: application/json");

	$result = "stopped";

	$file = '/tmp/log_dump.txt';
	$tmpFile = '/tmp/tmp_log_dump.txt';

	if (file_exists($file)){
		exec ( "ps | grep -q [/usr/bin/]log_dump",$output,$return_var);
		if ($return_var){
			$result = "finished";
			copy($file,$tmpFile);
		} else {
			$result = "running";
		}
	}

	$returnedResult['state'] = $result;

	echo json_encode($returnedResult);

?>
