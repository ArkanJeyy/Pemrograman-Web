<?php
session_start();
require __DIR__ . '/../includes/auth.php';
check_role(['admin']);
require __DIR__ . '/../includes/koneksi.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = $_POST['id'] ?? null;

if ($id) {
    $stmtCheck = $pdo->prepare("SELECT id FROM anggota WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    $anggota = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($anggota) {
        $stmt = $pdo->prepare("DELETE FROM anggota WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data anggota tidak ditemukan.'];
    }
} else {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID anggota tidak valid.'];
}

header('Location: list.php');
exit;