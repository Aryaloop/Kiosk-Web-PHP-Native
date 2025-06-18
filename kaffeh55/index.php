<?php
include('db.php');
include('templates/navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Caffe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Your Caffe</h1>
        <p><a href="admin/index.php">Admin</a></p>
        <p><a href="karyawan/index.php">Karyawan</a></p>
        <p><a href="pembeli/index.php">Pembeli</a></p>
    </div>
    <?php include('templates/footer.php'); ?>
</body>
</html>
