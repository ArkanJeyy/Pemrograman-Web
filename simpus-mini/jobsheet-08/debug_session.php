<?php
session_start();
$page_title = "Debug Session";
include __DIR__ . '/includes/header.php';
?>

<section>
    <h2>Debug Session Data</h2>
    <p>Halaman ini menampilkan data mentah yang tersimpan di dalam <code>SESSION</code> saat ini:</p>
    
    <pre style="background: #1e1e1e; color: #00ff00; padding: 1rem; border-radius: 6px; overflow-x: auto; font-family: monospace;"><?php print_r($_SESSION); ?></pre>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>