<?php

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
                $voucher = new Vouchers();

            break;
        default:
            echo "Invalid action";
    }


}