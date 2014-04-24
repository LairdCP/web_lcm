<?php
exec('sdc_cli status 2>&1', $sdcstatus);
$TitleStatus=ucwords(substr($sdcstatus[0],11));
echo "LCM - $TitleStatus";
?>
