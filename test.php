<?php

include_once 'config/init.php';

$paymongo = new Paymongo();
$paymongo->makeCheckout();