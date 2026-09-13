// Mengambil & menampilkan Daftar Anggota secara asinkron dari data/anggota.json
async function muatDaftarAnggota() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    if (loading) loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/anggota.json");
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const daftarAnggota = await res.json();

        daftarAnggota.forEach(function (anggota) {
            const tr = document.createElement("tr");
            
            // PERBAIKAN 1: Menyesuaikan seluruh 7 kolom data agar sejajar dengan <thead>
            tr.innerHTML =
                "<td>" + (anggota.no_anggota || "-") + "</td>" +
                "<td>" + (anggota.nama || "-") + "</td>" +
                "<td>" + (anggota.alamat || "-") + "</td>" +
                "<td>" + (anggota.email || "-") + "</td>" +
                "<td>" + (anggota.no_hp || "-") + "</td>" +
                "<td>" + (anggota.tgl_bergabung || "-") + "</td>" +
                "<td>" +
                "<button type=\"button\" class=\"btn-edit\">Edit</button> " +
                "<button type=\"button\"class=\"btn-detail\">Detail</button> " +
                "<button type=\"button\" class=\"btn-delete\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });

        // PERBAIKAN 2: Panggil event konfirmasi hapus & counter setelah data JSON selesai dimuat
        if (typeof initHapusConfirm === "function") initHapusConfirm();
        if (typeof updateCounter === "function") updateCounter();

    } catch (err) {
        // Colspan disesuaikan menjadi 7 karena total ada 7 kolom
        tbody.innerHTML =
            "<tr><td colspan=\"7\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        if (loading) loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", muatDaftarAnggota);