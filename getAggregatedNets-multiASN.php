#!/usr/bin/php -q

<?php

#Usage:
# ./getAggregatedNets-multiASN.php AS38895 AS7224 AS14618 AS16509

require_once('LibAgg/libAggregator.php');

use \CIDRAM\Aggregator\Aggregator;

if (php_sapi_name() == "cli") {
        $ASNs=$argv;
	unset($ASNs[0]);
        $rn="\n";
} else {
        $ASNs=$_GET['ASN'];
	unset($ASNs[0]);
        $rn="<br />";
}

//var_dump($ASNs);
//$ASN="AS14061";

$ips="";

foreach ($ASNs as &$ASN) {

$url="https://www.enjen.net/asn-blocklist/index.php?asn=".$ASN."&type=iplist";

$proxy="127.0.0.1:8118";
$pattern='/([0-9]{1,3}\.){3}[0-9]{1,3}(\/([0-2][0-9]|3[0-2]))?/';

$ch = curl_init();
//curl_setopt($curlInit, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
$response = curl_exec($ch);
curl_close($ch);

preg_match_all($pattern,$response,$matches);

//echo $response;
//var_dump($matches);

foreach ($matches[0] as &$ip) {
	//echo $ip."\n";
	$ips.=$ip.$rn;	
}
//echo $ASN."============================".$rn;
//echo $ips.$rn;

}

//echo $ips;

$Aggregator = new Aggregator();
$ipAggregated = $Aggregator->aggregate($ips);
echo $ipAggregated."\n";


