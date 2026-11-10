-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table grafika-printing.alats
CREATE TABLE IF NOT EXISTS `alats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `nama_alat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merek` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spesifikasi_alat` text COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','maintenance','rusak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `kapasitas_cetak_per_jam` int NOT NULL,
  `tersedia` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alats_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `alats_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.alats: ~2 rows (approximately)
REPLACE INTO `alats` (`id`, `vendor_id`, `nama_alat`, `merek`, `model`, `spesifikasi_alat`, `status`, `tanggal_pembelian`, `kapasitas_cetak_per_jam`, `tersedia`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Mesin Digital Printing', 'Mesin Digital Printing', 'Mesin Digital Printing', 'Mesin Digital Printing', 'aktif', '2025-03-27', 1, 1, '2025-03-27 07:16:37', '2025-03-27 07:16:37'),
	(2, 1, 'Pengeringan', 'Pengeringan', 'Pengeringan', 'Pengeringan', 'aktif', '2025-03-27', 1, 1, '2025-03-27 07:16:57', '2025-03-27 07:16:57'),
	(3, 1, 'Finishing', 'Finishing', 'Finishing', 'Finishing', 'aktif', '2025-03-27', 1, 1, '2025-03-27 07:17:17', '2025-03-27 07:17:17');

-- Dumping structure for table grafika-printing.bahans
CREATE TABLE IF NOT EXISTS `bahans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `nama_bahan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hpp` decimal(10,2) NOT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bahans_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `bahans_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.bahans: ~2 rows (approximately)
REPLACE INTO `bahans` (`id`, `vendor_id`, `nama_bahan`, `hpp`, `satuan`, `stok`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Flexi Korea 440gsm', 45000.00, 'Meter', '-234', '2025-03-27 07:19:12', '2025-04-07 21:20:02'),
	(2, 1, 'Mata Ayam', 20000.00, 'pcs', '910', '2025-03-27 07:21:01', '2025-04-07 21:20:02');

-- Dumping structure for table grafika-printing.bahan_spesifikasi_produk
CREATE TABLE IF NOT EXISTS `bahan_spesifikasi_produk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bahan_id` bigint unsigned NOT NULL,
  `spesifikasi_produk_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_spek_unique` (`bahan_id`,`spesifikasi_produk_id`),
  KEY `bahan_spesifikasi_produk_spesifikasi_produk_id_foreign` (`spesifikasi_produk_id`),
  CONSTRAINT `bahan_spesifikasi_produk_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_spesifikasi_produk_spesifikasi_produk_id_foreign` FOREIGN KEY (`spesifikasi_produk_id`) REFERENCES `spesifikasi_produks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.bahan_spesifikasi_produk: ~5 rows (approximately)
REPLACE INTO `bahan_spesifikasi_produk` (`id`, `bahan_id`, `spesifikasi_produk_id`, `created_at`, `updated_at`) VALUES
	(3, 1, 1, '2025-03-27 07:29:18', '2025-03-27 07:29:18'),
	(4, 1, 2, '2025-03-27 07:29:18', '2025-03-27 07:29:18'),
	(5, 2, 3, '2025-03-27 07:29:18', '2025-03-27 07:29:18'),
	(6, 1, 5, '2025-03-27 08:18:19', '2025-03-27 08:18:19'),
	(7, 2, 6, '2025-03-27 08:18:19', '2025-03-27 08:18:19');

