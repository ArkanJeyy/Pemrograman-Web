<?php $base = $base ?? ''; ?>
</main>

    <footer>
        <p>&copy; 2026 SIMPUS-Mini &mdash; Jobsheet 7</p>
    </footer>

    <!-- Script Utama untuk Navigasi Toggle / UI Interaktif -->
    <script src="<?php echo $base; ?>assets/js/app.js"></script>

    <!-- Script Tambahan Dinamis (Misal: anggota.js atau buku.js) -->
    <?php if (!empty($extra_scripts)): foreach ($extra_scripts as $src): ?>
        <script src="<?php echo htmlspecialchars($src); ?>"></script>
    <?php endforeach; endif; ?>
</body>
</html>