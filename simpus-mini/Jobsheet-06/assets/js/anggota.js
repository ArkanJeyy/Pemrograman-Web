function muatDaftarAnggota() {
    muatDataTabel("../data/anggota.json", [
        "no_anggota",
        "nama",
        "alamat",
        "email",
        "no_hp",
        "tgl_bergabung"
    ]);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarAnggota();
    
    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", function () {
            muatDaftarAnggota();
        });
    }
});