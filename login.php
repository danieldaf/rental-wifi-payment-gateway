<?php

    include_once 'config/init.php';

    if ($auth->isLoggedIn()){
             header('Location: index.php');
        }

    $csrf = new CSRF();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
<section class="h-100">
    <div class="container h-100">
        <div class="row justify-content-sm-center h-100">
            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-7 col-sm-9">

                <div class="card my-5 shadow-lg">
                    <div class="card-body p-5">
                        <h1 class="fs-4 card-title fw-bold mb-4">Login</h1>
                        <form id="loginform" enctype="multipart/form-data">
                            <input type="hidden" id="csrf" name="csrf" value="<?php echo $csrf->generateToken(); ?>"  />
                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="username">Username</label>
                                <input id="username" type="text" class="form-control" name="email" value=""  autofocus>
                            </div>

                            <div class="mb-3">
                                <div class="mb-2 w-100">
                                    <label class="text-muted" for="password">Password</label>
                                    <a href="#" class="float-end">
                                        Forgot Password?
                                    </a>
                                </div>
                                <input id="password" type="password" class="form-control" name="password" >
                            </div>

                            <div class="d-flex align-items-center">
                                <button id="login_as_guest" type="button" class="btn btn-secondary">Login as Guest</button>
                                <button type="submit" class="btn btn-primary ms-auto">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer py-3 border-0">
                        <div class="text-center">
                            Don't have an account? <a href="register.php" class="text-dark">Create One</a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-5 text-muted">
                    Copyright &copy; 2023 &mdash; Your Company
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="assets/js/sha512.js"></script>
<script>
    $(document).ready(function() {

        $("#login_as_guest").on("click", function(e) {

            $.ajax({
                type: "POST",
                url: "config/Ajax.php",
                data: {
                    action: "guestLogin"
                },
                success: function (response) {
                    if(response === "true"){
                        window.location.href = "index.php";
                    }
                }
            })


        });
        $("#loginform").on('submit', function(e) {
            e.preventDefault();

            var data = {
                username: $("#username").val(),
                password: $("#password").val(),
                csrf: $("#csrf").val()
            }

            data.password = CryptoJS.SHA512(data.password).toString();


            $.ajax({
                type: "POST",
                url: "config/Ajax.php",
                data: {
                    action: 'userLogin',
                    username: data.username,
                    password: data.password,
                    csrf: data.csrf
                },
                success: function(response){
                    if(response === "true"){
                        window.location.href = 'index.php';
                    } else {
                        alert(response)
                    }
                }

            })

        })
    });
</script>
</body>
</html>