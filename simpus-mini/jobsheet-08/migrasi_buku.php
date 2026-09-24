<?php
require_once __DIR__ . '/includes/koneksi.php';

$jsonPath = __DIR__ . '/data/buku.json';

if (!file_exists($jsonPath)) {
    die("Gagal: File data/buku.json tidak ditemukan di lokasi: " . $jsonPath);
}

$jsonData = file_get_contents($jsonPath);
$bukuList = json_decode($jsonData, true);

if (empty($bukuList)) {
    die("Gagal: Format JSON salah atau data kosong.");
}

$insertedCount = 0;
$skippedCount = 0;

// Query untuk mengecek apakah buku sudah ada berdasarkan judul
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE LOWER(judul) = LOWER(:judul)");

// Query untuk memasukkan data baru
$sql = "INSERT INTO buku (judul, pengarang, tahun, stok, kategori) 
        VALUES (:judul, :pengarang, :tahun, :stok, :kategori)";
$insertStmt = $pdo->prepare($sql);

foreach ($bukuList as $buku) {
    $judul = trim($buku['judul'] ?? '');

    if ($judul === '') continue;

    // Cek apakah judul buku sudah ada di database
    $checkStmt->execute([':judul' => $judul]);
    $isExist = $checkStmt->fetchColumn();

    if ($isExist > 0) {
        $skippedCount++;
    } else {
        try {
            $insertStmt->execute([
                ':judul'     => $judul,
                ':pengarang' => $buku['pengarang'] ?? 'Anonim',
                ':tahun'     => (int) ($buku['tahun'] ?? date('Y')),
                ':stok'      => (int) ($buku['stok'] ?? 0),
                ':kategori'  => $buku['kategori'] ?? 'Fiksi',
            ]);
            $insertedCount++;
        } catch (PDOException $e) {
            echo "Gagal menambahkan '{$judul}': " . $e->getMessage() . "<br>";
        }
    }
}

echo "<h3>Proses Migrasi Selesai!</h3>";
echo "<p>Berhasil menambahkan: <b>{$insertedCount}</b> buku baru.</p>";
echo "<p>Dilewati (sudah ada): <b>{$skippedCount}</b> buku.</p>";
echo "<br><a href='buku/list.php'>Lihat Daftar Buku</a>";