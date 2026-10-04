<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$username    = trim($_POST['username'] ?? '');
$password    = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']);

// Konfigurasi Batas Login
$max_attempts = 3;        
$lockout_time = 1 * 60;   

// Inisialisasi session tracking
$_SESSION['login_attempts']    = $_SESSION['login_attempts'] ?? 0;
$_SESSION['last_attempt_time'] = $_SESSION['last_attempt_time'] ?? 0;

// 1. CEK DULU APAKAH USER SEDANG DIBLOKIR / LOCKOUT
if ($_SESSION['login_attempts'] >= $max_attempts) {
    $selisih = time() - $_SESSION['last_attempt_time'];

    if ($selisih < $lockout_time) {
        $sisa_menit = ceil(($lockout_time - $selisih) / 60);
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => "Akun dikunci sementara karena 3x salah! Silakan coba lagi dalam {$sisa_menit} menit."
        ];
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// 2. QUERY KE DATABASE
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. JIKA LOGIN BERHASIL
if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);

    unset($_SESSION['login_attempts']);
    unset($_SESSION['last_attempt_time']);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

    // Fitur Ingat Saya (Berlaku 3 Hari)
    if ($remember_me) {
        $token  = bin2hex(random_bytes(32));
        $expiry = time() + (3 * 24 * 60 * 60); 

        setcookie('remember_token', $token, [
            'expires'  => $expiry,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $up = $pdo->prepare("UPDATE users SET remember_token = :t WHERE id = :id");
        $up->execute(['t' => $token, 'id' => $user['id']]);
    }

    header('Location: ../index.php');
    exit;
}

// 4. JIKA LOGIN GAGAL (PASSWORD SALAH)
$_SESSION['login_attempts']++;
$_SESSION['last_attempt_time'] = time();

$sisa = $max_attempts - $_SESSION['login_attempts'];

if ($sisa > 0) {
    $pesan_error = "Username atau password salah. Sisa percobaan: {$sisa} kali lagi.";
} else {
    $pesan_error = "Terlalu banyak percobaan gagal! Akun dikunci sementara selama 10 menit.";
}

$_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan_error];
header('Location: login.php');
exit;