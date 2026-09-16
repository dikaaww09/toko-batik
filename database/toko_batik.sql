-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: toko_batik
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','$2y$12$la7jjheqTiEGH3JMiMSnp.wUI6FM0nLakrY9CcSSgt56s4W87Gs7q','2026-09-07 15:24:17','2026-09-07 15:48:46');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (3,'Blus Batik'),(1,'Kain Batik'),(2,'Kemeja Batik');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-09-07-000001','App\\Database\\Migrations\\CreateShop','default','App',1788769449,1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `kategori_id` int(10) unsigned NOT NULL,
  `harga` decimal(12,0) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status_ketersediaan` varchar(20) NOT NULL DEFAULT 'tersedia',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `products_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Kain Batik Kawung Sogan','kain-batik-kawung-sogan',1,185000,'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.','sample-1.svg','tersedia','2026-09-07 15:24:17','2026-09-07 15:24:17'),(2,'Kemeja Batik Parang Bumi','kemeja-batik-parang-bumi',2,245000,'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.','sample-2.svg','tersedia','2026-09-07 15:24:17','2026-09-07 15:24:17'),(3,'Blus Batik Sekar Krem','blus-batik-sekar-krem',3,225000,'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.','sample-3.svg','tersedia','2026-09-07 15:24:17','2026-09-07 15:24:17'),(4,'Kain Batik Lereng Terakota','kain-batik-lereng-terakota',1,195000,'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.','sample-4.svg','tersedia','2026-09-07 15:24:17','2026-09-07 15:24:17'),(5,'Kemeja Batik Kawung Malam','kemeja-batik-kawung-malam',2,265000,'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.','sample-5.svg','tersedia','2026-09-07 15:24:17','2026-09-07 15:24:17'),(6,'Blus Batik Puspa Tanah','blus-batik-puspa-tanah',3,235000,'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.','sample-6.svg','tidak_tersedia','2026-09-07 15:24:17','2026-09-07 15:24:17');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-07 19:06:34
