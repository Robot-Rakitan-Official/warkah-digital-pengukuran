-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Bulan Mei 2026 pada 02.26
-- Versi server: 8.0.30
-- Versi PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_warkah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gambar_ukur`
--

CREATE TABLE `gambar_ukur` (
  `id` int NOT NULL,
  `no_gambar_ukur` varchar(50) NOT NULL,
  `tahun` int NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `desa_kelurahan` varchar(100) NOT NULL,
  `lemari_rak` varchar(50) NOT NULL,
  `file_pdf` varchar(255) NOT NULL,
  `keterangan` text,
  `status` enum('Tersedia','Tidak Tersedia','Dipinjam') DEFAULT 'Tersedia',
  `created_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `no_gambar_ukur` varchar(50) NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `nip_nik` varchar(50) NOT NULL,
  `status_peminjam` varchar(50) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `keperluan` text NOT NULL,
  `status` enum('Dipinjam','Dikembalikan') DEFAULT 'Dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','karyawan') DEFAULT 'karyawan',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `email`, `password`, `role`, `created_at`) VALUES
(1, '', 'Admin BPN', 'admin@bpn.go.id', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '2026-05-16 02:29:23'),
(2, '', 'Staff Entry', 'staff@bpn.go.id', 'e10adc3949ba59abbe56e057f20f883e', 'karyawan', '2026-05-16 02:29:23');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gambar_ukur`
--
ALTER TABLE `gambar_ukur`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `gambar_ukur`
--
ALTER TABLE `gambar_ukur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
