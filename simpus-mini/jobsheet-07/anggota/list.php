<?php
$page_title = "Daftar Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarAnggota = $_SESSION['anggota'] ?? [];
?>

<section>
    <h2>Daftar Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <div class="search-box">
        <label for="search-input">Cari Nama Anggota</label>
        <input 
            type="text" 
            id="search-input" 
            placeholder="Ketik nama anggota..." 
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
                    <th>No. Anggota</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Tanggal Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarAnggota)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Belum ada data anggota. Silakan tambah lewat menu "Tambah Anggota".
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarAnggota as $index => $anggota): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($anggota['no_anggota'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['nama'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['alamat'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['no_hp'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['tgl_bergabung'] ?? '-'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo urlencode($anggota['no_anggota'] ?? $index); ?>">
                                    <button type="button" class="btn-edit">Edit</button>
                                </a>
                                <a href="proses_hapus.php?id=<?php echo urlencode($anggota['no_anggota'] ?? $index); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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