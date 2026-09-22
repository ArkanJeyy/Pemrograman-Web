<?php
session_start();

$nama         = trim($_POST['nama'] ?? '');
$noAnggota    = trim($_POST['no_anggota'] ?? '');
$alamat       = trim($_POST['alamat'] ?? '');
$tglBergabung = trim($_POST['tgl_bergabung'] ?? '');
$email        = trim($_POST['email'] ?? '');
$noHp         = trim($_POST['no_hp'] ?? '');

$errors = [];

// Validasi Field Wajib
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

// Validasi Email jika diisi
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}

// Validasi No. HP jika diisi (hanya angka, spasi, dan tanda +/-)
if ($noHp !== '' && !preg_match('/^[0-9\+\-\s]+$/', $noHp)) {
    $errors[] = "No. HP hanya boleh berisi angka dan tanda (+/-).";
}

// Jika ada error, kirim pesan flash dan kembalikan ke form
if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

// Simpan data ke session
$_SESSION['anggota'][] = [
    'nama'          => $nama,
    'no_anggota'    => $noAnggota,
    'alamat'        => $alamat,
    'tgl_bergabung' => $tglBergabung,
    'email'         => $email,
    'no_hp'          => $noHp,
];

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;