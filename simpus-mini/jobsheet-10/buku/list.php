<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $searchKw = '%' . $keyword . '%';

    // 1. Hitung total baris yang cocok dengan Judul ATAU Pengarang
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE judul ILIKE :kw1 OR pengarang ILIKE :kw2");
    $hitung->execute([
        'kw1' => $searchKw,
        'kw2' => $searchKw
    ]);
    $totalRows = $hitung->fetchColumn();

    // 2. Ambil data dengan memasukkan LIMIT & OFFSET langsung sebagai nilai integer dalam SQL
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul ILIKE :kw1 OR pengarang ILIKE :kw2 ORDER BY id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute([
        'kw1' => $searchKw,
        'kw2' => $searchKw
    ]);
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
    $stmt = $pdo->query("SELECT * FROM buku ORDER BY id DESC LIMIT $perPage OFFSET $offset");
}

$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>

<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </p>
    <?php endif; ?>

    <div class="search-box">
        <form method="get" action="list.php">
            <span>
                <label for="search-input">Cari Judul / Pengarang Buku</label><br>
                <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Ketik judul / pengarang...">
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
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>ISBN</th>
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
                    <td colspan="8">Tidak ada data buku yang cocok.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($daftarBuku as $buku): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($buku['judul'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($buku['pengarang'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($buku['isbn'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($buku['tahun'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($buku['stok'] ?? 0); ?></td>
                        <td><?php echo htmlspecialchars($buku['kategori'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($buku['created_at'] ?? $buku['tanggal_ditambahkan'] ?? '-'); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo urlencode($buku['id']); ?>" class="btn-edit">Edit</a>
                            <form class="form-hapus" method="post" action="hapus.php" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($buku['id']); ?>">
                                <button type="submit" class="btn-hapus">Hapus</button>
                            </form>
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