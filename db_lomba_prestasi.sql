-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 09, 2026 at 12:45 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_lomba_prestasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int NOT NULL,
  `nama_katgeori` varchar(100) DEFAULT NULL,
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `nama_katgeori`, `deskripsi`) VALUES
(1, 'Akademik', 'Prestasi dalam bidang akademik dan pendidikan.'),
(2, 'Olahraga', 'Prestasi dalam berbagai cabang olahraga.'),
(3, 'Seni & Budaya', 'Prestasi dalam bidang seni, budaya, dan kreativitas.'),
(4, 'Teknologi', 'Prestasi dalam bidang teknologi dan digital.'),
(5, 'Kewirausahaan', 'Prestasi dalam bidang bisnis dan kewirausahaan.'),
(6, 'Sosial', 'Prestasi dalam bidang sosial dan kegiatan kemasyarakatan.');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pemuda`
--

CREATE TABLE `tb_pemuda` (
  `id_pemuda` int NOT NULL,
  `nama_pemuda` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `asal_instansi` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `alamat` text,
  `deskripsi` text,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_pemuda`
--

INSERT INTO `tb_pemuda` (`id_pemuda`, `nama_pemuda`, `tanggal_lahir`, `jenis_kelamin`, `asal_instansi`, `kecamatan`, `alamat`, `deskripsi`, `foto`) VALUES
(2, 'Ahmad Rizky', '2007-05-12', 'Laki-laki', 'SMK Negeri 1 Demak', 'Demak', 'Demak, Jawa Tengah', 'Pemuda yang aktif dalam bidang teknologi dan pengembangan aplikasi.', '1788695594_6a9d542a47a26.jpg'),
(3, 'Siti Aulia', '2006-08-20', 'Perempuan', 'SMA Negeri 1 Demak', 'Demak', 'Bintoro, Demak', 'Pemuda berprestasi yang aktif mengikuti berbagai kompetisi akademik.', '1788695586_6a9d5422956a1.jpg'),
(4, 'Fajar Pratama', '2007-02-15', 'Laki-laki', 'SMK Negeri 2 Demak', 'Mranggen', 'Mranggen, Demak', 'Pemuda yang aktif dalam bidang olahraga dan pencak silat.', '1788695579_6a9d541b28680.jpg'),
(5, 'Nabila Putri', '2006-11-03', 'Perempuan', 'SMA Negeri 2 Demak', 'Karanganyar', 'Karanganyar, Demak', 'Pemuda yang aktif dalam bidang seni dan budaya.', '1788695483_6a9d53bb9cc6b.jpg'),
(6, 'Rizky Maulana', '2007-01-25', 'Laki-laki', 'SMK Negeri 1 Demak', 'Wonosalam', 'Wonosalam, Demak', 'Pemuda yang mengembangkan usaha kreatif dan kewirausahaan.', '1788695447_6a9d539734e25.jpg'),
(7, 'Dinda Safitri', '2006-06-18', 'Perempuan', 'SMA Negeri 1 Demak', 'Sayung', 'Sayung, Demak', 'Pemuda yang aktif dalam kegiatan sosial dan kemasyarakatan.', '1788695415_6a9d537707973.jpg'),
(8, 'Bagas Aditya', '2007-09-10', 'Laki-laki', 'SMK Negeri 2 Demak', 'Bonang', 'Bonang, Demak', 'Pemuda yang aktif dalam kompetisi teknologi dan inovasi.', '1788695197_6a9d529d8b235.jpg'),
(9, 'Intan Permata', '2006-04-22', 'Perempuan', 'SMA Negeri 3 Demak', 'Dempet', 'Dempet, Demak', 'Pemuda yang memiliki prestasi dalam bidang akademik dan penelitian.', '1788695190_6a9d529682e3f.jpg'),
(10, 'Agung', '2026-09-02', 'Laki-laki', 'SMK Negeri 1 Sayung', 'Sayung', 'Sayung, Demak', 'MEnang JUara', '1788695181_6a9d528d4db15.jpg'),
(11, 'bibbur', '2026-09-07', 'Laki-laki', 'SMK Negeri 1 Sayung', 'Sayung', 'demak', 'gacor kang', '1788768665_6a9e719900adf.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tb_prestasi`
--

CREATE TABLE `tb_prestasi` (
  `id_prestasi` int NOT NULL,
  `id_pemuda` int DEFAULT NULL,
  `id_kategori` int DEFAULT NULL,
  `nama_prestasi` varchar(100) DEFAULT NULL,
  `tingkat` varchar(50) DEFAULT NULL,
  `penyelenggara` varchar(100) DEFAULT NULL,
  `tahun` year DEFAULT NULL,
  `id_sertifikat` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_prestasi`
--

INSERT INTO `tb_prestasi` (`id_prestasi`, `id_pemuda`, `id_kategori`, `nama_prestasi`, `tingkat`, `penyelenggara`, `tahun`, `id_sertifikat`) VALUES
(45, 10, 4, 'Lomba LCC Tingkat Demak', 'Kabupaten', 'Dinas Pendidikan Jawa Tengah', 2026, 18),
(46, 10, 2, 'Juara 1 Pencak Silat', 'Provinsi', 'Dinpora', 2026, 19),
(47, 6, 1, 'Juara 2 Kompetisi Sains', 'Nasional', 'Kompetisi Teknologi Indonesia', 2026, 20),
(48, 2, 1, 'Juara 1 Pencak Silat', 'Provinsi', 'Dinpora', 2026, 21);

-- --------------------------------------------------------

--
-- Table structure for table `tb_sertifikat`
--

CREATE TABLE `tb_sertifikat` (
  `id_sertifikat` int NOT NULL,
  `nomor_sertifikat` varchar(100) DEFAULT NULL,
  `nama_prestasi` varchar(255) DEFAULT NULL,
  `nama_pemuda` varchar(100) DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `tingkat` varchar(50) DEFAULT NULL,
  `penyelenggara` varchar(100) DEFAULT NULL,
  `tahun` year DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_sertifikat`
--

INSERT INTO `tb_sertifikat` (`id_sertifikat`, `nomor_sertifikat`, `nama_prestasi`, `nama_pemuda`, `tanggal_terbit`, `tingkat`, `penyelenggara`, `tahun`, `bukti`, `created_at`) VALUES
(1, 'SRT-2026-0029', 'Lomba LCC Tingkat Demak', 'Agung', '2026-09-07', 'Kabupaten', 'Dinpora', 2026, NULL, '2026-09-07 15:21:54'),
(2, 'SRT-2026-0030', 'Juara 1 Web Development', 'Ahmad Rizky', '2026-09-07', 'Nasional', 'Kompetisi Teknologi Indonesia', 2026, NULL, '2026-09-07 15:21:54'),
(3, 'SRT-2025-0031', 'Juara 2 Inovasi Teknologi Digital', 'Ahmad Rizky', '2026-09-07', 'Provinsi', 'Dinas Pendidikan Jawa Tengah', 2025, NULL, '2026-09-07 15:21:54'),
(4, 'SRT-2026-0032', 'Juara 1 Olimpiade Matematika', 'Siti Aulia', '2026-09-07', 'Nasional', 'Olimpiade Pelajar Indonesia', 2026, NULL, '2026-09-07 15:21:54'),
(5, 'SRT-2025-0033', 'Juara 2 Kompetisi Sains', 'Siti Aulia', '2026-09-07', 'Provinsi', 'Dinas Pendidikan Jawa Tengah', 2025, NULL, '2026-09-07 15:21:54'),
(6, 'SRT-2026-0034', 'Juara 1 Pencak Silat', 'Fajar Pratama', '2026-09-07', 'Provinsi', 'Pekan Olahraga Pelajar Jawa Tengah', 2026, NULL, '2026-09-07 15:21:54'),
(7, 'SRT-2025-0035', 'Juara 3 Kejuaraan Pencak Silat', 'Fajar Pratama', '2026-09-07', 'Nasional', 'Federasi Pencak Silat Indonesia', 2025, NULL, '2026-09-07 15:21:54'),
(8, 'SRT-2026-0036', 'Juara 1 Festival Tari Tradisional', 'Nabila Putri', '2026-09-07', 'Kabupaten', 'Pemerintah Kabupaten Demak', 2026, NULL, '2026-09-07 15:21:54'),
(9, 'SRT-2025-0037', 'Juara 2 Lomba Seni Tari', 'Nabila Putri', '2026-09-07', 'Provinsi', 'Festival Seni Jawa Tengah', 2025, NULL, '2026-09-07 15:21:54'),
(10, 'SRT-2026-0038', 'Juara 1 Wirausaha Muda', 'Rizky Maulana', '2026-09-07', 'Provinsi', 'Kompetisi Wirausaha Muda Jawa Tengah', 2026, NULL, '2026-09-07 15:21:54'),
(11, 'SRT-2026-0042', 'juara 1 hacker', 'bibbur', '2026-09-07', 'dunia', 'hackaton', 2026, NULL, '2026-09-07 15:21:54'),
(18, 'SRT-2026-0045', 'Lomba LCC Tingkat Demak', 'Agung', '2026-09-07', 'Kabupaten', 'Dinas Pendidikan Jawa Tengah', 2026, 'sertifikat-SRT-2026-0045.pdf', '2026-09-07 16:04:42'),
(19, 'SRT-2026-0046', 'Juara 1 Pencak Silat', 'Agung', '2026-09-07', 'Provinsi', 'Dinpora', 2026, 'sertifikat-SRT-2026-0046.pdf', '2026-09-07 16:13:13'),
(20, 'SRT-2026-0047', 'Juara 2 Kompetisi Sains', 'Rizky Maulana', '2026-09-07', 'Nasional', 'Kompetisi Teknologi Indonesia', 2026, '', '2026-09-07 16:19:05'),
(21, 'SRT-2026-0048', 'Juara 1 Pencak Silat', 'Ahmad Rizky', '2026-09-07', 'Provinsi', 'Dinpora', 2026, '', '2026-09-07 16:21:12');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama`, `password`, `username`) VALUES
(1, 'Administrator', '0192023a7bbd73250516f069df18b500', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tb_pemuda`
--
ALTER TABLE `tb_pemuda`
  ADD PRIMARY KEY (`id_pemuda`);

--
-- Indexes for table `tb_prestasi`
--
ALTER TABLE `tb_prestasi`
  ADD PRIMARY KEY (`id_prestasi`),
  ADD KEY `id_pemuda` (`id_pemuda`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_sertifikat` (`id_sertifikat`);

--
-- Indexes for table `tb_sertifikat`
--
ALTER TABLE `tb_sertifikat`
  ADD PRIMARY KEY (`id_sertifikat`),
  ADD UNIQUE KEY `nomor_sertifikat` (`nomor_sertifikat`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_pemuda`
--
ALTER TABLE `tb_pemuda`
  MODIFY `id_pemuda` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_prestasi`
--
ALTER TABLE `tb_prestasi`
  MODIFY `id_prestasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tb_sertifikat`
--
ALTER TABLE `tb_sertifikat`
  MODIFY `id_sertifikat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_prestasi`
--
ALTER TABLE `tb_prestasi`
  ADD CONSTRAINT `fk_prestasi_sertifikat` FOREIGN KEY (`id_sertifikat`) REFERENCES `tb_sertifikat` (`id_sertifikat`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_prestasi_ibfk_1` FOREIGN KEY (`id_pemuda`) REFERENCES `tb_pemuda` (`id_pemuda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_prestasi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
