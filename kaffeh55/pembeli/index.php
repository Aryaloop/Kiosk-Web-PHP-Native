<?php
session_start();

if (!isset($_SESSION['karyawan_id'])) {
    header('Location: login.php');
    exit();
}

include('../db.php');

function generatePesananID($db)
{
    $prefix = "PSN";
    $result = $db->query("SELECT MAX(SUBSTRING(IDPESANAN, 4)) AS max_id FROM pesanan");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $max_id = $row['max_id'];
    if ($max_id) {
        $new_id = $prefix . str_pad((intval($max_id) + 1), 6, '0', STR_PAD_LEFT);
    } else {
        $new_id = $prefix . '000001';
    }
    return $new_id;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['IDMENU']) && isset($_POST['JUMLAH'])) {
        $idMenu = $_POST['IDMENU'];
        $jumlah = $_POST['JUMLAH'];
        $idKaryawan = $_SESSION['karyawan_id'];

        $stmt_menu = $db->prepare('SELECT * FROM menu WHERE IDMENU = ?');
        $stmt_menu->execute([$idMenu]);
        $menu = $stmt_menu->fetch();

        if ($menu) {
            $stmt_check = $db->prepare('SELECT * FROM cart WHERE IDMENU = ? AND IDKARYAWAN = ?');
            $stmt_check->execute([$idMenu, $idKaryawan]);
            $existingCartItem = $stmt_check->fetch();

            if ($existingCartItem) {
                $stmt_update = $db->prepare('UPDATE cart SET JUMLAH = JUMLAH + ? WHERE IDCART = ?');
                $stmt_update->execute([$jumlah, $existingCartItem['IDCART']]);
            } else {
                $stmt_insert = $db->prepare('INSERT INTO cart (IDKARYAWAN, IDMENU, NAMAMENU, HARGA, KATEGORI, JUMLAH, GAMBAR) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt_insert->execute([$idKaryawan, $menu['IDMENU'], $menu['NAMAMENU'], $menu['HARGA'], $menu['KATEGORI'], $jumlah, $menu['GAMBAR']]);
            }
        }

        header('Location: index.php');
        exit();
    } elseif (isset($_POST['update_cart'])) {
        if (isset($_POST['IDCART']) && isset($_POST['JUMLAH'])) {
            $idCart = $_POST['IDCART'];
            $jumlah = $_POST['JUMLAH'];

            if ($jumlah > 0) {
                $stmt_update = $db->prepare('UPDATE cart SET JUMLAH = ? WHERE IDCART = ?');
                $stmt_update->execute([$jumlah, $idCart]);
            } else {
                $stmt_delete = $db->prepare('DELETE FROM cart WHERE IDCART = ?');
                $stmt_delete->execute([$idCart]);
            }

            header('Location: index.php');
            exit();
        }
    } elseif (isset($_POST['clear_cart'])) {
        $idKaryawan = $_SESSION['karyawan_id'];

        $stmt_check_pesanan = $db->prepare('SELECT COUNT(*) FROM pesanan WHERE IDKARYAWAN = ? AND IDCART IN (SELECT IDCART FROM cart WHERE IDKARYAWAN = ?)');
        $stmt_check_pesanan->execute([$idKaryawan, $idKaryawan]);
        $pesananCount = $stmt_check_pesanan->fetchColumn();

        if ($pesananCount == 0) {
            $stmt_clear = $db->prepare('DELETE FROM cart WHERE IDKARYAWAN = ?');
            $stmt_clear->execute([$idKaryawan]);
        } else {
            echo "Tidak dapat menghapus keranjang karena ada pesanan terkait.";
        }

        header('Location: index.php');
        exit();
    }
}

$menus = $db->query('SELECT * FROM menu');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Menu Pembelian</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Caffe</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#cartModal">Cart (<?php echo $db->query('SELECT COUNT(*) FROM cart WHERE IDKARYAWAN = ' . $_SESSION['karyawan_id'])->fetchColumn(); ?>)</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Menu Pembelian</h1>
        <div class="row">
            <?php while ($menu = $menus->fetch()) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <?php
                        $gambarBase64 = base64_encode($menu['GAMBAR']);
                        ?>
                        <img src="data:image/jpeg;base64,<?php echo $gambarBase64; ?>" class="card-img-top" alt="<?php echo $menu['NAMAMENU']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $menu['NAMAMENU']; ?></h5>
                            <p class="card-text"><?php echo $menu['HARGA']; ?></p>
                            <form method="post" action="index.php">
                                <input type="hidden" name="IDMENU" value="<?php echo $menu['IDMENU']; ?>">
                                <input type="number" name="JUMLAH" value="1" min="1" class="form-control mb-2" style="width: 60px;">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Cart</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $idKaryawan = $_SESSION['karyawan_id'];
                    $stmt_cart = $db->prepare('SELECT * FROM cart WHERE IDKARYAWAN = ?');
                    $stmt_cart->execute([$idKaryawan]);
                    $cartItems = $stmt_cart->fetchAll();
                    $totalHarga = 0;

                    if (!empty($cartItems)) {
                        foreach ($cartItems as $item) {
                            $totalHarga += $item['HARGA'] * $item['JUMLAH'];
                            echo "<div>
                                {$item['NAMAMENU']} 
                                <form method='post' action='index.php'>
                                    <input type='hidden' name='IDCART' value='{$item['IDCART']}'>
                                    <input type='number' name='JUMLAH' value='{$item['JUMLAH']}' min='1' class='form-control' style='width: 60px; display: inline-block;'>
                                    <button type='submit' name='update_cart' class='btn btn-sm btn-primary'>Update</button>
                                </form> - " . ($item['HARGA'] * $item['JUMLAH']) . "
                            </div>";
                        }
                    } else {
                        echo "<div>Keranjang kosong</div>";
                    }
                    ?>
                    <div>Total: <?php echo $totalHarga; ?></div>
                </div>
                <!-- Add the form and button for checkout within the modal -->
                <div class="modal-footer">
                    <form method="post" action="checkout.php">
                        <button type="submit" name="checkout" class="btn btn-success">Checkout</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" onclick="clearCart()">Clear Cart</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>