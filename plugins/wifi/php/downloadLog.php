<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	$sourceFile = '/tmp/tmp_log_dump.txt';
	$finalFile = '/tmp/log_dump.txt';

	if (file_exists($sourceFile)) {
		syslog(LOG_WARNING, "file exists");
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($finalFile).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($sourceFile));
		readfile($sourceFile);
		exit;
	}
	else {
		syslog(LOG_WARNING, "log_dump file does not exist");
	}
?>
