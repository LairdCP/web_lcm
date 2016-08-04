<?php
# Copyright (c) 2016, Laird
# Contact: ews-support@lairdtech.com

	$file = '/tmp/log_dump.txt';

	if (file_exists($file)) {
		unlink($file);
	}
?>
