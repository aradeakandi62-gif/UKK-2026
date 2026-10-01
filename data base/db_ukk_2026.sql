-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Sep 2026 pada 07.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ukk_2026`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_guru`
--

CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas`
--

CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `tahun_pelajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran`
--

CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `kode` varchar(30) DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran_kategori`
--

CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran_siswa`
--

CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL,
  `tahun_pelajaran_id` int(11) DEFAULT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `pelanggaran_id` int(11) DEFAULT NULL,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(30) DEFAULT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_tahun_ajaran`
--

CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_users`
--

CREATE TABLE `t_users` (
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_users`
--

INSERT INTO `t_users` (`name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`, `id`) VALUES
('Admin Utama', 'admin@sekolah.sch.id', '2026-09-23 03:55:21', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', '', 'admin', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 1),
('Budi Santoso', 'budi.santoso@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 2),
('Siti Aminah', 'siti.aminah@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 3),
('Ahmad Zainal', 'ahmad.zainal@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 4),
('Dewi Lestari', 'dewi.lestari@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 5),
('Eko Prasetyo', 'eko.prasetyo@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 6),
('Fitriani', 'fitriani@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 7),
('Gatot Sutanto', 'gatot.sutanto@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 8),
('Mardi Santoso', 'mardi.santoso@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 9),
('Indra Kurniawan', 'indra.kurniawan@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 10),
('Jaka Permana', 'jaka.permana@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 11),
('Kartika Sari', 'kartika.sari@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 12),
('Lukman Hakim', 'lukman.hakim@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 13),
('Maya Oktaviani', 'maya.oktaviani@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 14),
('Nugroho Adi', 'nugroho.adi@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 15),
('Oktavia Putri', 'oktavia.putri@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 16),
('Pratama Wirya', 'pratama.wirya@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 17),
('Putri Lestari', 'putri.lestari@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 18),
('Rina Marlina', 'rina.marlina@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 19),
('Rizky Darmawan', 'rizky.darmawan@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 20),
('Sari Indah', 'sari.indah@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 21),
('Surya Darma', 'surya.darma@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 22),
('Teguh Prakoso', 'teguh.prakoso@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 23),
('Umi Kalsum', 'umi.kalsum@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 24),
('Vina Sari', 'vina.sari@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 25),
('Wahyu Hidayat', 'wahyu.hidayat@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 26),
('Yanti Rahmawati', 'yanti.rahmawati@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 27),
('Yusuf Mahendra', 'yusuf.mahendra@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 28),
('Zahra Nur', 'zahra.nur@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 29),
('Agus Salim', 'agus.salim@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 30),
('Bambang Sutrisno', 'bambang.sutrisno@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 31),
('Citra Lestari', 'citra.lestari@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 32),
('Dedi Firmansyah', 'dedi.firmansyah@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 33),
('Eli Suryani', 'eli.suryani@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 34),
('Fajar Nugroho', 'fajar.nugroho@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 35),
('Galih Permana', 'galih.permana@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 36),
('Hani Safitri', 'hani.safitri@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 37),
('Hendra Kusuma', 'hendra.kusuma@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 38),
('Ika Wulandari', 'ika.wulandari@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 39),
('Joko Susilo', 'joko.susilo@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 40),
('Kusnadi', 'kusnadi@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 41),
('Lina Marlina', 'lina.marlina@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 42),
('Maman Suparman', 'maman.suparman@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 43),
('Nani Herawati', 'nani.herawati@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 44),
('Nanang Setiawan', 'nanang.setiawan@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 45),
('Nina Kartika', 'nina.kartika@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 46),
('Parwati', 'parwati@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 47),
('Rahmat Hidayat', 'rahmat.hidayat@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 48),
('Sri Wahyuni', 'sri.wahyuni@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 49),
('Toto Suryanto', 'toto.suryanto@sekolah.sch.id', '2026-09-23 03:55:21', 'ae81343369944399b70de862dbe75536faa8e44c50ad0a312e380303173f4756', '', 'guru', '2026-09-23 03:55:21', '2026-09-23 03:55:21', 50);

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_wali_kelas`
--

CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL,
  `tahun_pelajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `tahun_pelajaran_id` (`tahun_pelajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`);

--
-- Indeks untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_pelajaran_id` (`tahun_pelajaran_id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `pelanggaran_id` (`pelanggaran_id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_pelajaran_id` (`tahun_pelajaran_id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  ADD CONSTRAINT `t_guru_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_wali_kelas` (`guru_id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD CONSTRAINT `t_kelas_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_wali_kelas` (`kelas_id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD CONSTRAINT `t_pelanggaran_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_pelanggaran_siswa` (`pelanggaran_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_ibfk_2` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD CONSTRAINT `t_pelanggaran_kategori_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_pelanggaran_siswa` (`pelanggaran_kategori_id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_2` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_4` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD CONSTRAINT `t_siswa_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_kelas_siswa` (`siswa_id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD CONSTRAINT `t_tahun_ajaran_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_kelas_siswa` (`tahun_pelajaran_id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD CONSTRAINT `t_wali_kelas_ibfk_1` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
