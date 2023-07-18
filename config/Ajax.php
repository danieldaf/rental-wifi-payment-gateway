<?php

use GuzzleHttp\Exception\GuzzleException;

include_once 'init.php';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    switch($action){
        case 'userLogin':
            $login = new Authentication();
            $login->userLogin($_POST['username'], $_POST['password'], $_POST['csrf']);
            break;
        case 'guestLogin':

            $login = new Authentication();
            $login->loginAsGuest();
            break;
        case 'userRegister':
            $register = new Authentication();
            $register->userRegister($_POST['username'], $_POST['email'], $_POST['password']);
            break;
        case 'purchaseProcess':
            $purchase = new Purchase();
            $purchase->makePurchase($_POST['pricing']);
            break;
        case 'expireCheckout':
            $paymongo = new PayMongo();
            try {
                $paymongo->expireCheckout($_POST['checkout_session_id']);
            } catch (GuzzleException $e) {
                echo "Something went wrong";
            }

            break;
        default:
            echo "Invalid action";
    }


}