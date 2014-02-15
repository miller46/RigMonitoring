<?php

	define('DOGE_ADDRESS','your_doge_address');
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$curl_url = "";
	if ($_GET['op'] == "current") {
		$curl_url = 'http://dogechain.info/chain/Dogecoin/q/addressbalance/';
		curl_setopt($curl, CURLOPT_URL,$curl_url . DOGE_ADDRESS);
	}
	if ($_GET['op'] == "total") {
		$curl_url = 'http://dogechain.info/chain/Dogecoin/q/getreceivedbyaddress/';
		curl_setopt($curl, CURLOPT_URL,$curl_url . DOGE_ADDRESS);
	}
	if ($_GET['op'] == 'btc') {
		$curl_url = 'http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=132';
		curl_setopt($curl, CURLOPT_URL,$curl_url);
	}
	$resp = curl_exec($curl);
	curl_close($curl);
	$obj = json_decode($resp);
	if ($_GET['op'] == 'btc') {
		if (isset($obj->{'return'})) {
			$value = $obj->{'return'}->{'markets'}->{'DOGE'}->{'lasttradeprice'};
			if (is_numeric($value)) {
				echo $value;
			}
			else {
				echo "N/A";
			}
		}
		else {
			echo "N/A";
		}
	}
	else {
		echo $resp;
	}
?>