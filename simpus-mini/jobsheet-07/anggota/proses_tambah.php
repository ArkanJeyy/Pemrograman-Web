<?php
session_start();

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$tglBergabung = trim($_POST['tgl_bergabung'] ?? '');
$email = trim($_POST['email'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

$_SESSION['anggota'][] = [
    'nama' => $nama,
    'no_anggota' => $noAnggota,
    'alamat' => $alamat,
    'tgl_bergabung' => $tglBergabung,
    'email' => $email,
    'no_hp' => $noHp,
];

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;