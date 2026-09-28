<?php
require_once __DIR__ . '/../includes/koneksi.php';

$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Fitur 3: Query Pencarian di Server menggunakan ILIKE
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul ILIKE :keyword ORDER BY id DESC");
    $stmt->execute([':keyword' => '%' . $keyword . '%']);
    $daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftarBuku = $pdo->query("SELECT * FROM buku ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <!-- Form Pencarian Sisi Server -->
    <form method="get" action="list.php" class="search-box">
        <label for="search-input">Cari Judul Buku</label>
        <input 
            type="text" 
            id="search-input" 
            name="q" 
            value="<?php echo htmlspecialchars($keyword); ?>" 
            placeholder="Ketik judul buku..." 
        />
        <button type="submit" style="padding: 0.5rem 1rem; cursor: pointer;">Cari</button>
        <a href="list.php">
            <button type="button" style="padding: 0.5rem 1rem; margin-left: 0.25rem; cursor: pointer;">Muat Ulang</button>
        </a>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Tanggal Ditambahkan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Data buku tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($buku['judul'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['pengarang'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['tahun'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['stok'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['kategori'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['tanggal_ditambahkan'] ?? '-'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo urlencode($buku['id']); ?>">
                                    <button type="button" class="btn-edit">Edit</button>
                                </a>
                                <a href="proses_hapus.php?id=<?php echo urlencode($buku['id']); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                    <button type="button" class="btn-delete">Hapus</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>