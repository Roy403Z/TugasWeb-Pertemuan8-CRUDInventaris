<?php
session_start();
require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

// Search & Pagination Setup
$search = trim($_GET['search'] ?? '');
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count Total Rows with Prepared Statements
if ($search !== '') {
    $countSql = "SELECT COUNT(*) FROM produk p 
                 JOIN kategori k ON p.kategori_id = k.id 
                 JOIN supplier s ON p.supplier_id = s.id 
                 WHERE p.nama_produk LIKE :search OR k.nama_kategori LIKE :search OR s.nama_supplier LIKE :search";
    $stmtCount = $db->prepare($countSql);
    $stmtCount->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmtCount->execute();
    $totalRows = $stmtCount->fetchColumn();
} else {
    $countSql = "SELECT COUNT(*) FROM produk";
    $totalRows = $db->query($countSql)->fetchColumn();
}

$totalPages = ceil($totalRows / $limit);

// Fetch Products with JOIN 2 Tables
if ($search !== '') {
    $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier 
            FROM produk p 
            JOIN kategori k ON p.kategori_id = k.id 
            JOIN supplier s ON p.supplier_id = s.id 
            WHERE p.nama_produk LIKE :search OR k.nama_kategori LIKE :search OR s.nama_supplier LIKE :search 
            ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produkList = $stmt->fetchAll();
} else {
    $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier 
            FROM produk p 
            JOIN kategori k ON p.kategori_id = k.id 
            JOIN supplier s ON p.supplier_id = s.id 
            ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produkList = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Inventaris Barang - UNIMED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-dark text-white min-vh-100 d-flex flex-column py-4">

    <div class="container my-auto">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary">
            <div>
                <h1 class="h3 fw-bold mb-0 text-info"><i class="bi bi-boxes me-2"></i>Sistem Inventaris Barang</h1>
                <p class="text-secondary small mb-0">Tugas Rutin 8 Pemrograman Web - Database `inventaris_db`</p>
            </div>
            <a href="create.php" class="btn btn-info fw-bold text-dark shadow-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Produk</a>
        </div>

        <!-- Flash Message (Redirect Pattern) -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="card bg-secondary text-white border-0 shadow-lg mb-4">
            <div class="card-body p-3">
                <form action="index.php" method="GET" class="row g-2">
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-dark text-white border-0" placeholder="Cari nama produk, kategori, atau supplier..." value="<?= htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-sm-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Cari</button>
                        <?php if ($search !== ''): ?>
                            <a href="index.php" class="btn btn-outline-light">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Product List (JOIN 2 Tabel) -->
        <div class="card bg-secondary border-0 shadow-lg overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-active text-info">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3">Nama Produk</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Supplier</th>
                            <th class="py-3">Harga</th>
                            <th class="py-3 text-center">Stok</th>
                            <th class="py-3 text-center px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($produkList) > 0): ?>
                            <?php foreach ($produkList as $index => $row): ?>
                                <tr>
                                    <td class="px-4 text-secondary"><?= $offset + $index + 1; ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_produk']); ?></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($row['nama_kategori']); ?></span></td>
                                    <td><span class="badge bg-secondary border"><?= htmlspecialchars($row['nama_supplier']); ?></span></td>
                                    <td class="text-warning fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $row['stok'] > 10 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?= htmlspecialchars($row['stok']); ?> Pcs
                                        </span>
                                    </td>
                                    <td class="text-center px-4">
                                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"><i class="bi bi-trash-fill"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">Data inventaris barang tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link bg-secondary text-white border-0" href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                            <a class="page-link <?= $page == $i ? 'bg-info text-dark fw-bold' : 'bg-secondary text-white'; ?> border-0" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link bg-secondary text-white border-0" href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <footer class="text-center text-secondary mt-auto pt-4 small">
        <p>&copy; 2026 Ade Roy Simbolon. Tugas Rutin 8 Pemrograman Web UNIMED.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>