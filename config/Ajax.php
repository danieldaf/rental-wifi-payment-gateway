<?php

use GuzzleHttp\Exception\GuzzleException;


//csrf protection
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
    die("Sorry bro!");

$url = parse_url($_SERVER['HTTP_REFERER'] ?? '');
if( !isset( $url['host']) || ($url['host'] != $_SERVER['SERVER_NAME']))
    die("Sorry bro!");

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
            try {
                $purchase->makePurchase($_POST['pricing']);
            } catch (GuzzleException $e) {
                echo "Something went wrong";
            }
            break;
        case 'expireCheckout':
            $paymongo = new PayMongo();
            $paymongo->expireCheckout($_POST['checkout_session_id']);
            break;
        case 'deleteCode':
            adminOnlyAccess();
            $voucher = new Vouchers();
            $voucher->deleteCode($_POST['code_id']);
            break;
        case 'changePassword':

            $user = new User();
            $user->changePassword($_POST['oldpassword'], $_POST['newpassword']);

            break;
        default:
            echo "Invalid action";
    }
}

function adminOnlyAccess() {
    $auth = new Authentication();
    if (!$auth->isAdmin()){
        echo "Not authorized";
        return false;
    }
    return true;

}
