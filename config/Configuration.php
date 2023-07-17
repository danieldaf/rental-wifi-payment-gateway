<?php
const DB_HOST = 'localhost';
const DB_NAME = 'payment_gateway';
const DB_USER = 'root';
const DB_PASS = '@Cyanne01';
const AUTHORIZATION_VALUE = 'Basic c2tfdGVzdF93aUNjMVNacEt3TG5KeXhGYkZaWkZtTHQ6';


/**
 * APP Configuration
 */

const APP_NAME = 'Rental WiFi';

$protocol = !str_contains(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') ? 'http' : 'https';
$domainLink = $protocol . '://' . $_SERVER['HTTP_HOST'];

define("BASE_URL", $domainLink);
const SUCCESS_URL = BASE_URL . '/success.php';


