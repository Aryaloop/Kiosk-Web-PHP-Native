<?php
session_start();

if (!isset($_SESSION['karyawan_id'])) {
    header('Location: login.php');
    exit();
}

include('../db.php');

$idKaryawan = $_SESSION['karyawan_id'];

try {
    // Mulai transaksi
    $db->beginTransaction();
    $transactionStarted = true;

    // Ambil semua item dari tabel cart berdasarkan IDKARYAWAN
    $stmt_cart = $db->prepare('SELECT * FROM cart WHERE IDKARYAWAN = ?');
    $stmt_cart->execute([$idKaryawan]);
    $cartItems = $stmt_cart->fetchAll();

    if (!empty($cartItems)) {
        // Array untuk menyimpan item dari cart
        $items = [];
        $totalHarga = 0; // Inisialisasi total harga

        foreach ($cartItems as $item) {
            // Hitung harga total
            $hargaTotal = $item['HARGA'] * $item['JUMLAH'];
            $totalHarga += $hargaTotal; // Tambahkan harga total ke totalHarga

            // Tambahkan item ke dalam array $items
            $items[] = [
                'IDMENU' => $item['IDMENU'],
                'NAMAMENU' => $item['NAMAMENU'],
                'KATEGORI' => $item['KATEGORI'],
                'HARGA' => $item['HARGA'],
                'JUMLAH' => $item['JUMLAH'],
                'HARGATOTAL' => $hargaTotal
            ];
        }

        // Konversi array $items ke format JSON
        $itemsJSON = json_encode($items);

        // Simpan total harga keseluruhan
        $stmt_pesanan = $db->prepare('INSERT INTO pesanan (IDCART, IDKARYAWAN, STATUS, TANGGAL, ITEMS_JSON, TOTAL_HARGA) VALUES (?, ?, ?, ?, ?, ?)');
        // Menggunakan ID dari cart pertama untuk IDCART
        $stmt_pesanan->execute([$cartItems[0]['IDCART'], $idKaryawan, 'pending', date('Y-m-d H:i:s'), $itemsJSON, $totalHarga]);

        // Commit transaksi
        $db->commit();

        // Setelah commit, lakukan delete dari tabel cart
        $stmt_delete = $db->prepare('DELETE FROM cart WHERE IDKARYAWAN = ?');
        $stmt_delete->execute([$idKaryawan]);

        // Redirect ke halaman sukses atau halaman lain yang diinginkan
        header('Location: index.php');
        exit();
    } else {
        throw new Exception("Keranjang kosong.");
    }
} catch (Exception $e) {
    if (isset($transactionStarted) && $transactionStarted) {
        // Rollback transaksi jika terjadi kesalahan dan transaksi telah dimulai
        $db->rollBack();
    }
    echo "Gagal: " . $e->getMessage();
}
?>
