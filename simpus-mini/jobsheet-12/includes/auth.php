<?php
// Guard clause: pastikan session selalu berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/koneksi.php';

// 1. AUTO-LOGIN DARI COOKIE (Remember Me)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];
    }
}

// 2. PROTEKSI HALAMAN PRIVAT
$current_script = basename($_SERVER['SCRIPT_NAME']);
$public_pages   = ['login.php', 'register.php', 'proses_login.php', 'proses_register.php'];

// Jika halaman yang diakses butuh login dan user belum login, lempar ke login.php
if (!in_array($current_script, $public_pages)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }
}

/**
 * Memeriksa hak akses user berdasarkan role (RBAC).
 * Contoh penggunaan: check_role(['admin']);
 * 
 * @param array $allowed_roles
 */
function check_role(array $allowed_roles) {
    $user_role = $_SESSION['role'] ?? '';

    if (!in_array($user_role, $allowed_roles)) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman tersebut.'
        ];
        header('Location: ../index.php');
        exit;
    }
}