-- Dumping structure for table grafika-printing.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.cache: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.cache_locks: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.estimasi_produks
CREATE TABLE IF NOT EXISTS `estimasi_produks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `alat_id` bigint unsigned NOT NULL,
  `waktu_persiapan` int NOT NULL,
  `waktu_produksi_per_unit` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimasi_produks_vendor_id_foreign` (`vendor_id`),
  KEY `estimasi_produks_produk_id_foreign` (`produk_id`),
  KEY `estimasi_produks_alat_id_foreign` (`alat_id`),
  CONSTRAINT `estimasi_produks_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `estimasi_produks_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `estimasi_produks_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.estimasi_produks: ~6 rows (approximately)
REPLACE INTO `estimasi_produks` (`id`, `vendor_id`, `produk_id`, `alat_id`, `waktu_persiapan`, `waktu_produksi_per_unit`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 0, 120, '2025-03-27 07:26:13', '2025-03-27 07:26:13'),
	(2, 1, 1, 2, 0, 60, '2025-03-27 07:26:13', '2025-03-27 07:26:13'),
	(3, 1, 1, 3, 0, 60, '2025-03-27 07:26:13', '2025-03-27 07:26:13'),
	(4, 1, 2, 1, 0, 120, '2025-03-27 08:18:19', '2025-03-27 08:18:19'),
	(5, 1, 2, 2, 0, 60, '2025-03-27 08:18:19', '2025-03-27 08:18:19'),
	(6, 1, 2, 3, 0, 60, '2025-03-27 08:18:19', '2025-03-27 08:18:19');

-- Dumping structure for table grafika-printing.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.harga_grosir
CREATE TABLE IF NOT EXISTS `harga_grosir` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `bahan_id` bigint unsigned NOT NULL,
  `min_quantity` int NOT NULL,
  `max_quantity` int DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harga_grosir_vendor_id_foreign` (`vendor_id`),
  KEY `harga_grosir_bahan_id_foreign` (`bahan_id`),
  CONSTRAINT `harga_grosir_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `harga_grosir_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.harga_grosir: ~2 rows (approximately)
REPLACE INTO `harga_grosir` (`id`, `vendor_id`, `bahan_id`, `min_quantity`, `max_quantity`, `harga`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 100, 45000.00, '2025-03-27 07:19:12', '2025-03-27 07:19:12'),
	(2, 1, 1, 101, 1000, 40000.00, '2025-03-27 07:19:12', '2025-03-27 07:19:12');

