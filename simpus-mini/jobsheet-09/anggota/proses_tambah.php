<?php
session_start();

require_once __DIR__ . '/../includes/koneksi.php';

// Ambil dan bersihkan data dari form
$nama             = trim($_POST['nama'] ?? '');
$noAnggota        = trim($_POST['no_anggota'] ?? '');
$alamat           = trim($_POST['alamat'] ?? '');
$email            = trim($_POST['email'] ?? '');
$noHp             = trim($_POST['no_hp'] ?? '');
$tanggalBergabung = trim($_POST['tanggal_bergabung'] ?? $_POST['tanggal_bergabung'] ?? '');

// Validasi Server-Side
$errors = [];

if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}
if ($noHp !== '' && !preg_match('/^[0-9\+\-\s]+$/', $noHp)) {
    $errors[] = "No. HP hanya boleh berisi angka dan tanda (+/-).";
}

// Jika ada error validasi, kembalikan ke form
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
    $sql = "INSERT INTO anggota (nama, no_anggota, alamat, email, no_hp, tanggal_bergabung) 
            VALUES (:nama, :no_anggota, :alamat, :email, :no_hp, :tanggal_bergabung)";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nama'          => $nama,
        ':no_anggota'    => $noAnggota,
        ':alamat'        => $alamat,
        ':email'         => $email,
        ':no_hp'         => $noHp,
        ':tanggal_bergabung' => $tanggalBergabung !== '' ? $tanggalBergabung : null,
    ]);

    $_SESSION['flash'] = [
        'type'  => 'success',
        'pesan' => 'Anggota berhasil ditambahkan.'
    ];
    header('Location: list.php');
    exit;

} catch (PDOException $e) {
    // Handling error UNIQUE Constraint (SQLState 23505 di PostgreSQL)
    if ($e->getCode() === '23505') {
        $pesanError = "No. Anggota sudah dipakai, gunakan nomor lain.";
    } else {
        $pesanError = "Gagal menyimpan data ke database: " . $e->getMessage();
    }

    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => $pesanError
    ];
    header('Location: tambah.php');
    exit;
}