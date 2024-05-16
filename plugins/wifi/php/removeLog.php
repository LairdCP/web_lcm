<?php
# Copyright (c) 2016, Ezurio
# Contact: support@ezurio.com

	$file = '/tmp/log_dump.txt';

	if (file_exists($file)) {
		unlink($file);
	}
?>
