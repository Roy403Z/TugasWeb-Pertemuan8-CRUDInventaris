<?php
session_start();
require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

// Fetch Existing Product Data (Pre-filled)
$stmt = $db->prepare("SELECT * FROM produk WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$produk = $stmt->fetch();

if (!$produk) {
    $_SESSION['flash_message'] = 'Data produk tidak ditemukan!';
    $_SESSION['flash_type'] = 'danger';
    header('Location: index.php');
    exit();
}

$kategoriList = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
$supplierList = $db->query("SELECT * FROM supplier ORDER BY nama_supplier ASC")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = trim($_POST['nama_produk'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    $kategori_id = trim($_POST['kategori_id'] ?? '');
    $supplier_id = trim($_POST['supplier_id'] ?? '');

    if (empty($nama_produk) || empty($harga) || empty($stok) || empty($kategori_id) || empty($supplier_id)) {
        $error = 'Semua bidang form wajib diisi!';
    } elseif (!is_numeric($harga) || !is_numeric($stok)) {
        $error = 'Harga dan Stok harus berupa angka!';
    } else {
        // Prepared Statement Update
        $sql = "UPDATE produk SET nama_produk = :nama_produk, harga = :harga, stok = :stok, 
                kategori_id = :kategori_id, supplier_id = :supplier_id WHERE id = :id";
        $stmtUpdate = $db->prepare($sql);
        $stmtUpdate->bindValue(':nama_produk', $nama_produk, PDO::PARAM_STR);
        $stmtUpdate->bindValue(':harga', $harga, PDO::PARAM_STR);
        $stmtUpdate->bindValue(':stok', $stok, PDO::PARAM_INT);
        $stmtUpdate->bindValue(':kategori_id', $kategori_id, PDO::PARAM_INT);
        $stmtUpdate->bindValue(':supplier_id', $supplier_id, PDO::PARAM_INT);
        $stmtUpdate->bindValue(':id', $id, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            $_SESSION['flash_message'] = 'Data produk berhasil diperbarui!';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php');
            exit();
        } else {
            $error = 'Gagal memperbarui data produk.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-dark text-white min-vh-100 d-flex flex-column py-5">

    <div class="container my-auto" style="max-width: 600px;">
        <div class="card bg-secondary text-white border-0 shadow-lg p-4 p-sm-5">
            <h3 class="fw-bold mb-3 text-warning"><i class="bi bi-pencil-square me-2"></i>Edit Data Produk</h3>
            <p class="text-secondary small mb-4">Ubah detail barang inventaris di bawah ini.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="edit.php?id=<?= $id; ?>" method="POST">
                <div class="mb-3">
                    <label for="nama_produk" class="form-label fw-bold">Nama Produk</label>
                    <input type="text" class="form-control bg-dark text-white border-0" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']); ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="harga" class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" step="0.01" class="form-control bg-dark text-white border-0" id="harga" name="harga" value="<?= htmlspecialchars($produk['harga']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="stok" class="form-label fw-bold">Stok Barang</label>
                        <input type="number" class="form-control bg-dark text-white border-0" id="stok" name="stok" value="<?= htmlspecialchars($produk['stok']); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="kategori_id" class="form-label fw-bold">Kategori Produk</label>
                    <select class="form-select bg-dark text-white border-0" id="kategori_id" name="kategori_id" required>
                        <?php foreach ($kategoriList as $kat): ?>
                            <option value="<?= $kat['id']; ?>" <?= ($produk['kategori_id'] == $kat['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="supplier_id" class="form-label fw-bold">Supplier Barang</label>
                    <select class="form-select bg-dark text-white border-0" id="supplier_id" name="supplier_id" required>
                        <?php foreach ($supplierList as $sup): ?>
                            <option value="<?= $sup['id']; ?>" <?= ($produk['supplier_id'] == $sup['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($sup['nama_supplier']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="index.php" class="btn btn-outline-light w-50">Batal</a>
                    <button type="submit" class="btn btn-warning w-50 fw-bold text-dark">Perbarui Produk</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>