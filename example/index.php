<?php

require __DIR__ . "../../vendor/autoload.php";

use FogacaSammuel\Ofx\Ofx;

$ofx = new Ofx(__DIR__ . "/test.ofx");

//Get invoices from file OFX
$invoices = $ofx->invoices();
var_dump($invoices);

//Get data account from file OFX
$account = $ofx->account();
var_dump($account);

//Get balance from account
$balance = $ofx->balance();
var_dump($balance);