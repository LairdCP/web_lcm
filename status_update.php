<!DOCTYPE html>
<html lang="en">
 	<?php
	exec('sdc_cli status 2>&1', $sdcstatus);
	$status=$sdcstatus;
	$TitleStatus=ucwords(substr($sdcstatus[0],11));
	echo "<title>LCM - $TitleStatus</title>";
	?>
	<!-- Show status -->
	<div class="panel panel-default">
    <div class="row panel-body">
	<br>
	<div class="col-xs-12 col-sm-10 col-md-10 col-sm-offset-2 col-md-offset-2"><h2>WB Status</h2></div>
	<?php
	if (stristr($TitleStatus,"Adhoc"))
	{
		foreach ($status as $status_loop)
		{
			?>
			<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status_loop; ?></strong></h6></div>
			<?php
		}
	}
	elseif (stristr($TitleStatus,"AP Mode"))
	{
		?>
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[0]; ?></strong></h6></div> <!-- /status -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[1]; ?></strong></h6></div> <!-- /SSID -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[2]; ?></strong></h6></div> <!-- /Number of Clients -->
		<div class="col-xs-6 col-sm-8 col-md-8">
		<?php
		if (stristr($status[3],"MAC address") && stristr($status[3],"Connection seconds"))
		{
			?>
			<div class="col-xs-12 col-sm-9 col-md-9 col-sm-offset-3 col-md-offset-3"><h6><strong>MAC address and Connection seconds</strong></h6></div> <!-- /MAC address -->
			<?php
			for ($x = 4; $x <= count($status); $x++)
			{
				?>
				<div class="col-xs-12 col-sm-9 col-md-9 col-sm-offset-3 col-md-offset-3"><h6><strong><?php echo $status[$x]; ?></strong></h6></div> <!-- /Clients -->
				<?php
			}
		}
	}
	else
	{
		?>
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[1]; ?></strong></h6></div> <!-- /config name -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[2]; ?></strong></h6></div> <!-- /SSID -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[0]; ?></strong></h6></div> <!-- /status -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[5]; ?></strong></h6></div> <!-- /Device name -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[8]; ?></strong></h6></div> <!-- /AP name -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[7]; ?></strong></h6></div> <!-- /IP -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[10]; ?></strong></h6></div> <!-- /AP IP -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[6]; ?></strong></h6></div> <!-- /MAC -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[9]; ?></strong></h6></div> <!-- /AP MAC -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[3]; ?></strong></h6></div> <!-- /Channel -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[13]; ?></strong></h6></div> <!-- /Beacon Period -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[14]; ?></strong></h6></div> <!-- /DTIM -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[11]; ?></strong></h6></div> <!-- /Bit Rate -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-2"><h6><strong><?php echo $status[12]; ?></strong></h6></div> <!-- /TX Power -->
		<div class="col-xs-6 col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"><h6><strong><?php echo $status[4]; ?></strong></h6></div> <!-- /RSSI -->
		<div class="col-xs-10 col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2">

		<?php
		if (is_numeric(substr($status[3],10,3)))
		{
			$subRSSI=substr($status[3],10,3);
			$RSSIMeter=$subRSSI+120;
		}
		elseif (is_numeric(substr($status[4],10,3)))
		{
			$subRSSI=substr($status[4],10,3);
			$RSSIMeter=$subRSSI+120;
		}
		if ($subRSSI == 0 && trim($TitleStatus) != "Authenticated") //account for not connected
		{
			if (trim($status[0]) != "no hardware detected")
			{
				?>
				<div class="progress">
				<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0%">
				</div>
				</div>
				<?php
			}
		}
		elseif ($subRSSI < -90) //red
		{
		?>
		<div class="progress">
		<div class="progress-bar progress-bar-danger" role="progressbar" style='<?php echo "width: $RSSIMeter%;";?>'>
		</div>
		</div>
		<?php
		}
		elseif ($subRSSI < -70) //yellow
		{
		?>
		<div class="progress">
		<div class="progress-bar progress-bar-warning" role="progressbar" style='<?php echo "width: $RSSIMeter%;";?>'>
		</div>
		</div>
		<?php
		}
		elseif ($subRSSI > -20)//account for signals >-20
		{
		?>
		<div class="progress">
		<div class="progress-bar progress-bar-success" role="progressbar" style='<?php echo "width: $RSSIMeter%;";?>'>
		</div>
		</div>
		<?php
		}
		else //green
		{
		?>
		<div class="progress">
			<div class="progress-bar progress-bar-success" role="progressbar" style='<?php echo "width: $RSSIMeter%;";?>'>
			</div>
		</div>
		<?php
		}
	}
	?>
	</div>
    </div>
	</div><!-- /row panel-body -->
</html>
