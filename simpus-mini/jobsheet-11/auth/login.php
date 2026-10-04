<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung alihkan ke index.php
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Ambil pesan flash dari session sebelum memanggil header
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$page_title = "Login";
include __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Login Petugas</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>">
            <?php echo e($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <form method="post" action="proses_login.php">
        <p class="form-group">
            <label for="username">Username</label><br>
            <input type="text" id="username" name="username" required>
        </p>
        <p class="form-group">
            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required>
        </p>
        
        <p class="remember-me" style="margin: 10px 0;">
            <label for="remember_me" style="display: inline-flex; align-items: center; gap: 8px; width: auto; font-weight: normal; cursor: pointer;">
                <input type="checkbox" id="remember_me" name="remember_me" value="1" style="width: auto; margin: 0; cursor: pointer;">
                Ingat Saya
            </label>
        </p>

        <p>
            <button type="submit">Masuk</button>
        </p>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>