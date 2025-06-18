<?php
session_start();
if ($_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include('../db.php');

// Function to generate new IDMenu
function generateMenuID($db)
{
    // Query to get the latest IDMENU
    $result = $db->query("SELECT MAX(IDMENU) AS max_id FROM Menu");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $max_id = $row['max_id'];
    // Generating new IDMENU
    return ($max_id) ? $max_id + 1 : 1;
}

// Code to add new entry to the Menu table
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Generating new IDMENU
        $idMenu = generateMenuID($db);
        // Other form data
        $namaMenu = $_POST['NAMAMENU'];
        $harga = $_POST['HARGA'];
        $kategori = $_POST['KATEGORI'];
        $gambar = file_get_contents($_FILES['GAMBAR']['tmp_name']);

        $stmt = $db->prepare('INSERT INTO Menu (IDMENU, NAMAMENU, HARGA, KATEGORI, GAMBAR) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$idMenu, $namaMenu, $harga, $kategori, $gambar]);

    } elseif (isset($_POST['edit'])) {
        // Editing existing menu entry
        $idMenu = $_POST['IDMENU'];
        $namaMenu = $_POST['NAMAMENU'];
        $harga = $_POST['HARGA'];
        $kategori = $_POST['KATEGORI'];
        $gambar = $_FILES['GAMBAR']['name'] ? file_get_contents($_FILES['GAMBAR']['tmp_name']) : null;

        if ($gambar) {
            $stmt = $db->prepare('UPDATE Menu SET NAMAMENU = ?, HARGA = ?, KATEGORI = ?, GAMBAR = ? WHERE IDMENU = ?');
            $stmt->execute([$namaMenu, $harga, $kategori, $gambar, $idMenu]);
        } else {
            $stmt = $db->prepare('UPDATE Menu SET NAMAMENU = ?, HARGA = ?, KATEGORI = ? WHERE IDMENU = ?');
            $stmt->execute([$namaMenu, $harga, $kategori, $idMenu]);
        }
    } elseif (isset($_POST['delete'])) {
        // Deleting menu entry
        $idMenu = $_POST['IDMENU'];
        $stmt = $db->prepare('DELETE FROM Menu WHERE IDMENU = ?');
        $stmt->execute([$idMenu]);
    }
}

$menus = $db->query('SELECT * FROM Menu');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kelola Menu</title>
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
    <div class="container">
        <h1>Kelola Menu</h1>
        <form method="post" action="kelola_menu.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="NAMAMENU">Nama Menu</label>
                <input type="text" class="form-control" id="NAMAMENU" name="NAMAMENU" required>
            </div>
            <div class="form-group">
                <label for="HARGA">Harga</label>
                <input type="number" class="form-control" id="HARGA" name="HARGA" required>
            </div>
            <div class="form-group">
                <label for="KATEGORI">Kategori</label>
                <input type="text" class="form-control" id="KATEGORI" name="KATEGORI" required>
            </div>
            <div class="form-group">
                <label for="GAMBAR">Gambar</label>
                <input type="file" class="form-control" id="GAMBAR" name="GAMBAR" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Tambah Menu</button>
        </form>
        <hr>
        <h2>Daftar Menu</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Menu</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Gambar</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($menu = $menus->fetch()) { ?>
                    <tr>
                        <td><?php echo $menu['IDMENU']; ?></td>
                        <td><?php echo $menu['NAMAMENU']; ?></td>
                        <td><?php echo $menu['HARGA']; ?></td>
                        <td><?php echo $menu['KATEGORI']; ?></td>
                        <td><img src="data:image/jpeg;base64,<?php echo base64_encode($menu['GAMBAR']); ?>" width="50"></td>
                        <td>
                            <form method="post" action="kelola_menu.php" enctype="multipart/form-data" class="form-inline">
                                <input type="hidden" name="IDMENU" value="<?php echo $menu['IDMENU']; ?>">
                                <input type="text" class="form-control mb-2 mr-sm-2" name="NAMAMENU" value="<?php echo $menu['NAMAMENU']; ?>" required>
                                <input type="number" class="form-control mb-2 mr-sm-2" name="HARGA" value="<?php echo $menu['HARGA']; ?>" required>
                                <input type="text" class="form-control mb-2 mr-sm-2" name="KATEGORI" value="<?php echo $menu['KATEGORI']; ?>" required>
                                <input type="file" class="form-control mb-2 mr-sm-2" name="GAMBAR">
                                <button type="submit" name="edit" class="btn btn-secondary mb-2 mr-sm-2">Edit</button>
                                <button type="submit" name="delete" class="btn btn-danger mb-2">Delete</button>
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
