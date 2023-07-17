<?php

    include_once 'config/init.php';
    $voucher = new Vouchers();


    if (!$auth->isLoggedIn()){
        header('Location: login.php');
    }
$page = "home";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
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

            <div class="card">
                <div class="card-body">


                    <div class="table-responsive">
                        <table class="table table-hover" id="myTable">
                            <thead>
                            <tr>
                                <th scope="col">Reference Number</th>
                                <th scope="col">Code</th>
                                <th scope="col">Price</th>
                                <th scope="col">Voucher Information</th>
                                <th scope="col">Invoice Date</th>
                                <th scope="col">Due Date</th>
                                <th scope="col">Status</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $purchasedVoucher = $voucher->purchasedVoucher();
                                if (!empty($purchasedVoucher)):
                                foreach ($purchasedVoucher as $res):

                                    $paymongo = new Paymongo();
                                    $paymong_data = $paymongo->retrieveCheckout($res['checkout_session_id']);

                                    if($res['purchase_status'] === 'pending'){
                                        if ($paymong_data['status'] === 'not_paid') {
                                            $checkout_url =  $paymong_data['checkout_url'];
                                        }
                                    }


                            ?>
                            <tr>
                                <th scope="row"><?= $res['reference_number'] ?></th>
                                <td><?php echo (!$voucher->isPaid($res['purchased_id'], $res['checkout_session_id'])) ? $voucher->makeSpoiler($res['code']) : $res['code']; ?></td>
                                <td>₱<?=  number_format($res['price'] * $res['quantity'] / 100, 2); ?></td>
                                <td><?= $res['voucher_description'] ?></td>
                                <td><?= date('m/d/Y', strtotime($res['created_at'])); ?></td>
                                <td><?= date('m/d/Y', strtotime($res['created_at'])); ?></td>
                                <td>
                                    <?= ($res['purchase_status'] === "pending" && $res['status'] !== "purchased")
                                        ? '<div class="d-flex"><a href="'. $checkout_url .'"><span class="badge bg-primary">Pay</span></a><a data-id="'. $res['checkout_session_id'] .'" id="cancel_purchase"><span class="badge bg-warning">Cancel</span></a></div>'
                                        : (($res['purchase_status'] === "paid" && $res['status'] === "purchased")
                                            ? '<span class="badge bg-success">Paid</span>'
                                            : '<span class="badge bg-warning text-dark">Cancelled</span>');
                                    ?>
                                </td>
                            </tr>
                            <?php
                                endforeach;
                                else:
                            ?>
                            <tr>
                              <td colspan="7">You have not purchased any vouchers yet.</td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script type="text/javascript">
    const dataTable = new simpleDatatables.DataTable("#myTable");

    $(document).ready(function() {

        $(document).on('click', '#cancel_purchase', function (e){
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: 'config/Ajax.php',
                data: {
                    action: "expireCheckout",
                    checkout_session_id: $(this).data("id"),
                }, success: function(data) {
                    if(data === "expired"){
                        location.reload();
                    }
                }
            })
        });

    })
</script>
</body>
</html>