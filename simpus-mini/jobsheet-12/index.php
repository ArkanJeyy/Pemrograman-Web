<?php
require_once __DIR__ . '/includes/koneksi.php';

$page_title = "Beranda";
include __DIR__ . '/includes/header.php';

// Ambil hitungan data sungguhan dari database PostgreSQL
$totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn() ?: 0;
$totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn() ?: 0;
$sedangDipinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn() ?: 0;
$bukuTerlambat = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam' AND tanggal_kembali < CURRENT_DATE")->fetchColumn() ?: 0;
?>

<section>
    <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
    <p>Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.</p>
</section>

<section>
    <h2>Ringkasan</h2>
    <article>
        <h3>Total Buku</h3>
        <p><?php echo $totalBuku; ?></p>
    </article>
    <article>
        <h3>Total Anggota</h3>
        <p><?php echo $totalAnggota; ?></p>
    </article>
    <article>
        <h3>Sedang Dipinjam</h3>
        <p><?php echo $sedangDipinjam; ?></p>
    </article>
    <article>
        <h3>Buku Terlambat</h3>
        <p><?php echo $bukuTerlambat; ?></p>
    </article>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>