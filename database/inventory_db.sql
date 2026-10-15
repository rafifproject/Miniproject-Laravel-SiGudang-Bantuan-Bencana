-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: inventory_db
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `inventory_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `inventory_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `inventory_db`;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Pangan','Bahan makanan dan minuman untuk korban bencana','2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,'Sandang','Pakaian dan perlengkapan berpakaian','2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,'Kesehatan','Obat-obatan dan alat kesehatan','2026-10-14 22:30:35','2026-10-14 22:30:35'),(4,'Peralatan','Peralatan rumah tangga dan tenda pengungsian','2026-10-14 22:30:35','2026-10-14 22:30:35'),(5,'Higienitas','Sabun, sampo, dan perlengkapan kebersihan diri','2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `distribution_targets`
--

DROP TABLE IF EXISTS `distribution_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `distribution_targets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_targets`
--

LOCK TABLES `distribution_targets` WRITE;
/*!40000 ALTER TABLE `distribution_targets` DISABLE KEYS */;
INSERT INTO `distribution_targets` VALUES (1,'Posko Cianjur','Cianjur, Jawa Barat','Posko bantuan gempa Cianjur','2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,'Pengungsian Palu','Palu, Sulawesi Tengah','Pengungsian pasca gempa Palu','2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,'Pos Banjir Bekasi','Bekasi, Jawa Barat','Posko banjir Bekasi','2026-10-14 22:30:35','2026-10-14 22:30:35'),(4,'Pengungsian Lombok','Lombok, NTB','Pengungsian gempa Lombok','2026-10-14 22:30:35','2026-10-14 22:30:35'),(5,'Pos Gunung Semeru','Lumajang, Jawa Timur','Posko erupsi Gunung Semeru','2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `distribution_targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_10_15_000001_create_user_profiles_table',1),(6,'2026_10_15_000002_create_categories_table',1),(7,'2026_10_15_000003_create_products_table',1),(8,'2026_10_15_000004_create_suppliers_table',1),(9,'2026_10_15_000005_create_product_suppliers_table',1),(10,'2026_10_15_000006_create_distribution_targets_table',1),(11,'2026_10_15_000007_create_stock_movements_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_suppliers`
--

DROP TABLE IF EXISTS `product_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_suppliers` (
  `product_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `supplier_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`,`supplier_id`),
  KEY `product_suppliers_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `product_suppliers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_suppliers_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_suppliers`
--

LOCK TABLES `product_suppliers` WRITE;
/*!40000 ALTER TABLE `product_suppliers` DISABLE KEYS */;
INSERT INTO `product_suppliers` VALUES (1,1,72000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(1,2,70000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,1,92000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,3,43000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(4,3,33000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(6,1,82000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(6,4,80000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(9,1,120000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(9,2,118000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(10,1,26000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(11,1,33000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(12,2,1450000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(13,3,72000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(14,4,46000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(15,4,14000.00,'2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `product_suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `minimum_stock` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'PRD001','Beras Premium 5kg','karung',75000.00,1250,200,'Beras kualitas premium untuk pengungsi',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,1,'PRD002','Mi Instan (karton 40pcs)','karton',95000.00,480,100,'Mi instan berbagai rasa',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,1,'PRD003','Air Mineral 600ml (karton)','karton',45000.00,2300,300,'Air mineral dalam kemasan',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(4,1,'PRD004','Minyak Goreng 2L','botol',35000.00,380,80,'Minyak goreng kemasan',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(5,1,'PRD005','Telur Ayam (tray 30butir)','tray',62000.00,150,50,'Telur ayam segar',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(6,2,'PRD006','Selimut Tebal','lembar',85000.00,430,100,'Selimut untuk pengungsi',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(7,2,'PRD007','Pakaian Dewasa Campur','set',55000.00,300,80,'Paket pakaian dewasa layak pakai',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(8,2,'PRD008','Pakaian Anak','set',45000.00,220,60,'Paket pakaian anak',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(9,3,'PRD009','Obat P3K Kit','kotak',125000.00,180,50,'Kotak P3K lengkap',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(10,3,'PRD010','Parasetamol 500mg (box)','box',28000.00,420,100,'Parasetamol kemasan box',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(11,3,'PRD011','Masker Medis (box 50pcs)','box',35000.00,650,150,'Masker medis 3-ply',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(12,4,'PRD012','Tenda Pengungsian','unit',1500000.00,45,10,'Tenda kapasitas 6 orang',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(13,4,'PRD013','Terpal 4x6m','lembar',75000.00,120,30,'Terpal tebal warna biru',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(14,5,'PRD014','Sabun Mandi (lusin)','lusin',48000.00,280,80,'Sabun mandi batang',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(15,5,'PRD015','Pasta Gigi','buah',15000.00,550,100,'Pasta gigi 120gr',1,'2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `distribution_target_id` bigint unsigned DEFAULT NULL,
  `type` enum('IN','OUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_foreign` (`product_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_supplier_id_foreign` (`supplier_id`),
  KEY `stock_movements_distribution_target_id_foreign` (`distribution_target_id`),
  KEY `stock_movements_date_index` (`date`),
  KEY `stock_movements_type_index` (`type`),
  CONSTRAINT `stock_movements_distribution_target_id_foreign` FOREIGN KEY (`distribution_target_id`) REFERENCES `distribution_targets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `stock_movements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,1,1,1,NULL,'IN',500,'2026-01-05','REF-2026-001','Donasi awal masuk gudang',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,1,1,2,NULL,'IN',300,'2026-01-10','REF-2026-002','Pengiriman kedua dari PMI',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,1,2,NULL,1,'OUT',200,'2026-01-15','TRX-2026-001','Distribusi ke Posko Cianjur',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(4,2,1,1,NULL,'IN',200,'2026-01-05','REF-2026-003','Donasi mi instan',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(5,2,2,NULL,2,'OUT',80,'2026-01-20','TRX-2026-002','Distribusi ke Pengungsian Palu',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(6,3,1,3,NULL,'IN',1000,'2026-01-05','REF-2026-004','Donasi air mineral',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(7,3,2,NULL,1,'OUT',500,'2026-01-18','TRX-2026-003','Air mineral ke Cianjur',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(8,6,1,1,NULL,'IN',200,'2026-01-08','REF-2026-005','Selimut dari PMI',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(9,6,2,NULL,3,'OUT',100,'2026-01-25','TRX-2026-004','Selimut ke Bekasi',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(10,9,1,1,NULL,'IN',100,'2026-02-01','REF-2026-006','P3K kit dari PMI',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(11,9,2,NULL,4,'OUT',50,'2026-02-10','TRX-2026-005','P3K ke Lombok',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(12,12,1,2,NULL,'IN',30,'2026-02-05','REF-2026-007','Tenda dari BNPB',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(13,12,2,NULL,5,'OUT',15,'2026-02-15','TRX-2026-006','Tenda ke Semeru',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(14,10,1,1,NULL,'IN',200,'2026-02-10','REF-2026-008','Obat dari PMI',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(15,11,1,1,NULL,'IN',300,'2026-02-12','REF-2026-009','Masker dari PMI',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(16,11,2,NULL,1,'OUT',100,'2026-03-01','TRX-2026-007','Masker ke Cianjur',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(17,4,1,3,NULL,'IN',150,'2026-03-05','REF-2026-010','Minyak goreng',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(18,4,2,NULL,2,'OUT',80,'2026-03-10','TRX-2026-008','Minyak ke Palu',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(19,14,1,4,NULL,'IN',150,'2026-03-08','REF-2026-011','Sabun dari Yayasan',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(20,14,2,NULL,3,'OUT',70,'2026-03-15','TRX-2026-009','Sabun ke Bekasi',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(21,7,1,4,NULL,'IN',200,'2026-03-10','REF-2026-012','Pakaian dewasa',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(22,7,2,NULL,4,'OUT',100,'2026-03-20','TRX-2026-010','Pakaian ke Lombok',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(23,8,1,4,NULL,'IN',150,'2026-03-12','REF-2026-013','Pakaian anak',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(24,8,2,NULL,5,'OUT',70,'2026-03-22','TRX-2026-011','Pakaian ke Semeru',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(25,1,2,NULL,4,'OUT',150,'2026-04-01','TRX-2026-012','Beras ke Lombok',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(26,2,2,NULL,5,'OUT',60,'2026-04-05','TRX-2026-013','Mi ke Semeru',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(27,5,1,3,NULL,'IN',100,'2026-04-08','REF-2026-014','Telur donasi',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(28,5,2,NULL,1,'OUT',60,'2026-04-12','TRX-2026-014','Telur ke Cianjur',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(29,13,1,3,NULL,'IN',80,'2026-04-15','REF-2026-015','Terpal dari perusahaan',1,'2026-10-14 22:30:36','2026-10-14 22:30:36'),(30,13,2,NULL,2,'OUT',40,'2026-04-20','TRX-2026-015','Terpal ke Palu',1,'2026-10-14 22:30:36','2026-10-14 22:30:36');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Lembaga',
  `contact` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Palang Merah Indonesia','NGO','021-7992325','info@pmi.or.id','Jakarta Selatan','Organisasi kemanusiaan nasional',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,'BNPB - Badan Nasional','Pemerintah','021-5703867','info@bnpb.go.id','Jakarta Timur','Badan Nasional Penanggulangan Bencana',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,'PT. Indah Karya','Perusahaan','021-4545678','csr@indahkarya.co.id','Bandung','Donasi CSR perusahaan manufaktur',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(4,'Yayasan Peduli Sesama','Yayasan','021-8891234','info@yps.or.id','Yogyakarta','Yayasan sosial kemanusiaan',1,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(5,'Bank Mandiri (CSR)','Perusahaan','021-5299700','csr@bankmandiri.co.id','Jakarta Pusat','Program CSR Bank Mandiri',0,'2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,1,'Administrator Sistem','081234567890','Kantor BPBD / Gudang Pusat Logistik','2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,2,'Budi Santoso','081234567891','Gudang Posko Lapangan 1','2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,3,'Siti Rahayu','081234567892','Gudang Posko Lapangan 2','2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$12$wNn/xh9Imb1bcKDSyCoPgeUdZQWv.10.Pk0TUDXSpVcF7mPjjQWcK','admin','Administrator Sistem','admin@sigudang.id',1,NULL,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(2,'staff1','$2y$12$BEiqcMCS8M2wfG0ssnXf0.fQ6pccnMATnI2geMvaAYWES8jOh6miW','staff','Budi Santoso','budi@sigudang.id',1,NULL,'2026-10-14 22:30:35','2026-10-14 22:30:35'),(3,'staff2','$2y$12$R.xbnBgpIcWUXuyBhaY5RekBlqId25nrJK8B7hJfa/aW9nV2u//QW','staff','Siti Rahayu','siti@sigudang.id',1,NULL,'2026-10-14 22:30:35','2026-10-14 22:30:35');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-15 12:30:48
