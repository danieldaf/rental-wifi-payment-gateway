<?php

include_once 'config/init.php';
$voucher = new Vouchers();

if (!$auth->isLoggedIn()){
    header('Location: login.php');
}

if (!$auth->isAdmin()){
    header("Location: index.php");
}

$page = "vouchers";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codes = $_POST['code'];
    $category = $_POST['code_category'];

    $filteredCodes = array_filter($codes); // Remove empty values from the array
    if (!empty($filteredCodes)) {
        $code = implode(", ", $filteredCodes);
    }


    $stmt = $db->prepare("INSERT INTO vouchers (code, category) VALUES (?, ?)");
    $stmt->execute([$code, $category]);

    header("Location: vouchers.php?success=true");
    exit();
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vouchers | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
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

                    <?php

                    if (isset($_GET['success'])):
                    ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Code added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        Add Code
                    </button>

                    <div class="table-responsive">
                        <table class="table table-hover" id="myTable">
                            <thead>
                            <tr>
                                <th scope="col">Code</th>
                                <th scope="col">Duration</th>
                                <th scope="col">Price</th>
                                <th scope="col">Description</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $admin = new Admin();

                            $voucher_data = $admin->fetchAllVouchers();


                            foreach ($voucher_data as $res):
                                $code = str_contains($res['code'], ',') ? implode("<br>", explode(', ', $res['code'])) : $res['code'];

                                ?>

                            <tr>
                                <td> <?= $code ?></td>
                                <td> <?= $res['duration'] ?></td>
                                <td> <?= $res['price'] ?></td>
                                <td> <?= $res['voucher_description'] ?></td>
                                <td> <?= $res['status'] ?></td>
                                <td>

                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary edit_code" data-id="<?= $res['voucher_id'] ?>"><i class="far fa-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete_code" data-id="<?= $res['voucher_id'] ?>"><i class="far fa-trash"></i></button>
                                    </div>

                                </td>
                            </tr>
                            <?php
                            endforeach;
                            ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Codes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="vouchers.php" method="post">


                    <div class="mb-3">
                        <label for="code_category">Code Category</label>
                        <select id="code_category" name="code_category" class="form-control" onchange="toggleCodeInputs()">
                            <option selected disabled>Select Code Category</option>
                            <?php
                            $voucher_cat = new Admin();

                            $category = $voucher_cat->fetchAllCategory();

                            foreach ($category as $cat):
                                ?>
                                <option value="<?= $cat['category'] ?>"><?= $cat['voucher_description'] ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="code">Enter Code</label>
                        <div id="codeInputs">
                            <input type="number" name="code[]" id="code" class="form-control" placeholder="Enter Voucher Code" autofocus>
                        </div>
                    </div>



                    <div class="mb-3">
                        <button type="submit" name="saveCode" class="btn btn-success">Save</button>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script>
    const dataTable = new simpleDatatables.DataTable("#myTable");

    // Fetch the journal data
    $(document).on('click', '.edit', function (){

        let journal_id = $(this).data('id');
        let editModal = new bootstrap.Modal('#edit_modal')
        $.ajax({
            type: 'POST',
            url: 'config/Ajax.php',
            data: {
                action: 'fetchJournalByIDCrud',
                journal_id: journal_id
            },
            success: function (res) {

                var data = JSON.parse(res)

                editModal.show();

            }
        })

    });



        function toggleCodeInputs() {
        const codeCategory = document.getElementById('code_category');
        const codeInputs = document.getElementById('codeInputs');

        if (codeCategory.value === '3') {
            codeInputs.innerHTML = `
                <input type="number" name="code[]" class="form-control" placeholder="Enter Voucher Code 1">
                <input type="number" name="code[]" class="form-control" placeholder="Enter Voucher Code 2">
                <input type="number" name="code[]" class="form-control" placeholder="Enter Voucher Code 3">
                <input type="number" name="code[]" class="form-control" placeholder="Enter Voucher Code 4">
                <input type="number" name="code[]" class="form-control" placeholder="Enter Voucher Code 5">
            `;
    } else {
        codeInputs.innerHTML = `
                <input type="number" name="code[]" id="code" class="form-control" placeholder="Enter Voucher Code" autofocus>
            `;
    }
    }

    $(document).on('click', '.delete', function () {

        let journal_id = $(this).data('id');
        let name = $(this).data('name');

        var prompt = 'Are you sure you want to delete ' + name

        alertify.confirm('Are you sure?', prompt,
            function(){
                $.ajax({
                    type: 'POST',
                    url: 'config/Ajax.php',
                    data: {
                        action: 'deleteJournal',
                        journal_id: journal_id
                    }, success: function (res){
                        if (res === "true"){
                            notyf.success('Your changes have been successfully saved!');
                            initJournalTable();
                        } else {
                            notyf.error(res)
                        }
                    }
                })
            }, function(){ alertify.error('Cancel')});
    });
</script>
</body>
</html>