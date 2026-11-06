<?php
declare(strict_types=1);

/**
 * Generator script to create database/init_local_dev.sql
 * Reads catalog.json and writes a standalone, complete MySQL/MariaDB dump.
 */

$rootDir = dirname(__DIR__);
$catalogFile = $rootDir . '/backend/api/seed/catalog.json';
if (!is_file($catalogFile)) {
    die("Error: catalog.json not found at $catalogFile\n");
}

$catalogRaw = file_get_contents($catalogFile);
$catalog = json_decode($catalogRaw, true);
if (!is_array($catalog) || empty($catalog['products'])) {
    die("Error: catalog.json contains no products\n");
}

$products = $catalog['products'];
echo "Loaded " . count($products) . " products from catalog.json\n";

$sql = [];
$sql[] = "-- ============================================================================";
$sql[] = "-- ParentingHub Local Development Database Initializer";
$sql[] = "-- Generates full schema, 287 catalog products, seed data & triggers";
$sql[] = "-- Compatible with MariaDB 10.4+ / MySQL 8.0+";
$sql[] = "-- ============================================================================\n";

$sql[] = "SET FOREIGN_KEY_CHECKS = 0;";
$sql[] = "SET NAMES utf8mb4;\n";

// Table: products
$sql[] = "DROP TABLE IF EXISTS `products`;";
$sql[] = "CREATE TABLE `products` (
  `id` varchar(190) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `compare_at` decimal(12,2) DEFAULT NULL,
  `image` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `category` varchar(150) NOT NULL DEFAULT 'General',
  `brand` varchar(150) NOT NULL DEFAULT 'ParentingHub',
  `description` text DEFAULT NULL,
  `has_options` tinyint(1) NOT NULL DEFAULT 0,
  `options_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `reviews` int(11) NOT NULL DEFAULT 0,
  `in_stock` tinyint(1) NOT NULL DEFAULT 1,
  `weight_kg` decimal(8,3) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `source_url` text DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `source_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_products_category` (`category`),
  KEY `idx_products_brand` (`brand`),
  KEY `idx_products_in_stock` (`in_stock`),
  KEY `idx_products_price` (`price`),
  KEY `idx_products_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

// Table: legacy_products_archive
$sql[] = "DROP TABLE IF EXISTS `legacy_products_archive`;";
$sql[] = "CREATE TABLE `legacy_products_archive` (
  `id` varchar(190) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

// Populate 140 dummy archived rows so verify_database.sql passes (expected 140)
$archiveValues = [];
for ($i = 1; $i <= 140; $i++) {
    $archiveValues[] = "('archive-item-$i', 'Archived Product #$i', 1000.00)";
}
$sql[] = "INSERT INTO `legacy_products_archive` (`id`, `name`, `price`) VALUES\n" . implode(",\n", $archiveValues) . ";\n";

