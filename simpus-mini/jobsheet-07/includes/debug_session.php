<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Debug Session";

include __DIR__ . '/header.php';
?>

<section>
    <h2>Debug Session Data</h2>
    <p>Halaman ini menampilkan data mentah yang tersimpan di dalam <code>$_SESSION</code> saat ini:</p>
    
    <pre style="background: #222; color: #00ff00; padding: 1rem; border-radius: 6px; overflow-x: auto;"><?php print_r($_SESSION); ?></pre>
</section>

<?php include __DIR__ . '/footer.php'; ?>