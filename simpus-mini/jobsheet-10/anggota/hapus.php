<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = $_POST['id'] ?? null;

if ($id) {
    // Tambahan: Pastikan data anggota ada sebelum dihapus
    $stmtCheck = $pdo->prepare("SELECT id FROM anggota WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    $anggota = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($anggota) {
        // Logika lama penghapusan data tetap sama
        $stmt = $pdo->prepare("DELETE FROM anggota WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil dihapus.'];
    } else {
        // Tambahan: Pesan error jika ID anggota tidak ditemukan
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data anggota tidak ditemukan.'];
    }
} else {
    // Tambahan: Pesan error jika ID tidak dikirim
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID anggota tidak valid.'];
}

header('Location: list.php');
exit;