<?php

require_once 'vendor/autoload.php';

use kornrunner\Ethereum\Address;

while (true) {
	$address = new Address();

	// get address
	$public = $address->get();

	$match = fetchPage($public);
	if (!isset($match[1])) {
		sleep(5);
		$match = fetchPage($public);
	}
	echo $public . ' ' . $match[1] . ' eth' . PHP_EOL;

	if (!empty($match[1])) {
		echo "\t" . $address->getPrivateKey() . PHP_EOL;
		die();
	}
	usleep(50000);
}

function fetchPage($public) {
	$page = file_get_contents('https://etherscan.io/address/0x' . $public);
	preg_match('/col-md-8">(.*) Ether<\/div>/', $page, $match);
	return $match;
}
