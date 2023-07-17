<?php

include_once 'config/init.php';
$voucher = new Vouchers();


if (!$auth->isLoggedIn()){
    header('Location: login.php');
}


$user = new User();
$u = $user->getUserData();
$page = "settings";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Settings | <?= APP_NAME ?></title>
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
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-tab-pane" type="button" role="tab" aria-controls="account-tab-pane" aria-selected="true">Account</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">Password</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete-tab-pane" type="button" role="tab" aria-controls="delete-tab-pane" aria-selected="false">Delete Account</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="account-tab-pane" role="tabpanel" aria-labelledby="account-tab" tabindex="0">
                            <div class="container py-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="inputUsername">Username</label>
                                            <input type="text" class="form-control-plaintext" id="inputUsername" value="<?= $u['username']; ?>" placeholder="Username" readonly>
                                            <small id="inputUsername" class="text-muted">
                                                Username cannot be changed.
                                            </small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="inputEmail">First Name</label>
                                                    <input type="text" class="form-control" id="inputEmail" value="<?= $u['first_name'] ?>" placeholder="Enter your first name">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="inputEmail">Last Name</label>
                                                    <input type="text" class="form-control" id="inputEmail" value="<?= $u['last_name'] ?>" placeholder="Enter your last name    ">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="inputEmail">Email</label>
                                                    <input type="text" class="form-control" id="inputEmail" value="<?= $u['email'] ?>" placeholder="Email">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">

                            <div class="container py-3">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label for="oldpassword">Old Password</label>
                                                    <input type="password" id="oldpassword" name="oldpassword" class="form-control">
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label for="oldpassword">New Password</label>
                                                    <input type="password" id="oldpassword" name="oldpassword" class="form-control">
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label for="oldpassword">Confirm New Password</label>
                                                    <input type="password" id="oldpassword" name="oldpassword" class="form-control">
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="delete-tab-pane" role="tabpanel" aria-labelledby="delete-tab" tabindex="0">

                            <div class="container py-5">
                                <button type="button" name="delete_account" id="delete_account" class="btn btn-danger">Delete Account</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>