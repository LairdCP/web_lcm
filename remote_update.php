<!--
  Copyright (c) 2015, Laird; Released under the ISC license.
  Contact: ews-support@lairdtech.com
-->
<?php
	$FWupdateLog='fw_update_log.txt';
	$FWupdateLogString=implode("<br>",file("fw_update_log.txt"));
	$FWupdateLogPos=strpos($FWupdateLogString, 'Done.');
	if (file_exists($FWupdateLog) && !isset($_POST['RemoteReboot']) && !isset($_POST['RemoteUpdate']))
	{
		?>
		<html>
			<div class="alert alert-warning">
				<strong>Remote Update Started:</strong>
				<?php
				echo "<p>";
				echo $FWupdateLogString;
				echo "</p>";
				?>
			</div>
				<?php
			if ($FWupdateLogPos != false)
			{
			?>
			<form name="RemoteReboot" role="form" action='' method='post'>
				<button type='submit' class='btn btn-warning btn-block'>Reboot device to complete remote update.</button>
				<input type = "hidden" name = "RemoteReboot" value = "<?php echo $_POST['RemoteReboot']; ?>"/>
			</form>
			<?php
			}
			?>
		</html>
		<?php
	}
	?>