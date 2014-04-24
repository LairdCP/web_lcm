<?php
	$FWupdateLog='fw_update_log.txt';
	$FWupdateLogString=implode(file("fw_update_log.txt"));
	$FWupdateLogPos=strpos($FWupdateLogString, 'Done.');
	if (file_exists($FWupdateLog) && !isset($_POST['RemoteReboot']) && !isset($_POST['RemoteUpdate']))
	{
		?>
		<html>
			<div class="alert alert-warning">
				<strong>Remote Update Started:</strong>
				<?php
				echo "<p>";
				echo implode(file("fw_update_log.txt"));
				echo "</p>";
				?>
			</div>
				<?php
			if ($FWupdateLogPos === false)
			{

			}
			else
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