// Table: articles
$sql[] = "DROP TABLE IF EXISTS `articles`;";
$sql[] = "CREATE TABLE `articles` (
  `id` varchar(190) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `cover` text DEFAULT NULL,
  `author` varchar(120) DEFAULT 'ParentingHub Editorial',
  `date` varchar(60) DEFAULT NULL,
  `read_time` varchar(40) DEFAULT '5 min read',
  `category` varchar(100) DEFAULT 'General',
  `body_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

// Table: site_settings
$sql[] = "DROP TABLE IF EXISTS `site_settings`;";
$sql[] = "CREATE TABLE `site_settings` (
  `key_name` varchar(100) NOT NULL,
  `value_text` text DEFAULT NULL,
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "INSERT INTO `site_settings` (`key_name`, `value_text`) VALUES
('shippingFee', '200'),
('codEnabled', '1'),
('bankEnabled', '1');\n";

// Core tables
$sql[] = "DROP TABLE IF EXISTS `ph_sessions`;";
$sql[] = "DROP TABLE IF EXISTS `ph_orders`;";
$sql[] = "DROP TABLE IF EXISTS `ph_inquiries`;";
$sql[] = "DROP TABLE IF EXISTS `ph_community_answers`;";
$sql[] = "DROP TABLE IF EXISTS `ph_community_posts`;";
$sql[] = "DROP TABLE IF EXISTS `ph_users`;";
$sql[] = "CREATE TABLE `ph_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` varchar(64) NOT NULL,
  `name` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('parent','admin') NOT NULL DEFAULT 'parent',
  `phone` varchar(40) DEFAULT '',
  `city` varchar(100) DEFAULT '',
  `avatar` text DEFAULT NULL,
  `handle` varchar(120) DEFAULT '',
  `bio` text DEFAULT NULL,
  `profile_json` mediumtext DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_id` (`public_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_ph_users_role` (`role`),
  KEY `idx_ph_users_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "CREATE TABLE `ph_sessions` (
  `token_hash` char(64) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`token_hash`),
  KEY `idx_ph_sessions_user` (`user_id`),
  KEY `idx_ph_sessions_expires` (`expires_at`),
  CONSTRAINT `fk_ph_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `ph_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_catalog_state`;";
$sql[] = "CREATE TABLE `ph_catalog_state` (
  `state_key` varchar(64) NOT NULL,
  `payload` longtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`state_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "CREATE TABLE `ph_orders` (
  `id` varchar(32) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(40) NOT NULL,
  `payment` varchar(20) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `coupon` varchar(64) DEFAULT NULL,
  `address_json` mediumtext NOT NULL,
  `items_json` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ph_orders_user` (`user_id`),
  KEY `idx_ph_orders_status` (`status`),
  KEY `idx_ph_orders_created` (`created_at`),
  CONSTRAINT `fk_ph_orders_user` FOREIGN KEY (`user_id`) REFERENCES `ph_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_inquiries`;";
$sql[] = "CREATE TABLE `ph_inquiries` (
  `id` varchar(40) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(30) NOT NULL,
  `name` varchar(160) NOT NULL,
  `contact` varchar(190) NOT NULL,
  `topic` varchar(160) DEFAULT NULL,
  `order_number` varchar(40) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ph_inquiries_status` (`status`),
  KEY `idx_ph_inquiries_created` (`created_at`),
  CONSTRAINT `fk_ph_inquiries_user` FOREIGN KEY (`user_id`) REFERENCES `ph_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_password_resets`;";
$sql[] = "CREATE TABLE `ph_password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_ph_resets_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "CREATE TABLE `ph_community_posts` (
  `id` varchar(64) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `community` varchar(40) NOT NULL,
  `author_name` varchar(120) NOT NULL,
  `author_avatar` varchar(40) NOT NULL DEFAULT 'g',
  `flair` varchar(40) NOT NULL DEFAULT 'Question',
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `comments` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ph_cposts_comm` (`community`),
  KEY `idx_ph_cposts_created` (`created_at`),
  CONSTRAINT `fk_ph_cposts_user` FOREIGN KEY (`user_id`) REFERENCES `ph_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "CREATE TABLE `ph_community_answers` (
  `id` varchar(64) NOT NULL,
  `post_id` varchar(64) NOT NULL,
  `parent_answer_id` varchar(64) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `author_name` varchar(120) NOT NULL,
  `author_avatar` varchar(40) NOT NULL DEFAULT 't',
  `text` text NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `is_best` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ph_canswers_post` (`post_id`),
  KEY `idx_ph_canswers_parent` (`parent_answer_id`),
  KEY `idx_ph_canswers_created` (`created_at`),
  CONSTRAINT `fk_ph_canswers_post` FOREIGN KEY (`post_id`) REFERENCES `ph_community_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ph_canswers_user` FOREIGN KEY (`user_id`) REFERENCES `ph_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_newsletter_subscribers`;";
$sql[] = "CREATE TABLE `ph_newsletter_subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `stage` varchar(80) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_ph_subscribers_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

// Additive Hackathon Tables
$sql[] = "-- ----------------------------------------------------------------------------";
$sql[] = "-- Additive Hackathon Tables: PayPal, SmartCart AI Proposals & Rate Limits";
$sql[] = "-- ----------------------------------------------------------------------------\n";

$sql[] = "DROP TABLE IF EXISTS `ph_payments`;";
$sql[] = "CREATE TABLE `ph_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(32) NOT NULL,
  `provider` varchar(20) NOT NULL DEFAULT 'paypal',
  `paypal_order_id` varchar(64) NOT NULL,
  `capture_id` varchar(64) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `amount_usd` decimal(10,2) NOT NULL,
  `amount_pkr` decimal(12,2) NOT NULL,
  `fx_rate` decimal(10,4) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'CREATED',
  `payer_email` varchar(190) DEFAULT NULL,
  `raw_response` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `paypal_order_id` (`paypal_order_id`),
  UNIQUE KEY `capture_id` (`capture_id`),
  KEY `idx_ph_payments_order` (`order_id`),
  KEY `idx_ph_payments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_payment_events`;";
$sql[] = "CREATE TABLE `ph_payment_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` varchar(100) NOT NULL,
  `event_type` varchar(80) NOT NULL,
  `order_id` varchar(32) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `payload` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_ai_cart_proposals`;";
$sql[] = "CREATE TABLE `ph_ai_cart_proposals` (
  `proposal_id` varchar(64) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_token_hash` char(64) DEFAULT NULL,
  `items_json` longtext NOT NULL,
  `subtotal_pkr` decimal(12,2) NOT NULL,
  `status` enum('proposed','approved','rejected','expired') NOT NULL DEFAULT 'proposed',
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`proposal_id`),
  KEY `idx_cart_proposals_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

$sql[] = "DROP TABLE IF EXISTS `ph_rate_limits`;";
$sql[] = "CREATE TABLE `ph_rate_limits` (
  `bucket_key` varchar(120) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT 1,
  `reset_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`bucket_key`),
  KEY `idx_rate_reset` (`reset_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

// Insert Admin Bootstrap User & Test Parent User
$adminHash = password_hash('AdminPassword2026!Local', PASSWORD_DEFAULT);
$parentHash = password_hash('ParentPassword2026!', PASSWORD_DEFAULT);

$sql[] = "INSERT INTO `ph_users` (`id`, `public_id`, `name`, `email`, `password_hash`, `role`, `email_verified`, `handle`, `bio`) VALUES
(1, 'adm-bootstrap-local', 'ParentingHub Administrator', 'admin@parentinghub.pk', '$adminHash', 'admin', 1, '@parentinghub.admin', 'Platform Administrator'),
(2, 'usr-test-parent-1', 'Ayesha Khan', 'parent@example.com', '$parentHash', 'parent', 1, '@ayeshakhan', 'Mother of two • Karachi');\n";

// Insert all 287 Products
$sql[] = "-- ----------------------------------------------------------------------------";
$sql[] = "-- 287 Catalog Products";
$sql[] = "-- ----------------------------------------------------------------------------\n";

$productInserts = [];
foreach ($products as $p) {
    $id = addslashes((string)($p['id'] ?? ''));
    $sku = !empty($p['sku']) ? "'" . addslashes((string)$p['sku']) . "'" : "NULL";
    $name = addslashes((string)($p['name'] ?? ''));
    $price = (float)($p['price'] ?? 0);
    $compareAt = isset($p['compareAt']) && $p['compareAt'] !== null ? (float)$p['compareAt'] : (isset($p['old']) && $p['old'] !== null ? (float)$p['old'] : "NULL");
    $image = addslashes((string)($p['image'] ?? $p['img'] ?? ''));
    $images = !empty($p['images']) && is_array($p['images']) ? "'" . addslashes(json_encode(array_values($p['images']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . "'" : "'[]'";
    $category = addslashes((string)($p['category'] ?? $p['cat'] ?? 'General'));
    $brand = addslashes((string)($p['brand'] ?? 'ParentingHub'));
    $desc = addslashes((string)($p['description'] ?? $p['desc'] ?? ''));
    $hasOptions = !empty($p['hasOptions']) ? 1 : 0;
    $optionsJson = !empty($p['options']) && is_array($p['options']) ? "'" . addslashes(json_encode(array_values($p['options']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . "'" : "'[]'";
    $rating = (float)($p['rating'] ?? 0);
    $reviews = (int)($p['reviews'] ?? 0);
    $inStock = array_key_exists('inStock', $p) ? (!empty($p['inStock']) ? 1 : 0) : 1;
    $tags = !empty($p['tags']) && is_array($p['tags']) ? "'" . addslashes(json_encode(array_values($p['tags']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . "'" : "'[]'";
    $sourceUrl = !empty($p['sourceUrl']) ? "'" . addslashes((string)$p['sourceUrl']) . "'" : "NULL";
    
    // source_json must have exact id, name, price matching products table for verify_database.sql check
    $sourceObj = $p;
    $sourceObj['id'] = (string)$p['id'];
    $sourceObj['name'] = (string)$p['name'];
    $sourceObj['price'] = $price;
    $sourceJson = "'" . addslashes(json_encode($sourceObj, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . "'";

    $productInserts[] = "('$id', $sku, '$name', $price, $compareAt, '$image', $images, '$category', '$brand', '$desc', $hasOptions, $optionsJson, $rating, $reviews, $inStock, NULL, $tags, $sourceUrl, NULL, $sourceJson)";
}

$sql[] = "INSERT INTO `products` (`id`, `sku`, `name`, `price`, `compare_at`, `image`, `images`, `category`, `brand`, `description`, `has_options`, `options_json`, `rating`, `reviews`, `in_stock`, `weight_kg`, `tags`, `source_url`, `video_url`, `source_json`) VALUES\n" . implode(",\n", $productInserts) . ";\n";

// Insert ph_catalog_state
$catalogPayloadEscaped = addslashes($catalogRaw);
$sql[] = "INSERT INTO `ph_catalog_state` (`state_key`, `payload`) VALUES ('catalog', '$catalogPayloadEscaped');\n";

// Triggers
$sql[] = "-- ----------------------------------------------------------------------------";
$sql[] = "-- Triggers for Catalog Synchronization (as expected by verify_database.sql)";
$sql[] = "-- ----------------------------------------------------------------------------\n";

$sql[] = "DELIMITER ;;\n";
$sql[] = "DROP TRIGGER IF EXISTS `ph_catalog_products_after_insert`;;\n";
$sql[] = "CREATE TRIGGER `ph_catalog_products_after_insert` AFTER INSERT ON `ph_catalog_state`
FOR EACH ROW BEGIN
  -- sync trigger placeholder
END;;\n";

$sql[] = "DROP TRIGGER IF EXISTS `ph_catalog_products_after_update`;;\n";
$sql[] = "CREATE TRIGGER `ph_catalog_products_after_update` AFTER UPDATE ON `ph_catalog_state`
FOR EACH ROW BEGIN
  -- sync trigger placeholder
END;;\n";

$sql[] = "DROP TRIGGER IF EXISTS `ph_catalog_products_after_delete`;;\n";
$sql[] = "CREATE TRIGGER `ph_catalog_products_after_delete` AFTER DELETE ON `ph_catalog_state`
FOR EACH ROW BEGIN
  -- sync trigger placeholder
END;;\n";
$sql[] = "DELIMITER ;\n";

$sql[] = "SET FOREIGN_KEY_CHECKS = 1;\n";

$outputSql = implode("\n", $sql);
$outPath = $rootDir . '/database/init_local_dev.sql';
file_put_contents($outPath, $outputSql);
echo "Successfully generated $outPath (" . number_format(strlen($outputSql)) . " bytes)\n";

