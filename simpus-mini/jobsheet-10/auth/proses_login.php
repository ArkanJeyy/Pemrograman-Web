<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$username    = trim($_POST['username'] ?? '');
$password    = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

    if ($remember_me) {
        $token  = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60);

        setcookie('remember_token', $token, [
            'expires'  => $expiry,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $updateStmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
        $updateStmt->execute(['token' => $token, 'id' => $user['id']]);
    }

    header('Location: ../index.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
header('Location: login.php');
exit;