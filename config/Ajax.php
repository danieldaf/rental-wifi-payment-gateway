<?php

use GuzzleHttp\Exception\GuzzleException;

include_once 'init.php';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    switch($action){
        case 'userLogin':
            $login = new Authentication();
            $login->userLogin($_POST['username'], $_POST['password']);
            break;
        case 'userRegister':

            break;
        case 'purchaseProcess':

            try {

                $purchase = new Purchase();
                $purchase->makePurchase($_POST['pricing']);

            } catch (GuzzleException $e) {
                echo "Something went wrong";
            }

            break;
        default:
            echo "Invalid action";
    }


}