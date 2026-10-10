-- Jobsheet 8: skema awal database simpus_mini (PostgreSQL)
-- Jalankan setelah membuat database, misal:
--   createdb simpus_mini
--   psql -d simpus_mini -f sql/01_buku_anggota.sql

CREATE TABLE IF NOT EXISTS buku (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(255) NOT NULL,
    tahun INTEGER NOT NULL,
    isbn VARCHAR(50),
    stok INTEGER NOT NULL DEFAULT 0,
    kategori VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS anggota (
    id SERIAL PRIMARY KEY,
    no_anggota VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    alamat VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    no_hp VARCHAR(30),
    tanggal_bergabung DATE DEFAULT CURRENT_DATE
);
