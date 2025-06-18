<?php
include('../db.php');

function generateHariID($db)
{
    $prefix = "HARI";
    $result = $db->query("SELECT MAX(SUBSTRING(IDHARI, 5)) AS max_id FROM transaksi WHERE TANGGAL = CURDATE()");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $max_id = $row['max_id'];
    if ($max_id) {
        $new_id = $prefix . str_pad((intval($max_id) + 1), 6, '0', STR_PAD_LEFT);
    } else {
        $new_id = $prefix . '000001';
    }
    return $new_id;
}
function insertDailyTransactions($db, $date)
{
    // Select all completed orders for the given date
    $stmt_pesanan = $db->prepare('SELECT * FROM pesanan WHERE TANGGAL = ? AND STATUS = "Completed"');
    $stmt_pesanan->execute([$date]);
    $pesananList = $stmt_pesanan->fetchAll(PDO::FETCH_ASSOC);

    if ($pesananList) {
        // Generate a unique ID for the given date
        $idHari = generateHariID($db);

        // Insert each order into the transactions table
        foreach ($pesananList as $pesanan) {
            $stmt_transaksi = $db->prepare('INSERT INTO transaksi (IDTRANSAKSI, IDPESANAN, NAMAMENU, HARGA, KATEGORI, JUMLAH, HARGATOTAL, TANGGAL, IDHARI) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt_transaksi->execute([
                $pesanan['IDPESANAN'],
                $pesanan['NAMAMENU'],
                $pesanan['HARGA'],
                $pesanan['KATEGORI'],
                $pesanan['JUMLAH'],
                $pesanan['HARGATOTAL'],
                $pesanan['TANGGAL'],
                $idHari
            ]);
        }

        echo "Transaksi untuk tanggal $date telah berhasil dimasukkan ke dalam tabel transaksi.";
        return true;
    } else {
        echo "Tidak ada transaksi yang selesai pada tanggal $date.";
        return false;
    }
}


function displayTransactions($db, $date)
{
    $stmt = $db->prepare('SELECT * FROM transaksi WHERE TANGGAL = ?');
    $stmt->execute([$date]);
    $transaksiList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($transaksiList) {
        echo "<table class='table table-bordered'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID Transaksi</th>";
        echo "<th>ID Pesanan</th>";
        echo "<th>Nama Menu</th>";
        echo "<th>Harga</th>";
        echo "<th>Kategori</th>";
        echo "<th>Jumlah</th>";
        echo "<th>Harga Total</th>";
        echo "<th>Tanggal</th>";
        echo "<th>ID Hari</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($transaksiList as $transaksi) {
            echo "<tr>";
            echo "<td>{$transaksi['IDTRANSAKSI']}</td>";
            echo "<td>{$transaksi['IDPESANAN']}</td>";
            echo "<td>{$transaksi['NAMAMENU']}</td>";
            echo "<td>{$transaksi['HARGA']}</td>";
            echo "<td>{$transaksi['KATEGORI']}</td>";
            echo "<td>{$transaksi['JUMLAH']}</td>";
            echo "<td>{$transaksi['HARGATOTAL']}</td>";
            echo "<td>{$transaksi['TANGGAL']}</td>";
            echo "<td>{$transaksi['IDHARI']}</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "Tidak ada transaksi yang tercatat pada tanggal $date.";
    }
}

if (isset($_POST['date'])) {
    $date = $_POST['date'];
    if (insertDailyTransactions($db, $date)) {
        displayTransactions($db, $date);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kelola Penjualan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* CSS untuk body dan html */
        body,
        html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* CSS untuk container utama */
        .main-container {
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* CSS untuk konten di dalam container */
        .content {
            flex: 1;
            /* Mengisi sisa tinggi dengan konten */
            padding: 20px;
            /* Padding untuk konten */
        }

        .navbar {
            padding: 0;
            /* Menghilangkan padding default pada navbar */
        }
    </style>
</head>

<body>
    <div class="main-container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light p-0">
            <a class="navbar-brand" href="#">Your Caffe</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Back </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kelola_karyawan.php">Kelola Karyawan</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Kelola Menu<span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid content">
            <h1>Kelola penjualan</h1>
            <form method="post" action="kelola_menu.php" enctype="multipart/form-data">
                <!-- Form tambah menu tetap menggunakan container -->
                <!-- Isi form -->
            </form>
            <hr>
            <h2>Daftar Transaksi Harian</h2>
            <table class="table">
                <!-- Tabel daftar menu tetap menggunakan container -->
                <!-- Isi tabel -->
            </table>
        </div>

        <?php include('../templates/footer.php'); ?>
    </div>
</body>

</html>