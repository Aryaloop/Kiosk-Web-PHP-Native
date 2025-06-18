<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk memeriksa apakah pengguna adalah pembeli
function isBuyer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'buyer';
}
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-md bg-dark navbar-dark">
    <a class="navbar-brand" href="#">Nama Toko Anda</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <!-- Menampilkan ikon keranjang jika pengguna adalah pembeli -->
            <?php if (isBuyer()) { ?>
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#cartModal">
                    <i class="fa fa-shopping-cart" style="color: yellow;"></i> Keranjang <span class="badge badge-light">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
</nav>

<!-- Modal Keranjang -->
<?php if (isBuyer()) { ?>
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Keranjang Belanja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Menampilkan isi keranjang jika tidak kosong -->
                <?php if (!empty($_SESSION['cart'])) { ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalHarga = 0;
                        foreach ($_SESSION['cart'] as $index => $item) {
                            $totalHarga += $item['harga'] * $item['jumlah'];
                        ?>
                        <tr>
                            <td><?php echo $item['nama']; ?></td>
                            <td><?php echo $item['harga']; ?></td>
                            <td><?php echo $item['jumlah']; ?></td>
                            <td><?php echo $item['harga'] * $item['jumlah']; ?></td>
                            <td>
                                <form method="post" action="hapus_item_keranjang.php">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="text-right">
                    <h4>Total: <?php echo $totalHarga; ?></h4>
                </div>
                <?php } else { ?>
                <p>Keranjang belanja kosong.</p>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <a href="pembeli/checkout.php" class="btn btn-primary">Lanjutkan Pembayaran</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
