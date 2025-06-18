<?php
session_start();
if (!isset($_SESSION['karyawan_id']) || $_SESSION['user_role'] != 'karyawan') {
    header('Location: login.php');
    exit();
}

include('../db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $idPesanan = $_POST['IDPESANAN'];
        $status = $_POST['STATUS'];

        $stmt = $db->prepare('UPDATE pesanan SET STATUS = ? WHERE IDPESANAN = ?');
        $stmt->execute([$status, $idPesanan]);
    }
}

$orders = $db->query('SELECT * FROM pesanan');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan</title>
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
                    <a class="nav-link" href="#">Manage Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_menu.php">View Menu</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Kelola Pesanan</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>ID Cart</th>
                    <th>ID Karyawan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                    <th>Items</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch(PDO::FETCH_ASSOC)) { 
                    $items = json_decode($order['ITEMS_JSON'], true);
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['IDPESANAN']); ?></td>
                        <td><?php echo htmlspecialchars($order['IDCART']); ?></td>
                        <td><?php echo htmlspecialchars($order['IDKARYAWAN']); ?></td>
                        <td><?php echo htmlspecialchars($order['STATUS']); ?></td>
                        <td><?php echo htmlspecialchars($order['TANGGAL']); ?></td>
                        <td><?php echo htmlspecialchars($order['TOTAL_HARGA']); ?></td>
                        <td>
                            <ul>
                                <?php foreach ($items as $item) { ?>
                                    <li><?php echo htmlspecialchars($item['NAMAMENU']) . ' (Jumlah: ' . htmlspecialchars($item['JUMLAH']) . ', Harga Total: ' . htmlspecialchars($item['HARGATOTAL']) . ')'; ?></li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="IDPESANAN" value="<?php echo htmlspecialchars($order['IDPESANAN']); ?>">
                                <select name="STATUS" class="form-control">
                                    <option value="Pending" <?php if ($order['STATUS'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Processed" <?php if ($order['STATUS'] == 'Processed') echo 'selected'; ?>>Processed</option>
                                    <option value="Completed" <?php if ($order['STATUS'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Cancelled" <?php if ($order['STATUS'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary mt-1">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include('../templates/footer.php'); ?>
</body>
</html>