-- Dumping structure for table grafika-printing.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.jobs: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.job_batches: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.kategori_produks
CREATE TABLE IF NOT EXISTS `kategori_produks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kategori_produks_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `kategori_produks_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.kategori_produks: ~0 rows (approximately)
REPLACE INTO `kategori_produks` (`id`, `vendor_id`, `nama_kategori`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Banner & Spanduk', 'banner-spanduk', '2025-03-27 07:26:13', '2025-03-27 07:26:13');

-- Dumping structure for table grafika-printing.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.migrations: ~0 rows (approximately)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_02_23_063831_create_vendors_table', 1),
	(5, '2025_02_23_063913_create_vendor_user_table', 1),
	(6, '2025_03_13_112500_create_alats_table', 1),
	(7, '2025_03_13_112747_create_kategori_produks_table', 1),
	(8, '2025_03_13_113011_create_produks_table', 1),
	(9, '2025_03_13_113252_create_pelanggans_table', 1),
	(10, '2025_03_13_113509_create_spesifikasis_table', 1),
	(11, '2025_03_13_114002_create_spesifikasi_produks_table', 1),
	(12, '2025_03_13_114145_create_estimasi_produks_table', 1),
	(13, '2025_03_13_114405_create_bahans_table', 1),
	(14, '2025_03_13_114854_create_bahan_spesifikasi_produk_table', 1),
	(15, '2025_03_13_115008_create_transaksis_table', 1),
	(16, '2025_03_13_120519_create_notifications_table', 1),
	(17, '2025_03_27_222451_add_payment_details_to_transaksis_table', 2);

-- Dumping structure for table grafika-printing.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.notifications: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table grafika-printing.pelanggans
CREATE TABLE IF NOT EXISTS `pelanggans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaksi_terakhir` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pelanggans_kode_unique` (`kode`),
  KEY `pelanggans_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `pelanggans_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.pelanggans: ~0 rows (approximately)
REPLACE INTO `pelanggans` (`id`, `vendor_id`, `kode`, `nama`, `alamat`, `no_telp`, `email`, `transaksi_terakhir`, `created_at`, `updated_at`) VALUES
	(1, 1, 'PLG-20250327212854', 'dad', 'dada', '322344', 'admin@gmail.com', '2025-04-07 21:20:02', '2025-03-27 14:28:54', '2025-04-07 21:20:02');

-- Dumping structure for table grafika-printing.produks
CREATE TABLE IF NOT EXISTS `produks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `gambar` json DEFAULT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produks_vendor_id_foreign` (`vendor_id`),
  KEY `produks_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `produks_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_produks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `produks_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.produks: ~2 rows (approximately)
REPLACE INTO `produks` (`id`, `vendor_id`, `gambar`, `nama_produk`, `deskripsi`, `kategori_id`, `created_at`, `updated_at`) VALUES
	(1, 1, '["produk_gambar/1743060373_67e4fd959e29b.png"]', 'Banner Indoor Full Color', 'Banner berkualitas tinggi untuk indoor dengan gambar resolusi tinggi', 1, '2025-03-27 07:26:13', '2025-03-27 07:26:13'),
	(2, 1, '[]', 'banner', 'banner bahan flexy', 1, '2025-03-27 08:18:19', '2025-03-27 08:18:19');

-- Dumping structure for table grafika-printing.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.sessions: ~1 rows (approximately)
REPLACE INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('1Tv79OUJpoWxcgB27b9iwFm2jE7oiLBj363rbkxY', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiR1lHdzlmSXVTZTVEZ3RqVVNsbWNTTk11U0FCdVo5YWg1ekJyMW12RyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9ncmFmaWthLXByaW50aW5nLnRlc3QvZGFzaGJvYXJkL3Bvcy9pbnZvaWNlLzgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6ImN1cnJlbnRfdmVuZG9yX2lkIjtpOjE7fQ==', 1744061000);

-- Dumping structure for table grafika-printing.spesifikasis
CREATE TABLE IF NOT EXISTS `spesifikasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `nama_spesifikasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_input` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spesifikasis_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `spesifikasis_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.spesifikasis: ~2 rows (approximately)
REPLACE INTO `spesifikasis` (`id`, `vendor_id`, `nama_spesifikasi`, `tipe_input`, `satuan`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Bahan', 'select', '', '2025-03-27 07:22:48', '2025-03-27 07:22:48'),
	(2, 1, 'Ukuran', 'number', 'meter/persegi', '2025-03-27 07:23:15', '2025-03-27 07:23:15'),
	(3, 1, 'Finishing', 'select', '', '2025-03-27 07:23:29', '2025-03-27 07:23:29');

-- Dumping structure for table grafika-printing.spesifikasi_produks
CREATE TABLE IF NOT EXISTS `spesifikasi_produks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `spesifikasi_id` bigint unsigned NOT NULL,
  `wajib_diisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilihan` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spesifikasi_produks_vendor_id_foreign` (`vendor_id`),
  KEY `spesifikasi_produks_produk_id_foreign` (`produk_id`),
  KEY `spesifikasi_produks_spesifikasi_id_foreign` (`spesifikasi_id`),
  CONSTRAINT `spesifikasi_produks_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `spesifikasi_produks_spesifikasi_id_foreign` FOREIGN KEY (`spesifikasi_id`) REFERENCES `spesifikasis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `spesifikasi_produks_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.spesifikasi_produks: ~6 rows (approximately)
REPLACE INTO `spesifikasi_produks` (`id`, `vendor_id`, `produk_id`, `spesifikasi_id`, `wajib_diisi`, `pilihan`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, '1', '[]', '2025-03-27 07:26:13', '2025-03-27 07:26:13'),
	(2, 1, 1, 2, '1', '[]', '2025-03-27 07:26:13', '2025-03-27 07:26:13'),
	(3, 1, 1, 3, '1', '[]', '2025-03-27 07:26:13', '2025-03-27 07:29:18'),
	(4, 1, 2, 1, '1', '["Flexy"]', '2025-03-27 08:18:19', '2025-03-27 08:18:19'),
	(5, 1, 2, 2, '1', '[]', '2025-03-27 08:18:19', '2025-03-27 08:18:19'),
	(6, 1, 2, 3, '1', '[]', '2025-03-27 08:18:19', '2025-03-27 08:18:19');

-- Dumping structure for table grafika-printing.transaksis
CREATE TABLE IF NOT EXISTS `transaksis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `pelanggan_id` bigint unsigned NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `terbayar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kembali` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','completed','cancelled','quality_check','processing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimasi_selesai` timestamp NOT NULL,
  `tanggal_dibuat` date NOT NULL,
  `progress_percentage` int NOT NULL DEFAULT '0',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksis_kode_unique` (`kode`),
  KEY `transaksis_vendor_id_foreign` (`vendor_id`),
  KEY `transaksis_user_id_foreign` (`user_id`),
  KEY `transaksis_pelanggan_id_foreign` (`pelanggan_id`),
  CONSTRAINT `transaksis_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksis_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.transaksis: ~6 rows (approximately)
REPLACE INTO `transaksis` (`id`, `vendor_id`, `kode`, `user_id`, `pelanggan_id`, `total_harga`, `terbayar`, `kembali`, `status`, `payment_method`, `estimasi_selesai`, `tanggal_dibuat`, `progress_percentage`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 1, 'TRX-20250327-6414', 2, 1, 582500.00, 0.00, 0.00, 'pending', 'cash', '2025-03-27 18:28:56', '2025-03-27', 0, 'fdfgsdgg', '2025-03-27 14:28:56', '2025-03-27 14:28:56'),
	(2, 1, 'TRX-20250327-4320', 2, 1, 582500.00, 0.00, 0.00, 'pending', 'cash', '2025-03-27 22:28:56', '2025-03-27', 0, NULL, '2025-03-27 15:22:41', '2025-03-27 15:22:41'),
	(3, 1, 'TRX-20250327-4454', 2, 1, 582500.00, 582500.00, 0.00, 'pending', 'cash', '2025-03-28 02:28:56', '2025-03-27', 0, NULL, '2025-03-27 15:29:16', '2025-03-27 15:29:16'),
	(4, 1, 'TRX-20250327-5728', 2, 1, 5467500.00, 5467500.00, 0.00, 'pending', 'cash', '2025-03-28 10:28:56', '2025-03-27', 0, NULL, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(7, 1, 'TRX-20250408-3540', 2, 1, 582500.00, 582500.00, 0.00, 'completed', 'cash', '2025-03-27 17:00:00', '2025-04-08', 100, 'fcsdfsdf', '2025-04-07 18:50:18', '2025-04-07 21:12:52'),
	(8, 1, 'TRX-20250408-5411', 2, 1, 48930000.00, 48930000.00, 0.00, 'pending', 'cash', '2025-04-11 10:28:56', '2025-04-08', 0, NULL, '2025-04-07 21:20:02', '2025-04-07 21:20:02');

-- Dumping structure for table grafika-printing.transaksi_items
CREATE TABLE IF NOT EXISTS `transaksi_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `transaksi_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `kuantitas` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_items_vendor_id_foreign` (`vendor_id`),
  KEY `transaksi_items_transaksi_id_foreign` (`transaksi_id`),
  KEY `transaksi_items_produk_id_foreign` (`produk_id`),
  CONSTRAINT `transaksi_items_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_items_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.transaksi_items: ~7 rows (approximately)
REPLACE INTO `transaksi_items` (`id`, `vendor_id`, `transaksi_id`, `produk_id`, `kuantitas`, `harga_satuan`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 582500.00, '2025-03-27 14:28:56', '2025-03-27 14:28:56'),
	(2, 1, 2, 1, 1, 582500.00, '2025-03-27 15:22:41', '2025-03-27 15:22:41'),
	(3, 1, 3, 1, 1, 582500.00, '2025-03-27 15:29:16', '2025-03-27 15:29:16'),
	(4, 1, 4, 1, 1, 582500.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(5, 1, 4, 1, 1, 4885000.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(8, 1, 7, 1, 1, 582500.00, '2025-04-07 18:50:18', '2025-04-07 18:50:18'),
	(9, 1, 8, 1, 84, 582500.00, '2025-04-07 21:20:02', '2025-04-07 21:20:02');

-- Dumping structure for table grafika-printing.transaksi_item_specifications
CREATE TABLE IF NOT EXISTS `transaksi_item_specifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `transaksi_item_id` bigint unsigned NOT NULL,
  `spesifikasi_produk_id` bigint unsigned NOT NULL,
  `bahan_id` bigint unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_item_specifications_vendor_id_foreign` (`vendor_id`),
  KEY `transaksi_item_specifications_transaksi_item_id_foreign` (`transaksi_item_id`),
  KEY `transaksi_item_specifications_spesifikasi_produk_id_foreign` (`spesifikasi_produk_id`),
  KEY `transaksi_item_specifications_bahan_id_foreign` (`bahan_id`),
  CONSTRAINT `transaksi_item_specifications_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_item_specifications_spesifikasi_produk_id_foreign` FOREIGN KEY (`spesifikasi_produk_id`) REFERENCES `spesifikasi_produks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_item_specifications_transaksi_item_id_foreign` FOREIGN KEY (`transaksi_item_id`) REFERENCES `transaksi_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_item_specifications_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.transaksi_item_specifications: ~18 rows (approximately)
REPLACE INTO `transaksi_item_specifications` (`id`, `vendor_id`, `transaksi_item_id`, `spesifikasi_produk_id`, `bahan_id`, `value`, `input_type`, `price`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, '1', 'select', 45000.00, '2025-03-27 14:28:56', '2025-03-27 14:28:56'),
	(2, 1, 1, 2, 1, '11.5', 'number', 517500.00, '2025-03-27 14:28:56', '2025-03-27 14:28:56'),
	(3, 1, 1, 3, 2, '2', 'select', 20000.00, '2025-03-27 14:28:56', '2025-03-27 14:28:56'),
	(4, 1, 2, 1, 1, '1', 'select', 45000.00, '2025-03-27 15:22:41', '2025-03-27 15:22:41'),
	(5, 1, 2, 2, 1, '11.5', 'number', 517500.00, '2025-03-27 15:22:41', '2025-03-27 15:22:41'),
	(6, 1, 2, 3, 2, '2', 'select', 20000.00, '2025-03-27 15:22:41', '2025-03-27 15:22:41'),
	(7, 1, 3, 1, 1, '1', 'select', 45000.00, '2025-03-27 15:29:16', '2025-03-27 15:29:16'),
	(8, 1, 3, 2, 1, '11.5', 'number', 517500.00, '2025-03-27 15:29:16', '2025-03-27 15:29:16'),
	(9, 1, 3, 3, 2, '2', 'select', 20000.00, '2025-03-27 15:29:16', '2025-03-27 15:29:16'),
	(10, 1, 4, 1, 1, '1', 'select', 45000.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(11, 1, 4, 2, 1, '11.5', 'number', 517500.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(12, 1, 4, 3, 2, '2', 'select', 20000.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(13, 1, 5, 1, 1, '1', 'select', 45000.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(14, 1, 5, 2, 1, '120.5', 'number', 4820000.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(15, 1, 5, 3, 2, '2', 'select', 20000.00, '2025-03-27 15:39:52', '2025-03-27 15:39:52'),
	(19, 1, 9, 1, 1, '1', 'select', 3780000.00, '2025-04-07 21:20:02', '2025-04-07 21:20:02'),
	(20, 1, 9, 2, 1, '11.5', 'number', 43470000.00, '2025-04-07 21:20:02', '2025-04-07 21:20:02'),
	(21, 1, 9, 3, 2, '2', 'select', 1680000.00, '2025-04-07 21:20:02', '2025-04-07 21:20:02');

-- Dumping structure for table grafika-printing.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype` enum('dev','vendor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vendor',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.users: ~2 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `usertype`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Dev', 'dev@gmail.com', '2025-03-26 20:31:59', '$2y$12$dm4QsIvNB5X8C3Kf3DVe9.8MW0Sb5kx4uZwOExY9GnvjWa150TZqi', 'dev', 'SiwmZdrEOmg04t0hKNG7KCXnHmnh22XEvnQZOBjtBgCdcWhkspv6sQSQAx0n', '2025-03-26 20:31:59', '2025-03-26 20:31:59'),
	(2, 'Vendor', 'vendor@gmail.com', '2025-03-26 20:31:59', '$2y$12$dm4QsIvNB5X8C3Kf3DVe9.8MW0Sb5kx4uZwOExY9GnvjWa150TZqi', 'vendor', 'QQFSdGuJVDHEnEfaq9KPMqvvcCYXAg2HdefDFna57txmDHwEVsFittosVazS', '2025-03-26 20:31:59', '2025-03-26 20:31:59');

-- Dumping structure for table grafika-printing.vendors
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendors_email_unique` (`email`),
  UNIQUE KEY `vendors_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.vendors: ~0 rows (approximately)
REPLACE INTO `vendors` (`id`, `name`, `email`, `phone`, `address`, `logo`, `website`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Grafika Printing', 'grafika@gmail.com', '081234567890', 'Jl. Grafika No. 1', '1743021207.png', 'https://grafika-printing.com', 1, '2025-03-26 20:31:59', '2025-03-26 20:33:27');

-- Dumping structure for table grafika-printing.vendor_user
CREATE TABLE IF NOT EXISTS `vendor_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_user_vendor_id_foreign` (`vendor_id`),
  KEY `vendor_user_user_id_foreign` (`user_id`),
  CONSTRAINT `vendor_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendor_user_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grafika-printing.vendor_user: ~0 rows (approximately)
REPLACE INTO `vendor_user` (`id`, `vendor_id`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
