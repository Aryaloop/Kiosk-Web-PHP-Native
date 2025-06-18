<?php
session_start();

// Periksa apakah karyawan telah login
if (!isset($_SESSION['karyawan_id']) || $_SESSION['user_role'] != 'karyawan') {
    header('Location: login.php');
    exit();
}

include('../db.php');

// Query untuk mengambil semua data menu dari database
$menus = $db->query('SELECT * FROM menu');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Menu</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Your Caffe</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Back <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kelola_pesanan.php">Manage Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">View Menu</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Menu</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Menu</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($menu = $menus->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($menu['IDMENU']); ?></td>
                        <td><?php echo htmlspecialchars($menu['NAMAMENU']); ?></td>
                        <td><?php echo htmlspecialchars($menu['HARGA']); ?></td>
                        <td><?php echo htmlspecialchars($menu['KATEGORI']); ?></td>
                        <td>
                            <?php
                            if ($menu['GAMBAR']) {
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($menu['GAMBAR']) . '" width="50">';
                            } else {
                                echo 'No Image';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include('../templates/footer.php'); ?>
</body>

</html>