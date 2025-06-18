<?php
session_start();
if (!isset($_SESSION['karyawan_id'])) {
    header('Location: login.php');
    exit();
}

include('../templates/navbar.php');

// Tampilkan pesan keberhasilan jika ada
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Karyawan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container-fluid">
    <div class="container">
        <h1>Dashboard Karyawan</h1>
        <p>Welcome, Karyawan!</p>
        <a href="view_menu.php" class="btn btn-primary">View Menu</a>
        <a href="kelola_pesanan.php" class="btn btn-primary">Manage Orders</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
    <?php include('../templates/footer.php'); ?>
</body>
</html>