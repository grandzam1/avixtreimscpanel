-- phpMyAdmin SQL Dump / Avix Treims Production Database Setup
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+, and PHP 8.2 (cPanel / EA-PHP82)
-- Domain: avixtreims.com
-- Prepared for 1-Click phpMyAdmin Import

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(255) NOT NULL DEFAULT 'Avix Treims',
  `siteurl` varchar(255) NOT NULL DEFAULT 'https://avxtsinc.com',
  `sitemail` varchar(255) NOT NULL DEFAULT 'noreply@avxtsinc.com',
  `ref_bonus` varchar(255) NOT NULL DEFAULT '10',
  `walt` varchar(255) NOT NULL DEFAULT 'bc1qzxhs0c73r4tmphcrmwjtwjvdxcc9eapcuqn63e',
  `walt_eth` varchar(255) DEFAULT '0x71C83d3129486c9A74cfdD522aAf5E0C2B9f5fE0',
  `walt_usdt` varchar(255) DEFAULT 'TXh8Z3z79Vp7f8ZpL2hY4eR3nK9wM1qXyT',
  `qr_btc` varchar(255) DEFAULT '',
  `qr_eth` varchar(255) DEFAULT '',
  `qr_usdt` varchar(255) DEFAULT '',
  `whatsapp` varchar(255) NOT NULL DEFAULT '+1 (555) 234-5678',
  `phone` varchar(255) DEFAULT '+1 (555) 234-5678',
  `address` varchar(255) DEFAULT 'Flatiron District, New York, NY 10010',
  `logo` varchar(255) DEFAULT 'images/logo.png',
  `theme_color` varchar(30) DEFAULT '#ccff00',
  `bg_color` varchar(30) DEFAULT '#0b0f0d',
  `mail_host` varchar(255) DEFAULT 'mail.avxtsinc.com',
  `mail_user` varchar(255) DEFAULT 'noreply@avxtsinc.com',
  `mail_pass` varchar(255) DEFAULT 'CHANGE_ME_SMTP_PASS',
  `mail_port` varchar(10) DEFAULT '465',
  `meta_title` varchar(255) DEFAULT 'Avix Treims - Digital Asset Management & Technology Platform',
  `meta_desc` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_author` varchar(255) DEFAULT 'Avix Treims',
  `og_image` varchar(255) DEFAULT 'images/logo.png',
  `google_analytics` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`id`, `sitename`, `siteurl`, `sitemail`, `ref_bonus`, `walt`, `walt_eth`, `walt_usdt`, `qr_btc`, `qr_eth`, `qr_usdt`, `whatsapp`, `phone`, `address`, `logo`, `theme_color`, `bg_color`, `mail_host`, `mail_user`, `mail_pass`, `mail_port`, `meta_title`, `meta_desc`, `meta_keywords`, `meta_author`, `og_image`, `google_analytics`) VALUES
(1, 'Avix Treims', 'https://avxtsinc.com', 'noreply@avxtsinc.com', '10', 'bc1qzxhs0c73r4tmphcrmwjtwjvdxcc9eapcuqn63e', '0x71C83d3129486c9A74cfdD522aAf5E0C2B9f5fE0', 'TXh8Z3z79Vp7f8ZpL2hY4eR3nK9wM1qXyT', '', '', '', '+1 (555) 234-5678', '+1 (555) 234-5678', 'Flatiron District, New York, NY 10010', 'images/logo.png', '#ccff00', '#0b0f0d', 'mail.avxtsinc.com', 'noreply@avxtsinc.com', 'CHANGE_ME_SMTP_PASS', '465', 'Avix Treims - Digital Asset Management & Technology Platform', 'Avix Treims delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.', 'Avix Treims, digital asset management, cryptocurrency custody, institutional portfolio terminal, quantitative analytics, blockchain technology, secure client portal', 'Avix Treims', 'images/logo.png', '')
ON DUPLICATE KEY UPDATE 
  `sitename` = VALUES(`sitename`),
  `siteurl` = VALUES(`siteurl`),
  `sitemail` = VALUES(`sitemail`),
  `mail_host` = VALUES(`mail_host`),
  `mail_user` = VALUES(`mail_user`),
  `mail_pass` = VALUES(`mail_pass`),
  `mail_port` = VALUES(`mail_port`),
  `walt` = VALUES(`walt`),
  `walt_eth` = VALUES(`walt_eth`),
  `walt_usdt` = VALUES(`walt_usdt`),
  `whatsapp` = VALUES(`whatsapp`);

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `referral` varchar(255) DEFAULT NULL,
  `last_access` varchar(255) DEFAULT NULL,
  `dbalance` varchar(255) NOT NULL DEFAULT '0.00',
  `mbalance` varchar(255) NOT NULL DEFAULT '0.00',
  `refbalance` varchar(255) NOT NULL DEFAULT '0.00',
  `abonus` varchar(255) NOT NULL DEFAULT '0.00',
  `upgrade` int(11) DEFAULT 0,
  `verification` int(11) DEFAULT 0,
  `wpin` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT '',
  `kyc_status` varchar(30) DEFAULT 'unverified',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- --------------------------------------------------------
-- Table structure for table `deposits`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `crypto` varchar(50) DEFAULT 'BTC',
  `txn_hash` varchar(255) DEFAULT '',
  `shares_order_id` int(11) DEFAULT 0,
  `proof_image` varchar(255) DEFAULT '',
  `date` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_shares_order` (`shares_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- --------------------------------------------------------
-- Table structure for table `withdrawals`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `wallet` varchar(255) DEFAULT NULL,
  `method` varchar(255) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `acct_number` varchar(255) DEFAULT NULL,
  `acct_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'PENDING',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `kyc_applications`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `kyc_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `front_image` varchar(255) NOT NULL,
  `back_image` varchar(255) DEFAULT '',
  `selfie_image` varchar(255) DEFAULT '',
  `country` varchar(100) DEFAULT '',
  `dob` varchar(50) DEFAULT '',
  `status` varchar(30) DEFAULT 'PENDING',
  `admin_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `shares_orders`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `shares_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL UNIQUE,
  `user_id` int(11) NOT NULL,
  `username` varchar(191) NOT NULL,
  `user_email` varchar(191) NOT NULL,
  `symbol` varchar(30) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `shares_qty` decimal(18,4) NOT NULL,
  `purchase_price` decimal(18,2) NOT NULL,
  `total_amount` decimal(18,2) NOT NULL,
  `current_price` decimal(18,2) NOT NULL,
  `current_value` decimal(18,2) NOT NULL,
  `profit_loss` decimal(18,2) DEFAULT 0.00,
  `profit_loss_pct` decimal(10,2) DEFAULT 0.00,
  `status` varchar(30) DEFAULT 'Pending',
  `payment_status` varchar(30) DEFAULT 'Pending Payment',
  `deposit_id` int(11) DEFAULT 0,
  `admin_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_email` (`user_email`),
  KEY `idx_symbol` (`symbol`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `support_threads`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `support_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(50) NOT NULL UNIQUE,
  `reply_token` varchar(64) NOT NULL UNIQUE,
  `user_id` int(11) DEFAULT 0,
  `user_email` varchar(191) NOT NULL,
  `user_name` varchar(191) DEFAULT '',
  `subject` varchar(255) NOT NULL,
  `is_broadcast` tinyint(1) DEFAULT 0,
  `status` varchar(30) DEFAULT 'admin_replied',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_email` (`user_email`),
  KEY `idx_ticket` (`ticket_code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `support_messages`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `support_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `sender_type` varchar(20) NOT NULL,
  `sender_name` varchar(191) DEFAULT '',
  `sender_email` varchar(191) DEFAULT '',
  `message` longtext NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_thread_id` (`thread_id`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `password_resets`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token`),
  KEY `idx_email` (`email`),
  KEY `idx_otp` (`otp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `notifications`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_type` varchar(20) DEFAULT 'all',
  `target_email` varchar(191) DEFAULT '',
  `target_user_id` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `link_url` varchar(255) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_target` (`target_type`, `target_email`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `notifications` (`id`, `target_type`, `target_email`, `target_user_id`, `title`, `message`, `type`, `link_url`, `created_at`) VALUES
(1, 'all', '', 0, 'Institutional Dashboard System Online', 'Welcome to the newly upgraded Avix Treims member terminal. Advanced digital asset infrastructure, automated execution engines, and instant KYC verification clearance are active.', 'success', 'customer.php', NOW()),
(2, 'all', '', 0, 'Multi-Asset Portfolio Desk Live', 'You can now access direct institutional digital asset portfolios with live real-time market pricing directly from your dashboard.', 'market', 'customer.php', NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- --------------------------------------------------------
-- Table structure for table `notification_reads`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `notification_reads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `user_email` varchar(191) NOT NULL,
  `read_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_notif` (`user_email`, `notification_id`),
  KEY `idx_user` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
