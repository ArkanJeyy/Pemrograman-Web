// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn") || document.querySelector(".nav-toggle-label");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

function updateCounter() {
    const table = document.querySelector("table");
    const counterElement = document.getElementById("counter-buku");
    if (!table || !counterElement) return;

    const allRows = table.querySelectorAll("tbody tr");
    const totalBuku = allRows.length;

    let visibleCount = 0;
    allRows.forEach(function (row) {
        if (row.style.display !== "none") {
            visibleCount++;
        }
    });

    counterElement.textContent = `Menampilkan ${visibleCount} dari ${totalBuku} buku`;
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus, .btn-delete");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && row) {
            row.remove();
            updateCounter();
        }
    });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector("table"); 
    
    if (!input || !table) {
        console.log("Input atau tabel tidak ditemukan!");
        return;
    }

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase().trim();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const kolomJudul = row.querySelector("td");
            if (kolomJudul) {
                const teksJudul = kolomJudul.textContent.toLowerCase().trim();
                if (teksJudul.includes(keyword)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
        updateCounter();
    });
}
// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        const fieldsWajib = [
            { name: "judul", label: "Judul" },
            { name: "pengarang", label: "Pengarang" },
            { name: "tahun", label: "Tahun Terbit" },
            { name: "isbn", label: "ISBN" },
            { name: "stok", label: "Stok" }
        ];

        fieldsWajib.forEach(function (field) {
            const input = form.querySelector(`[name='${field.name}']`);
            if (input) {
                if (input.value.trim() === "") {
                    tampilkanError(input, `${field.label} wajib diisi.`);
                    valid = false;
                } else {
                    hapusError(input);
                }
            }
        });

        const tahun = form.querySelector("[name='tahun']");
        if (tahun && tahun.value.trim() !== "") {
            const nilai = parseInt(tahun.value, 10);
            if (isNaN(nilai) || nilai < 1900 || nilai > 2026) {
                tampilkanError(tahun, "Tahun harus di antara 1900-2026.");
                valid = false;
            }
        }

        const isbn = form.querySelector("[name='isbn']");
        if (isbn && isbn.value.trim() !== "") {
            const isbnRegex = /^[0-9\-]+$/;
            if (!isbnRegex.test(isbn.value.trim())) {
                tampilkanError(isbn, "ISBN hanya boleh berisi angka dan tanda hubung (-).");
                valid = false;
            }
        }

       const stok = form.querySelector("[name='stok']");
        if (stok && stok.value.trim() !== "") {
            const nilai = parseInt(stok.value, 10);
            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(stok, "Stok tidak boleh negatif.");
                valid = false;
            }
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
}

async function muatDataTabel(urlJson, keys) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    if (loading) loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch(urlJson);
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const dataList = await res.json();

        dataList.forEach(function (item) {
            const tr = document.createElement("tr");
            let rowHtml = "";

            keys.forEach(function (key) {
                rowHtml += "<td>" + (item[key] !== undefined ? item[key] : "-") + "</td>";
            });

            rowHtml += "<td>" +
                "<button type=\"button\" class=\"btn-edit\">Edit</button> " +
                "<button type=\"button\" class=\"btn-detail\">Detail</button> " +
                "<button type=\"button\" class=\"btn-delete\">Hapus</button>" +
                "</td>";

            tr.innerHTML = rowHtml;
            tbody.appendChild(tr);
        });

        updateCounter();

    } catch (err) {
        const totalKolom = keys.length + 1;
        tbody.innerHTML = `<tr><td colspan="${totalKolom}">Gagal memuat data: ${err.message}</td></tr>`;
    } finally {
        if (loading) loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
    updateCounter();
});
