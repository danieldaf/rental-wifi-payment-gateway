<?php

include_once 'config/init.php';

$voucher = new Vouchers();
$page = "buy";
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
            <?php

            include_once __DIR__ . '/views/sidebar.php';

            ?>
        </div>
        <div class="col-md-9">

            <div class="container">
                <div class="row no-gutters my-5">

                    <div class="col-sm-6 col-lg-3 py-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="fw-bold lead text-primary">
                                    Starter
                                    <span class="float-end text-dark">₱150</span>
                                </div>
                                <p class="text-truncate">Our most popular option.</p>
                                <button class="btn btn-outline-primary btn-lg w-100 text-truncate" id="purchase_button" data-id="starter">Purchase</button>
                                <div class="py-4 small">
                                    <h6 class="text-uppercase small">What's Included</h6>
                                    <div> <i class="mdi mdi-check text-primary"></i> 5Mbps download </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 2Mbps upload </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 30 days </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 2 devices max </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 py-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="fw-bold lead text-primary">
                                    Basic
                                    <span class="float-end text-dark">₱300</span>
                                </div>
                                <p class="text-truncate">Our most popular option.</p>
                                <button class="btn btn-outline-primary btn-lg w-100 text-truncate" id="purchase_button" data-id="basic">Purchase</button>
                                <div class="py-4 small">
                                    <h6 class="text-uppercase small">What's Included</h6>
                                    <div> <i class="mdi mdi-check text-primary"></i> 8Mbps download </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 4Mbps upload </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 30 days </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 2 devices max </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 py-3">
                        <div class="card shadow-lg border-0 h-100">
                            <div class="card-body">
                                <div class="fw-bold lead text-primary">
                                    5-day plan
                                    <span class="float-end text-dark">₱20/day</span>
                                </div>
                                <p class="text-truncate">Get a more features and share.</p>
                                <button class="btn btn-outline-primary btn-lg w-100 text-truncate" id="purchase_button" data-id="5dayplan">Purchase</button>
                                <div class="py-4 small">
                                    <h6 class="text-uppercase small">What's Included</h6>
                                    <div> <i class="mdi mdi-check text-primary"></i> 5Mbps download </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 2Mbps upload </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 5 day </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 1 device per voucher </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 py-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="fw-bold lead text-primary">
                                    Pro
                                    <span class="float-end text-dark">₱100/20gb</span>
                                </div>
                                <p class="text-truncate">Top-notch features &amp; support.</p>
                                <button class="btn btn-outline-primary btn-lg w-100 text-truncate" id="purchase_button" data-id="pro">Purchase</button>
                                <div class="py-4 small">
                                    <h6 class="text-uppercase small">What's Included</h6>
                                    <div> <i class="mdi mdi-check text-primary"></i> 8Mbps  download </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 4Mbps  upload </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> No Expiry </div>
                                    <div> <i class="mdi mdi-check text-primary"></i> 1 device per voucher </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>\
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '#purchase_button', function (e){

            e.preventDefault();

        let pricing = $(this).data("id");

        $.ajax({
            type: "POST",
            url: 'config/Ajax.php',
            beforeSend: function(){
                $("#purchase_button").addClass("disabled");
            },
            data: {
                action: "purchaseProcess",
                pricing: pricing
            },
            success: function (response) {
                var data = JSON.parse(response);
                window.location.href = data.checkout_url;

            }
        })



        })
    })
</script>
</body>
</html>