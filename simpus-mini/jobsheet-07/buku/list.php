<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarBuku = $_SESSION['buku'] ?? [];
?>

<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <div class="search-box">
        <label for="search-input">Cari Judul Buku</label>
        <input 
            type="text" 
            id="search-input" 
            placeholder="Ketik judul buku..." 
        />
        <button 
            type="button" 
            id="btn-reload" 
            onclick="window.location.reload();" 
            style="padding: 0.5rem 1rem; margin-left: 0.5rem; cursor: pointer;">
            Muat Ulang
        </button>
    </div>

    <p id="loading-indicator" style="display: none">Memuat data...</p>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">
                            Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarBuku as $index => $buku): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($buku['judul'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['pengarang'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['tahun'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['stok'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($buku['kategori'] ?? '-'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo urlencode($index); ?>">
                                    <button type="button" class="btn-edit">Edit</button>
                                </a>
                                <a href="proses_hapus.php?id=<?php echo urlencode($index); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
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