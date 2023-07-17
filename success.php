<?php

include_once 'config/init.php';

    $payment = new PayMongo();
    $purchase = new Purchase();

    if (Session::checkSession('checkout_session_id')){

        $checkout_session_id = Session::getSession('checkout_session_id');
        $checkout_details = $payment->retrieveCheckout($checkout_session_id);

        if ($checkout_details['status'] === "paid"){
            if ($checkout_details['checkoutId'] == $checkout_session_id){

                $payment->updateVoucherandPurchase($checkout_details['checkoutId']);
                Session::unsetSession('checkout_session_id');
                $payment->removeVoucherFromOtherCart(Session::getSession('vid'));


            }
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }



?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"> Rental Wifi</a>
    </div>
</nav>


<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link"  href="index.php">My Vouchers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="buy.php">Buy Voucher</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">Account Setting</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        <div class="col-md-9">

            <div class="card">
                <div class="card-body">

                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Purchase has been successfully processed.
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
