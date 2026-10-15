-- ==========================================================================
-- HSN SRHAN Portfolio & Dashboard Database Schema (Hostinger MySQL Export)
-- Primary Brand Navy Accent: #0B132B
-- ==========================================================================

SET FOREIGN_KEY_CHECKS=0;

-- 1. Users Table (Admin & SEO Specialist Roles)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'seo') NOT NULL DEFAULT 'seo',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`) VALUES
(1, 'admin', 'hsn96srhan@gmail.com', '$2y$10$wN1g4l9aP5b4/o8fO7qS5.X1w64D9T0l4V0X0Z0Y0W0X0Z0Y0W0X0', 'admin'),
(2, 'seouser', 'seo@hsn.agency', '$2y$10$wN1g4l9aP5b4/o8fO7qS5.X1w64D9T0l4V0X0Z0Y0W0X0Z0Y0W0X0', 'seo')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 2. Site Settings & Script Injection Engine
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT PRIMARY KEY DEFAULT 1,
  `site_title_ar` VARCHAR(255) DEFAULT 'حسن سرحان | مدير تسويق',
  `site_title_en` VARCHAR(255) DEFAULT 'HASSAN SARHAN | Marketing Director',
  `script_header` TEXT,
  `script_body` TEXT,
  `emergency_phone` VARCHAR(50) DEFAULT '+966506267542',
  `emergency_whatsapp` VARCHAR(50) DEFAULT '+966506267542',
  `courses_page_enabled` TINYINT(1) DEFAULT 1,
  `max_daily_bookings` INT DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`id`, `site_title_ar`, `site_title_en`, `emergency_phone`, `emergency_whatsapp`, `courses_page_enabled`, `max_daily_bookings`) 
VALUES (1, 'حسن سرحان | مدير تسويق', 'HASSAN SARHAN | Marketing Director', '+966506267542', '+966506267542', 1, 3)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 3. Projects & WOW Case Studies (13 Cases)
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `client_name` VARCHAR(150) NOT NULL,
  `client_logo_url` VARCHAR(255) DEFAULT NULL,
  `sector` ENUM('medical', 'e-commerce', 'b2b_logistics') NOT NULL DEFAULT 'e-commerce',
  `problem_text_ar` TEXT NOT NULL,
  `strategy_text_ar` TEXT NOT NULL,
  `results_json` JSON NOT NULL,
  `priority` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. YouTube Shorts Highlights
CREATE TABLE IF NOT EXISTS `youtube_shorts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `youtube_video_id` VARCHAR(100) NOT NULL,
  `views_count` VARCHAR(50) DEFAULT '100K+',
  `likes_count` VARCHAR(50) DEFAULT '5K',
  `priority` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Articles & Blog
CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content_ar` LONGTEXT NOT NULL,
  `linkedin_url` VARCHAR(255) DEFAULT NULL,
  `priority` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Dynamic Calendar Bookings
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `company_name` VARCHAR(150) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `booking_date` DATE NOT NULL,
  `time_slot` VARCHAR(50) DEFAULT '10:00 AM',
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Digital Products & Playbooks
CREATE TABLE IF NOT EXISTS `digital_products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) DEFAULT NULL,
  `badge_ar` VARCHAR(100) DEFAULT NULL,
  `category` VARCHAR(50) DEFAULT 'playbook',
  `price_sar` DECIMAL(10,2) NOT NULL DEFAULT 199.00,
  `original_price_sar` DECIMAL(10,2) DEFAULT NULL,
  `download_url` VARCHAR(255) DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `description_ar` TEXT DEFAULT NULL,
  `features_json` JSON DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. D360 Bank Receipt Orders
CREATE TABLE IF NOT EXISTS `bank_orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(50) NOT NULL,
  `product_title` VARCHAR(255) NOT NULL,
  `amount_sar` DECIMAL(10,2) NOT NULL,
  `receipt_url` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Landing Pages Builder Stack (10 Sections)
CREATE TABLE IF NOT EXISTS `landing_pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title_ar` VARCHAR(255) NOT NULL,
  `title_en` VARCHAR(255) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `meta_description_ar` TEXT DEFAULT NULL,
  `blocks_json` JSON NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
