<?php


/**
 * APP Configuration
 */
const APP_NAME = 'Rental WiFi';
const PRODUCTION = false;

if (PRODUCTION) {
    define("DB_HOST", '');
    define("DB_NAME", '');
    define("DB_USER", '');
    define("DB_PASS", '');
    define("AUTHORIZATION_VALUE", ''); // Base64 encoded API key
    define("BASE_URL", "https://www.example.com/payment-gateway");
} else {
    define("DB_HOST", 'localhost');
    define("DB_NAME", 'payment_gateway');
    define("DB_USER", 'root');
    define("DB_PASS", '');
    define("AUTHORIZATION_VALUE", 'c2tfdGVzdF93aUNjMVNacEt3TG5KeXhGYkZaWkZtTHQ6'); // Base64 encoded API key
    define("BASE_URL", "http://localhost/payment-gateway");
}

const SUCCESS_URL = BASE_URL . '/success.php';

if (!PRODUCTION) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    error_reporting(0);
    ini_set('display_errors', 'Off');
}

