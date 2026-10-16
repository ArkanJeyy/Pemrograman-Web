<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
check_role(['admin']);
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = $_POST['id'] ?? null;

if ($id) {
    try {
        // Mulai Transaksi Database
        $pdo->beginTransaction();

        // 1. Cek keberadaan anggota
        $stmtCheck = $pdo->prepare("SELECT id FROM anggota WHERE id = :id");
        $stmtCheck->execute(['id' => $id]);
        $anggota = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($anggota) {
            // 2. Hapus terlebih dahulu transaksi peminjaman terkait (jika ada relasi ke tabel peminjaman)
            $stmtDelPeminjaman = $pdo->prepare("DELETE FROM peminjaman WHERE anggota_id = :id");
            $stmtDelPeminjaman->execute(['id' => $id]);

            // 3. Hapus data anggota
            $stmtDelAnggota = $pdo->prepare("DELETE FROM anggota WHERE id = :id");
            $stmtDelAnggota->execute(['id' => $id]);

            // Commit perubahan ke database
            $pdo->commit();

            $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota dan riwayat terkait berhasil dihapus.'];
        } else {
            $pdo->rollBack();
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data anggota tidak ditemukan.'];
        }
    } catch (PDOException $e) {
        // Batalkan transaksi jika terjadi kesalahan
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Tampilkan pesan error aktual dari PostgreSQL untuk memudahkan pemeliharaan
        $_SESSION['flash'] = [
            'type' => 'error', 
            'pesan' => 'Gagal menghapus dari database: ' . $e->getMessage()
        ];
    }
} else {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID anggota tidak valid.'];
}

header('Location: list.php');
exit;