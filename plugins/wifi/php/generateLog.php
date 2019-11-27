<?php
# Copyright (c) 2016, Laird
# Contact: support@lairdconnect.com

	$file = '/tmp/log_dump.txt';

	if (!file_exists($file)) {
		exec("/usr/bin/log_dump &> /dev/null &");
	} else {
		$result = SDCERR_SUCCESS;
	}
?>
