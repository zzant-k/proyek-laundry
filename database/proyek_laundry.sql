-- ═══════════════════════════════════════════════════════
-- PROYEK LAUNDRY — Database Setup (Latest Schema)
-- ═══════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `proyek_laundry`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `proyek_laundry`;

-- ── 1. Tabel Admin (legacy) ──
CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 1b. Tabel User (Login + Registrasi dengan Role) ──
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` INT NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(50) NOT NULL DEFAULT '',
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 2. Tabel Transaksi ──
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id_laundry` INT NOT NULL AUTO_INCREMENT,
  `kode_order` VARCHAR(20) NOT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `pesan` TEXT,
  `jenis_pencucian` VARCHAR(50) NOT NULL,
  `jenis_layanan` VARCHAR(50) NOT NULL,
  `tanggal_penjemputan` DATE NOT NULL,
  `jam_penjemputan` TIME NOT NULL,
  `status` ENUM('Baru', 'Dicuci', 'Dijemput', 'Selesai') DEFAULT 'Baru',
  PRIMARY KEY (`id_laundry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 3. Tabel Pengiriman (Pesan Pelanggan) ──
CREATE TABLE IF NOT EXISTS `pengiriman` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(150) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `pesan` TEXT,
  `jenis_pencucian` VARCHAR(50) NOT NULL,
  `jenis_layanan` VARCHAR(50) NOT NULL,
  `tanggal_pengiriman` DATE NOT NULL,
  `jam_pengiriman` TIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 4. Tabel Profile ──
CREATE TABLE IF NOT EXISTS `profile` (
  `id_profile` INT NOT NULL AUTO_INCREMENT,
  `id_user`    INT NOT NULL,
  `nama`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `no_hp`      VARCHAR(50) NOT NULL DEFAULT '',
  `foto_profile` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_profile`),
  KEY `fk_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
