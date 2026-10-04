<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Daftar Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Jumlah baris per halaman diset ke 10 sesuai instruksi tugas 2
$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $searchKw = '%' . $keyword . '%';

    $hitung = $pdo->prepare("SELECT COUNT(*) FROM anggota WHERE nama ILIKE :kw1 OR no_anggota::text ILIKE :kw2");
    $hitung->execute([
        'kw1' => $searchKw,
        'kw2' => $searchKw
    ]);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE nama ILIKE :kw1 OR no_anggota::text ILIKE :kw2 ORDER BY id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute([
        'kw1' => $searchKw,
        'kw2' => $searchKw
    ]);
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
    $stmt = $pdo->query("SELECT * FROM anggota ORDER BY id DESC LIMIT $perPage OFFSET $offset");
}

$daftarAnggota = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>

<section>
    <h2>Daftar Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <div class="search-box">
        <form method="get" action="list.php">
            <span>
                <label for="search-input">Cari Nama / No. Anggota</label><br>
                <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Ketik nama atau no. anggota...">
            </span>
            <button type="submit">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="list.php"><button type="button">Muat Ulang</button></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. Anggota</th>
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
                    <td colspan="7">Tidak ada data anggota yang cocok.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($daftarAnggota as $anggota): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($anggota['nama'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($anggota['no_anggota'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($anggota['alamat'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($anggota['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($anggota['no_hp'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($anggota['tanggal_bergabung'] ?? '-'); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo urlencode($anggota['id']); ?>" class="btn-edit">Edit</a>
                            
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <form class="form-hapus" method="post" action="hapus.php" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($anggota['id']); ?>">
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
           class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>