<?php
session_start();

require_once __DIR__ . '/../includes/koneksi.php';

// Ambil dan bersihkan data dari form
$judul     = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun     = $_POST['tahun'] ?? '';
$isbn      = trim($_POST['isbn'] ?? '');
$stok      = $_POST['stok'] ?? '';
$kategori  = trim($_POST['kategori'] ?? '');

// Validasi Server-Side
$errors = [];

if ($judul === '') {
    $errors[] = "Judul wajib diisi.";
}
if ($pengarang === '') {
    $errors[] = "Pengarang wajib diisi.";
}
if (!is_numeric($tahun) || $tahun < 1900 || $tahun > 2026) {
    $errors[] = "Tahun harus di antara 1900-2026.";
}
if ($isbn !== '' && !preg_match('/^[0-9\-]+$/', $isbn)) {
    $errors[] = "Format ISBN tidak valid (hanya boleh angka dan tanda hubung).";
}
if (!is_numeric($stok) || $stok < 0) {
    $errors[] = "Stok tidak boleh negatif.";
}

// Jika ada error validasi, kembalikan ke form tambah
if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type'  => 'error', 
        'pesan' => implode(' ', $errors)
    ];
    header('Location: tambah.php');
    exit;
}

// Simpan Data ke Database PostgreSQL
try {
    $sql = "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori) 
            VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':judul'     => $judul,
        ':pengarang' => $pengarang,
        ':tahun'     => (int) $tahun,
        ':isbn'      => $isbn,
        ':stok'      => (int) $stok,
        ':kategori'  => $kategori,
    ]);

    $_SESSION['flash'] = [
        'type'  => 'success', 
        'pesan' => 'Buku berhasil ditambahkan.'
    ];
    header('Location: list.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'  => 'error', 
        'pesan' => 'Gagal menyimpan data: ' . $e->getMessage()
    ];
    header('Location: tambah.php');
    exit;
}