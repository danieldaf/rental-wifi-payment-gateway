<?php

include_once 'config/init.php';

//$paymongo = new Paymongo();
//$paymongo->expireCheckout('cs_FyNLyqFZTsF4v7qriUd985x8');

//$csrf = new CSRF();
//$token =  $csrf->generateToken();
//
//echo "Token: " . $token;
//echo "<br>";
//echo "Is Valid: " . $csrf->validateToken($token);


$string = "value1, value2, value3"; // Example string
if (strpos($string, ',') !== false) {
    $result = implode("<br>", explode(', ', $string));
} else {
    $result = $string;
}
echo  $result;