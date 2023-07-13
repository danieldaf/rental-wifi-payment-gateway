<?php

include_once 'config/init.php';

$paymongo = new Paymongo();
$paymongo->makeCheckout(10000, 1, "Voucher Name hjere");