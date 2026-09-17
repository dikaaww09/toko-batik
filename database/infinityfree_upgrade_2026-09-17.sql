-- Upgrade database Batik Pusaka di InfinityFree.
-- Dibuat untuk struktur backup if0_42853862_toko_batik tanggal 16 September 2026.
-- Jalankan satu kali melalui phpMyAdmin setelah menyimpan backup.

ALTER TABLE `products`
  ADD COLUMN `stok` INT UNSIGNED NOT NULL DEFAULT 10 AFTER `harga`;

-- Produk yang sebelumnya ditandai tidak tersedia dimulai dengan stok 0.
UPDATE `products`
SET `stok` = 0
WHERE `status_ketersediaan` = 'tidak_tersedia';

CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_order` VARCHAR(30) NOT NULL,
  `nama_pembeli` VARCHAR(120) NOT NULL,
  `whatsapp` VARCHAR(20) NOT NULL,
  `alamat` TEXT NOT NULL,
  `catatan` TEXT DEFAULT NULL,
  `total` DECIMAL(12,0) NOT NULL,
  `metode_pembayaran` VARCHAR(20) NOT NULL DEFAULT 'cod',
  `status_pembayaran` VARCHAR(20) NOT NULL DEFAULT 'belum_dibayar',
  `status` VARCHAR(20) NOT NULL DEFAULT 'baru',
  `stok_dikembalikan` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_order` (`kode_order`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED DEFAULT NULL,
  `nama_produk` VARCHAR(150) NOT NULL,
  `harga` DECIMAL(12,0) NOT NULL,
  `qty` INT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(12,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tandai migration aplikasi sebagai sudah diterapkan agar tidak dijalankan ulang.
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT '2026-09-16-000002', 'App\\Database\\Migrations\\CreateOrders', 'default', 'App', UNIX_TIMESTAMP(), 2
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` WHERE `version` = '2026-09-16-000002' AND `namespace` = 'App'
);

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT '2026-09-17-000003', 'App\\Database\\Migrations\\AddStockAndPayment', 'default', 'App', UNIX_TIMESTAMP(), 3
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` WHERE `version` = '2026-09-17-000003' AND `namespace` = 'App'
);
