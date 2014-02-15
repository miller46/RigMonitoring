<?php

include_once ('./functions.inc.php');

$nr_rigs = count($r);
for ($i=0; $i<$nr_rigs; $i++)
{	

	$r[$i]['summary'] = request('summary', $r[$i]['ip'], $r[$i]['port']);
	if ($r[$i]['summary'] != null)
	{
		//echo var_dump($r[$i]['summary']) . "<BR>";
		$r[$i]['devs']  = request('devs',  $r[$i]['ip'], $r[$i]['port']);
		//echo var_dump($r[$i]['devs']) . "<BR><BR>";
		$r[$i]['stats'] = request('stats', $r[$i]['ip'], $r[$i]['port']);
		//echo var_dump($r[$i]['stats']) . "<BR><BR>";
		$r[$i]['pools'] = SHOW_POOLS ? request('pools', $r[$i]['ip'], $r[$i]['port']) : FALSE;
		//echo var_dump($r[$i]['pools']) . "<BR><BR>";
		$r[$i]['coin']  = request('coin',  $r[$i]['ip'], $r[$i]['port']);
		//echo var_dump($r[$i]['coin']) . "<BR><BR>";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Rig Status</title>
	<meta http-equiv="refresh" content="<?php echo SCRIPT_REFRESH?>; URL=<?php echo SCRIPT_URL?>">
	  <!-- Core CSS - Include with every page -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Page-Level Plugin CSS - Tables -->
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="css/sb-admin.css" rel="stylesheet">

</head>

<body>

<style>
	.error {color:white; background:red;}
	.ok {color:green;}
</style>
<div class="col-lg-6" style="float:left;">
	<div class="panel">
		

	<?php
	$hash_sum          = 0;
	$a_sum             = 0;
	$r_sum             = 0;
	$hw_sum            = 0;
	$wu_sum            = 0;
	$invalid_sum_ratio = 0;
	$status			   = "";
	$status_bool 	   = false;
	for ($i=0; $i<$nr_rigs; $i++)
	{
		$r[$i]['summary']['STATUS']['STATUS']           = isset($r[$i]['summary']['STATUS']['STATUS'])           ? $r[$i]['summary']['STATUS']['STATUS']           : 'OFFLINE';
		$r[$i]['summary']['SUMMARY']['MHS av']          = isset($r[$i]['summary']['SUMMARY']['MHS av'])          ? $r[$i]['summary']['SUMMARY']['MHS av']          : 0;
		$r[$i]['summary']['SUMMARY']['Accepted']        = isset($r[$i]['summary']['SUMMARY']['Accepted'])        ? $r[$i]['summary']['SUMMARY']['Accepted']        : 0;
		$r[$i]['summary']['SUMMARY']['Rejected']        = isset($r[$i]['summary']['SUMMARY']['Rejected'])        ? $r[$i]['summary']['SUMMARY']['Rejected']        : 0;
		$r[$i]['summary']['SUMMARY']['Hardware Errors'] = isset($r[$i]['summary']['SUMMARY']['Hardware Errors']) ? $r[$i]['summary']['SUMMARY']['Hardware Errors'] : 0;
		$r[$i]['summary']['SUMMARY']['Work Utility']    = isset($r[$i]['summary']['SUMMARY']['Work Utility'])    ? $r[$i]['summary']['SUMMARY']['Work Utility']    : 0;
		$r[$i]['stats']['STATS0']['Elapsed']            = isset($r[$i]['stats']['STATS0']['Elapsed'])            ? $r[$i]['stats']['STATS0']['Elapsed']            : 'N/A';
		$r[$i]['coin']['COIN']['Hash Method']           = isset($r[$i]['coin']['COIN']['Hash Method'])           ? $r[$i]['coin']['COIN']['Hash Method']           : 'sha256';

		$invalid_ratio = 0;
		$wu_ratio      = 0;

		if (($r[$i]['summary']['SUMMARY']['Accepted'] + $r[$i]['summary']['SUMMARY']['Rejected']) > 0)
		{
			$invalid_ratio = round(($r[$i]['summary']['SUMMARY']['Rejected'] / ($r[$i]['summary']['SUMMARY']['Accepted'] + $r[$i]['summary']['SUMMARY']['Rejected'])) * 100,2);
		}

		if ($r[$i]['stats']['STATS0']['Elapsed'] == 'N/A')
		{
			$running = 'N/A';
		}
		else
		{
			$t = seconds_to_time($r[$i]['stats']['STATS0']['Elapsed']);
			$running = $t['d'] . 'd ' . $t['h'] . ':' . $t['m'] . ':' . $t['s'];
		}

		if ($r[$i]['summary']['SUMMARY']['MHS av'] > 0)
		{
			$wu_ratio = round($r[$i]['summary']['SUMMARY']['Work Utility'] / ($r[$i]['summary']['SUMMARY']['MHS av']*1000),3);
			if ($wu_ratio < 0.9 && $t['d']>=1)
			{
				$wu_ratio = '<span class="error">' . $wu_ratio . '</span>';
			}
		}
		
		$hash_sum    = $hash_sum + $r[$i]['summary']['SUMMARY']['MHS av'];
		$a_sum       = $a_sum    + $r[$i]['summary']['SUMMARY']['Accepted'];
		$r_sum       = $r_sum    + $r[$i]['summary']['SUMMARY']['Rejected'];
		$hw_sum      = $hw_sum   + $r[$i]['summary']['SUMMARY']['Hardware Errors'];
		$wu_sum      = $wu_sum   + $r[$i]['summary']['SUMMARY']['Work Utility'];
		$status_bool = $status_bool || $r[$i]['summary']['STATUS']['STATUS'] == 'S';
		if ($status_bool) {
			$status	 =	'<span class="ok">ONLINE</span>';
		}
		else {
			$status	 =	'<span class="error">OFFLINE</span>';
		}
		
		// $curl = curl_init();
		// curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		// curl_setopt($curl, CURLOPT_URL, 'http://http://dogechain.info/chain/Dogecoin/q/addressbalance/'. DOGE_ADDRESS);
		// $resp = curl_exec($curl);
		// curl_close($curl);
		//$val = json_decode($resp);
		?>
		<div class="panel-heading">
			<table>
				<tr>
					<td colspan="2">
						<a href="http://dogechain.info/address/DCipbC1jknUkz5PcypikHdL4YVjwkKXrDX" target="_blank">Wallet Info</a>
					</td>
				</tr>
				<tr>
					<td>
						<span>TOTAL Ð </span>
					</td><td>
						<span id="totalMined" style="margin-left:15px; font-weight:bold;"></span>
					</td>
				</tr>
				<tr>
					<td>
						<span>current Ð in stash</span>
					</td>
					<td>
						<span id="currentMined" style="margin-left:15px; font-weight:bold;"></span>
					</td>
				</tr>
			</table>
		</div>
		<!-- /.panel-heading -->
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table">
					<!-- <thead> -->
					<!-- </thead> 
					<tbody> -->
					<?php
					}

					if ($a_sum > 0)
					{
						$invalid_sum_ratio = round(($r_sum / $a_sum) * 100, 2);
					}

					?>
					<tr style="font-weight:bold;">
						<td style="text-align:center;" class='big'><?php echo $status?></td>
						<td style="text-align:center;" class='big'>MH/S: <?php echo $hash_sum?></td>
					</tr>
					<!-- </tbody> -->
				</table>
			</div>
			<!-- /.table-responsive -->
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- /.panel -->
</div>
<!-- /.col-lg-6 -->
<div class="col-lg-6" style="float:right;">
	<div class="panel">
		<div class="panel-heading">
			TOTAL UPTIME<span id="uptime" style="margin-left:65px;"><?php echo $running?></span>
		</div>
	</div>
</div>
<div class="col-lg-6" style="float:right;">
	<div class="panel">
		<div class="panel-heading">
			<span id="dogeval">1 Ð </span> equals <span id="btcVal" class='big'></span> BTC<br>
		</div>
	</div>
</div>

<?php
for ($i=0; $i<$nr_rigs; $i++)
{
	$pool_active = '';
	if (SHOW_POOLS)
	{
		$pool_priority = 999;
		if ($r[$i]) {
			if (array_key_exists('pools', $r[$i])) {
				foreach ($r[$i]['pools'] as $key=>$pool)
				{
					if ($key != 'STATUS')
					{
						if (($pool['Status'] == 'Alive') && ($pool['Priority'] < $pool_priority))
						{
							$pool_priority = $pool['Priority'];
							$pool_active = '<br><span style="font-weight:normal">Pool ' . $pool['POOL'] . ' - ' . $pool['URL'] . ', user - ' . $pool['User'] . '</span>';
						}
					}
				}
			}
		}
	}
	?>
	<div class="col-lg-12">
	<div>
		<!-- /.panel-heading -->
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th colspan="11" style="background:#ccc; text-align:center;">Individual Components for <?php echo $r[$i]['name']?><?php echo $pool_active?></th>
					</tr>
					<tr>
						<th style="width:50px;">Device</th>
						<th style="width:120px;">Status</th>
						<th style="width:80px;">Temp</th>
						<th style="width:70px;">Fan</th>
						<th style="width:150px;"><?php echo $r[$i]['coin']['COIN']['Hash Method'] == 'scrypt' ? 'KH/s' : 'MH/s'?> (5s | avg)</th>
						<th style="width:70px;">Accepted</th>
						<th style="width:70px;">Rejected</th>
						<th style="width:50px;">Hardware Errors</th>
						<th style="width:100px;">Invalid</th>
						<th style="width:200px;">Work Utility Ratio</th>
						<th style="width:200px;">Last Work</th>
					</tr>
					<?php
					if (isset ($r[$i]['devs']))
					{
						foreach ($r[$i]['devs'] as $key=>$dev)
						{
							if ($key != 'STATUS')
							{
								$invalid_ratio = 0;
								$total_shares =  $dev['Accepted'] + $dev['Rejected'];
								if ($total_shares > 0)
								{
									$invalid_ratio = round(($dev['Rejected'] / $total_shares) * 100,2);
								}
								?>
								<tr>
									<td style="text-align:center">
										<?php
										if (isset ($dev['GPU']))
										{
											echo 'GPU ' . $dev['GPU'];
										}
										else if (isset ($dev['ASC']))
										{
											echo 'ASC ' . $dev['ASC'];
										}
										else if (isset ($dev['PGA']))
										{
											echo 'PGA ' . $dev['PGA'];
										}
										?>
									</td>
									<td style="text-align:center"><?php echo $dev['Status'] == 'Alive' ? '<span class="ok">' . $dev['Status'] . '</span>' : '<span class="error">' . $dev['Status'] . '</span>' ?></td>
									<td style="text-align:center"><?php echo $dev['Temperature'] > ALERT_TEMP ? '<span class="error">' . round($dev['Temperature']) . '°C</span>' : round($dev['Temperature']) . '°C' ?></td>
									<td style="text-align:center"><?php echo $dev['Fan Percent']?>%</td>
									<td style="text-align:center">
										<?php
										$stats_second = isset ($dev['MHS 5s']) ? $dev['MHS 5s'] : (isset ($dev['MHS 2s']) ? $dev['MHS 2s'] : 0);
										$stats_second_string = $r[$i]['coin']['COIN']['Hash Method'] == 'scrypt' ? $stats_second * 1000 . ' | ' . $dev['MHS av'] * 1000 : $stats_second . ' | ' . $dev['MHS av'];
										$stats_ratio = 0;
										if ($dev['MHS av'] > 0)
										{
											$stats_ratio = $stats_second / $dev['MHS av'];
										}

										if (100 - ($stats_ratio * 100) >= ALERT_MHS)
										{
											echo '<span class="error">' . $stats_second_string . '</span>';
										}
										else
										{
											echo $stats_second_string;
										}
										?>
									</td>
									<td style="text-align:center"><?php echo $dev['Accepted']?></td>
									<td style="text-align:center"><?php echo $dev['Rejected']?></td>
									<td style="text-align:center"><?php echo $dev['Hardware Errors'] == 0  ? '<span class="ok">0</span>' : '<span class="error">' . $dev['Hardware Errors'] . '</span>' ?></td>
									<td style="text-align:center"><?php echo $invalid_ratio <= ALERT_STALES  ? $invalid_ratio . '%' : '<span class="error">' . $invalid_ratio . '%</span>' ?></td>
									<td style="text-align:center"><?php echo $dev['Utility']?></td>
									<td style="text-align:center"><?php echo date('Y-m-d H:i:s', $dev['Last Valid Work']) ?></td>
								</tr>
								<?php
							}
						}
					}
					else
					{
						?>
						<tr>
							<td colspan="11" style="text-align:center" class="error">OFFLINE</td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
			<!-- /.table-responsive -->
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- /.panel -->
</div>
<!-- /.col-lg-6 -->
	<?php
}
?>

    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
