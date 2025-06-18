<?php
session_start();
if ($_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Your Caffe</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link btn btn-danger text-white rounded-pill" href="../logout.php">Logout <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Dashboard Admin</h1>
        <p>Welcome, Admin!</p>
        <div class="row mt-4">
            <div class="col-md-4">
                <a href="kelola_karyawan.php" class="btn btn-primary btn-block">Kelola Karyawan</a>
            </div>
            <div class="col-md-4">
                <a href="kelola_menu.php" class="btn btn-primary btn-block">Kelola Menu</a>
            </div>
            <div class="col-md-4">
                <a href="penjualan_harian.php" class="btn btn-primary btn-block">Kelola Penjualan</a>
            </div>
        </div>
    </div>
    <?php include('../templates/footer.php'); ?>
</body>

</html>