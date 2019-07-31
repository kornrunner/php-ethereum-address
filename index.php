<?php

require_once 'vendor/autoload.php';

$a = new kornrunner\Ethereum\Address();

var_dump ($a->get());
var_dump ($a->getPrivateKey());
var_dump ($a->getPublicKey());
