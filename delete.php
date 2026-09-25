<?php
session_start();
require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit();
}

try {
    // Bonus: Database Transaction
    $db->beginTransaction();

    // Prepared Statement Delete
    $stmt = $db->prepare("DELETE FROM produk WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $db->commit();

    $_SESSION['flash_message'] = 'Produk berhasil dihapus dari sistem inventaris!';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['flash_message'] = 'Gagal menghapus produk: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: index.php');
exit();
?>