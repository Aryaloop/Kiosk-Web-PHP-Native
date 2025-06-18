<?php
session_start();
if ($_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include('../db.php');

function generateKaryawanID($db)
{
    // Query to get the latest IDKARYAWAN
    $result = $db->query("SELECT MAX(IDKARYAWAN) AS max_id FROM Karyawan");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $max_id = $row['max_id'];
    // Generating new IDKARYAWAN
    if ($max_id) {
        $new_id = intval($max_id) + 1;
    } else {
        $new_id = 1;
    }
    return $new_id;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Generate new IDKARYAWAN
        $idKaryawan = generateKaryawanID($db);
        $namaPengguna = $_POST['namaPengguna'];
        $password = $_POST['password'];

        $stmt = $db->prepare('INSERT INTO Karyawan (IDKARYAWAN, NAMAPENGGUNA, PASSWORD) VALUES (?, ?, ?)');
        $stmt->execute([$idKaryawan, $namaPengguna, $password]);
    } elseif (isset($_POST['delete'])) {
        $idKaryawan = $_POST['idKaryawan'];

        $stmt = $db->prepare('DELETE FROM Karyawan WHERE IDKARYAWAN = ?');
        $stmt->execute([$idKaryawan]);
    } elseif (isset($_POST['edit'])) {
        $idKaryawan = $_POST['idKaryawan'];
        $namaPengguna = $_POST['namaPengguna'];
        $password = $_POST['password'];

        $stmt = $db->prepare('UPDATE Karyawan SET NAMAPENGGUNA = ?, PASSWORD = ? WHERE IDKARYAWAN = ?');
        $stmt->execute([$namaPengguna, $password, $idKaryawan]);
    }
}

$karyawans = $db->query('SELECT * FROM Karyawan');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kelola Karyawan</title>
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
                    <a class="nav-link" href="index.php">Back</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">Kelola Karyawan<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kelola_menu.php">Kelola Menu</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Kelola Karyawan</h1>
        <form method="post" action="kelola_karyawan.php">
            <div class="form-group">
                <label for="namaPengguna">Nama Pengguna</label>
                <input type="text" class="form-control" id="namaPengguna" name="namaPengguna" required>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Tambah Karyawan</button>
        </form>
        <hr>
        <h2>Daftar Karyawan</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Karyawan</th>
                    <th>Nama Pengguna</th>
                    <th>Kata Sandi</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($karyawan = $karyawans->fetch()) { ?>
                    <tr>
                        <td><?php echo $karyawan['IDKARYAWAN']; ?></td>
                        <td><?php echo $karyawan['NAMAPENGGUNA']; ?></td>
                        <td><?php echo $karyawan['PASSWORD']; ?></td>
                        <td>
                            <form method="post" action="kelola_karyawan.php" class="form-inline">
                                <input type="hidden" name="idKaryawan" value="<?php echo $karyawan['IDKARYAWAN']; ?>">
                                <input type="text" class="form-control mb-2 mr-sm-2" name="namaPengguna" value="<?php echo $karyawan['NAMAPENGGUNA']; ?>" required>
                                <input type="password" class="form-control mb-2 mr-sm-2" name="password" value="<?php echo $karyawan['PASSWORD']; ?>" required>
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
