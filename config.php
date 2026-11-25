<?php 
if (file_exists(__DIR__ . '/config.local.php')) {
    include_once __DIR__ . '/config.local.php';
}
$smtp_fallback_pass = $smtp_fallback_pass ?? (getenv('SMTP_PASS') ?: 'CHANGE_ME_SMTP_PASS');

if (!isset($link) || $link === false) {
    if (file_exists(__DIR__ . '/db.php')) {
        include_once __DIR__ . '/db.php';
    } elseif (file_exists(__DIR__ . '/../db.php')) {
        include_once __DIR__ . '/../db.php';
    }
}

// Function to safely add missing column to a table
if (!function_exists('ensure_column_exists')) {
    function ensure_column_exists($link, $table, $column, $definition) {
        if (!$link) return false;
        $check = @mysqli_query($link, "SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check) {
            if (mysqli_num_rows($check) == 0) {
                @mysqli_query($link, "ALTER TABLE `$table` ADD `$column` $definition");
            }
        } else {
            @mysqli_query($link, "ALTER TABLE `$table` ADD `$column` $definition");
        }
    }
}

// Function to safely create missing table
if (!function_exists('ensure_table_exists')) {
    function ensure_table_exists($link, $table, $schema) {
        if (!$link) return false;
        @mysqli_query($link, "CREATE TABLE IF NOT EXISTS `$table` ($schema) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
}

if (isset($link) && $link) {
    // Ensure all dynamic columns exist in settings
    ensure_column_exists($link, 'settings', 'address', "VARCHAR(255) DEFAULT 'Flatiron District, New York, NY 10010'");
    ensure_column_exists($link, 'settings', 'phone', "VARCHAR(255) DEFAULT '+1 (555) 234-5678'");
    ensure_column_exists($link, 'settings', 'logo', "VARCHAR(255) DEFAULT 'images/logo.png'");
    ensure_column_exists($link, 'settings', 'theme_color', "VARCHAR(30) DEFAULT '#ccff00'");
    ensure_column_exists($link, 'settings', 'bg_color', "VARCHAR(30) DEFAULT '#0b0f0d'");
    ensure_column_exists($link, 'settings', 'walt_eth', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'settings', 'walt_usdt', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'settings', 'qr_btc', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'settings', 'qr_eth', "VARCHAR(255) DEFAULT ''");
    $current_host = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']) : 'avxtsinc.com';
    $default_mail_host = 'mail.' . $current_host;
    $default_mail_user = 'noreply@' . $current_host;

    ensure_column_exists($link, 'settings', 'qr_usdt', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'settings', 'mail_host', "VARCHAR(255) DEFAULT '$default_mail_host'");
    ensure_column_exists($link, 'settings', 'mail_user', "VARCHAR(255) DEFAULT '$default_mail_user'");
    ensure_column_exists($link, 'settings', 'mail_pass', "VARCHAR(255) DEFAULT '$smtp_fallback_pass'");
    ensure_column_exists($link, 'settings', 'mail_port', "VARCHAR(10) DEFAULT '465'");

    // Auto-populate or correct default SMTP credentials
    @mysqli_query($link, "UPDATE settings SET 
        mail_host = IF(mail_host IS NULL OR mail_host = '' OR mail_host = 'mail.avixtreims.com' OR mail_host = 'mail.avixtreimsco.com', '$default_mail_host', mail_host),
        mail_user = IF(mail_user IS NULL OR mail_user = '' OR mail_user = 'noreply@avixtreims.com' OR mail_user = 'noreply@avixtreimsco.com', '$default_mail_user', mail_user),
        mail_pass = IF(mail_pass IS NULL OR mail_pass = '', '$smtp_fallback_pass', mail_pass),
        mail_port = IF(mail_port IS NULL OR mail_port = '', '465', mail_port),
        sitemail  = IF(sitemail IS NULL OR sitemail = '' OR sitemail = 'info@avixtreims.com' OR sitemail = 'noreply@avixtreims.com' OR sitemail = 'noreply@avixtreimsco.com', '$default_mail_user', sitemail),
        siteurl   = IF(siteurl IS NULL OR siteurl = '' OR siteurl = 'https://avixtreims.com' OR siteurl = 'https://avixtreimsco.com', 'https://$current_host', siteurl)
        WHERE id = '1'");

    // Safe, Compliant SEO Metadata Defaults (Free of trademark flags / deceptive keywords)
    $clean_meta_title    = 'Avix Treims - Digital Asset Management & Technology Platform';
    $clean_meta_desc     = 'Avix Treims delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.';
    $clean_meta_keywords = 'Avix Treims, digital asset management, cryptocurrency custody, institutional portfolio terminal, quantitative analytics, blockchain technology, secure client portal';

    // Enhanced SEO & Search Visibility Columns
    ensure_column_exists($link, 'settings', 'meta_title', "VARCHAR(255) DEFAULT '$clean_meta_title'");
    ensure_column_exists($link, 'settings', 'meta_desc', "TEXT DEFAULT NULL");
    ensure_column_exists($link, 'settings', 'meta_keywords', "TEXT DEFAULT NULL");
    ensure_column_exists($link, 'settings', 'meta_author', "VARCHAR(255) DEFAULT 'Avix Treims'");
    ensure_column_exists($link, 'settings', 'og_image', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'settings', 'google_analytics', "TEXT DEFAULT NULL");

    // Auto-sanitize existing database settings row to remove any flagged deceptive/trademark terms
    @mysqli_query($link, "UPDATE settings SET 
        meta_title = '$clean_meta_title',
        meta_desc = '$clean_meta_desc',
        meta_keywords = '$clean_meta_keywords'
        WHERE id = '1' AND (meta_title LIKE '%Tesla%' OR meta_desc LIKE '%Tesla%' OR meta_keywords LIKE '%Tesla%' OR meta_title = '' OR meta_title IS NULL)");

    // Ensure User Display Picture / Avatar column exists
    ensure_column_exists($link, 'users', 'image', "VARCHAR(255) DEFAULT ''");

    // Also ensure deposits table has crypto, txn_hash, and shares_order_id columns
    ensure_column_exists($link, 'deposits', 'crypto', "VARCHAR(50) DEFAULT 'BTC'");
    ensure_column_exists($link, 'deposits', 'txn_hash', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'deposits', 'shares_order_id', "INT DEFAULT 0");
    ensure_column_exists($link, 'deposits', 'proof_image', "VARCHAR(255) DEFAULT ''");

    // Ensure users table has kyc_status and verification columns
    ensure_column_exists($link, 'users', 'verification', "INT DEFAULT 0");
    ensure_column_exists($link, 'users', 'kyc_status', "VARCHAR(30) DEFAULT 'unverified'");

    // Ensure KYC Applications Table exists
    ensure_table_exists($link, 'kyc_applications', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `username` VARCHAR(191) NOT NULL,
        `email` VARCHAR(191) NOT NULL,
        `document_type` VARCHAR(50) NOT NULL,
        `document_number` VARCHAR(100) NOT NULL,
        `front_image` VARCHAR(255) NOT NULL,
        `back_image` VARCHAR(255) DEFAULT '',
        `selfie_image` VARCHAR(255) DEFAULT '',
        `country` VARCHAR(100) DEFAULT '',
        `dob` VARCHAR(50) DEFAULT '',
        `status` VARCHAR(30) DEFAULT 'PENDING',
        `admin_note` TEXT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_user_email` (`email`),
        KEY `idx_status` (`status`)
    ");

    // Ensure Stock & Equity Shares Orders Table exists (Technology, Silicon, Aerospace Indices)
    ensure_table_exists($link, 'shares_orders', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `order_code` VARCHAR(50) NOT NULL UNIQUE,
        `user_id` INT NOT NULL,
        `username` VARCHAR(191) NOT NULL,
        `user_email` VARCHAR(191) NOT NULL,
        `symbol` VARCHAR(30) NOT NULL,
        `asset_name` VARCHAR(100) NOT NULL,
        `shares_qty` DECIMAL(18, 4) NOT NULL,
        `purchase_price` DECIMAL(18, 2) NOT NULL,
        `total_amount` DECIMAL(18, 2) NOT NULL,
        `current_price` DECIMAL(18, 2) NOT NULL,
        `current_value` DECIMAL(18, 2) NOT NULL,
        `profit_loss` DECIMAL(18, 2) DEFAULT 0.00,
        `profit_loss_pct` DECIMAL(10, 2) DEFAULT 0.00,
        `status` VARCHAR(30) DEFAULT 'Pending',
        `payment_status` VARCHAR(30) DEFAULT 'Pending Payment',
        `deposit_id` INT DEFAULT 0,
        `admin_note` TEXT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_user_email` (`user_email`),
        KEY `idx_symbol` (`symbol`),
        KEY `idx_status` (`status`)
    ");

    // Ensure Bidirectional Support & Mail Center Tables exist
    ensure_table_exists($link, 'support_threads', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `ticket_code` VARCHAR(50) NOT NULL UNIQUE,
        `reply_token` VARCHAR(64) NOT NULL UNIQUE,
        `user_id` INT DEFAULT 0,
        `user_email` VARCHAR(191) NOT NULL,
        `user_name` VARCHAR(191) DEFAULT '',
        `subject` VARCHAR(255) NOT NULL,
        `is_broadcast` TINYINT(1) DEFAULT 0,
        `status` VARCHAR(30) DEFAULT 'admin_replied',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_user_email` (`user_email`),
        KEY `idx_ticket` (`ticket_code`),
        KEY `idx_status` (`status`)
    ");

    ensure_table_exists($link, 'support_messages', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `thread_id` INT NOT NULL,
        `sender_type` VARCHAR(20) NOT NULL,
        `sender_name` VARCHAR(191) DEFAULT '',
        `sender_email` VARCHAR(191) DEFAULT '',
        `message` LONGTEXT NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY `idx_thread_id` (`thread_id`),
        KEY `idx_is_read` (`is_read`)
    ");

    // Ensure Password Resets Table exists for Forgot Password functionality
    ensure_table_exists($link, 'password_resets', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `username` VARCHAR(191) NOT NULL,
        `email` VARCHAR(191) NOT NULL,
        `token` VARCHAR(191) NOT NULL,
        `otp_code` VARCHAR(10) NOT NULL,
        `expires_at` DATETIME NOT NULL,
        `used` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY `idx_token` (`token`),
        KEY `idx_email` (`email`),
        KEY `idx_otp` (`otp_code`)
    ");

    // Ensure In-App Notifications & Notification Reads Tables exist
    ensure_table_exists($link, 'notifications', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `target_type` VARCHAR(20) DEFAULT 'all',
        `target_email` VARCHAR(191) DEFAULT '',
        `target_user_id` INT DEFAULT 0,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `type` VARCHAR(50) DEFAULT 'info',
        `link_url` VARCHAR(255) DEFAULT '',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY `idx_target` (`target_type`, `target_email`),
        KEY `idx_created` (`created_at`)
    ");

    ensure_table_exists($link, 'notification_reads', "
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `notification_id` INT NOT NULL,
        `user_email` VARCHAR(191) NOT NULL,
        `read_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `idx_user_notif` (`user_email`, `notification_id`),
        KEY `idx_user` (`user_email`)
    ");

    // Seed initial broadcast notification if empty
    $q_chk_notif = @mysqli_query($link, "SELECT COUNT(*) as c FROM notifications");
    if ($q_chk_notif && $r_notif = mysqli_fetch_assoc($q_chk_notif)) {
        if ((int)$r_notif['c'] === 0) {
            mysqli_query($link, "INSERT INTO notifications (target_type, title, message, type, link_url) VALUES 
                ('all', 'Institutional Dashboard System Online', 'Welcome to the newly upgraded Avix Treims member terminal. Advanced digital asset infrastructure, automated execution engines, and instant KYC verification clearance are active.', 'success', 'customer.php'),
                ('all', 'Multi-Asset Portfolio Desk Live', 'You can now access direct institutional digital asset portfolios with live real-time market pricing directly from your dashboard.', 'market', 'customer.php')");
        }
    }

    // Load Mail Helper if present
    if (file_exists(__DIR__ . '/mail-helper.php')) {
        require_once __DIR__ . '/mail-helper.php';
    } elseif (file_exists(__DIR__ . '/../mail-helper.php')) {
        require_once __DIR__ . '/../mail-helper.php';
    }

    $select = mysqli_query($link, "SELECT * FROM settings WHERE id = '1'");
    if ($select && mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);

        $detected_host = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']) : 'avxtsinc.com';
        $detected_url  = (!empty($_SERVER['HTTP_HOST']) ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']) : 'https://' . $detected_host);
        $sitename      = !empty($row['sitename']) ? $row['sitename'] : 'Avix Treims';
        $sitemail      = (!empty($row['sitemail']) && strpos($row['sitemail'], 'avixtreims.com') === false) ? $row['sitemail'] : ('noreply@' . $detected_host);
        $siteurl       = (!empty($row['siteurl']) && $row['siteurl'] !== 'https://avixtreims.com') ? rtrim($row['siteurl'], '/') : $detected_url;
        $mail_host     = (!empty($row['mail_host']) && $row['mail_host'] !== 'mail.avixtreims.com') ? $row['mail_host'] : ('mail.' . $detected_host);
        $mail_user     = (!empty($row['mail_user']) && $row['mail_user'] !== 'noreply@avixtreims.com') ? $row['mail_user'] : ('noreply@' . $detected_host);
        $mail_pass     = !empty($row['mail_pass']) ? $row['mail_pass'] : $smtp_fallback_pass;
        $mail_port     = !empty($row['mail_port']) ? $row['mail_port'] : '465';
        $wallet      = !empty($row['walt']) ? $row['walt'] : 'bc1qzxhs0c73r4tmphcrmwjtwjvdxcc9eapcuqn63e';
        $walt_eth    = !empty($row['walt_eth']) ? $row['walt_eth'] : '0x71C83d3129486c9A74cfdD522aAf5E0C2B9f5fE0';
        $walt_usdt   = !empty($row['walt_usdt']) ? $row['walt_usdt'] : 'TXh8Z3z79Vp7f8ZpL2hY4eR3nK9wM1qXyT';
        $qr_btc      = !empty($row['qr_btc']) ? $row['qr_btc'] : '';
        $qr_eth      = !empty($row['qr_eth']) ? $row['qr_eth'] : '';
        $qr_usdt     = !empty($row['qr_usdt']) ? $row['qr_usdt'] : '';
        $ref_bonus   = !empty($row['ref_bonus']) ? $row['ref_bonus'] : '10';
        $whatsapp    = !empty($row['whatsapp']) ? $row['whatsapp'] : '+1 (555) 234-5678';
        $siteaddress = !empty($row['address']) ? $row['address'] : 'Flatiron District, New York, NY 10010';
        $phone       = !empty($row['phone']) ? $row['phone'] : $whatsapp;
        $logo        = !empty($row['logo']) ? $row['logo'] : 'images/logo.png';
        $theme_color = !empty($row['theme_color']) ? $row['theme_color'] : '#ccff00';
        $bg_color    = !empty($row['bg_color']) ? $row['bg_color'] : '#0b0f0d';

        // Dynamic SEO Fields (Sanitized against trademark / deceptive keywords)
        $meta_title       = (!empty($row['meta_title']) && stripos($row['meta_title'], 'tesla') === false) ? $row['meta_title'] : ($sitename . ' - Digital Asset Management & Technology Platform');
        $meta_desc        = (!empty($row['meta_desc']) && stripos($row['meta_desc'], 'tesla') === false) ? $row['meta_desc'] : ($sitename . ' delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.');
        $meta_keywords    = (!empty($row['meta_keywords']) && stripos($row['meta_keywords'], 'tesla') === false) ? $row['meta_keywords'] : ($sitename . ', ' . $sitename . ' platform, ' . $sitename . ' login, digital asset custody, institutional portfolio management, quantitative analytics, blockchain technology, secure client portal');
        $meta_author      = !empty($row['meta_author']) ? $row['meta_author'] : $sitename;
        $og_image         = !empty($row['og_image']) ? $row['og_image'] : $logo;
        $google_analytics = !empty($row['google_analytics']) ? $row['google_analytics'] : '';
    } else {
        $detected_host = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']) : 'avxtsinc.com';
        $detected_url  = 'https://' . $detected_host;
        $sitename      = 'Avix Treims';
        $sitemail      = 'noreply@' . $detected_host;
        $siteurl       = $detected_url;
        $mail_host     = 'mail.' . $detected_host;
        $mail_user     = 'noreply@' . $detected_host;
        $mail_pass     = $smtp_fallback_pass;
        $mail_port     = '465';
        $wallet      = 'bc1qzxhs0c73r4tmphcrmwjtwjvdxcc9eapcuqn63e';
        $walt_eth    = '0x71C83d3129486c9A74cfdD522aAf5E0C2B9f5fE0';
        $walt_usdt   = 'TXh8Z3z79Vp7f8ZpL2hY4eR3nK9wM1qXyT';
        $qr_btc      = '';
        $qr_eth      = '';
        $qr_usdt     = '';
        $ref_bonus   = '10';
        $whatsapp    = '+1 (555) 234-5678';
        $siteaddress = 'Flatiron District, New York, NY 10010';
        $phone       = '+1 (555) 234-5678';
        $logo        = 'images/logo.png';
        $theme_color = '#ccff00';
        $bg_color    = '#0b0f0d';

        $meta_title       = 'Avix Treims - Digital Asset Management & Technology Platform';
        $meta_desc        = 'Avix Treims delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.';
        $meta_keywords    = 'Avix Treims, digital asset management, cryptocurrency custody, institutional portfolio terminal, quantitative analytics, blockchain technology, secure client portal';
        $meta_author      = 'Avix Treims';
        $og_image         = 'images/logo.png';
        $google_analytics = '';
    }
} else {
    $detected_url = (!empty($_SERVER['HTTP_HOST']) ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']) : 'http://avixtreimsco.com');
    $sitename    = 'Avix Treims';
    $sitemail    = 'noreply@avixtreims.com';
    $siteurl     = $detected_url;
    $mail_host   = 'mail.avixtreims.com';
    $mail_user   = 'noreply@avixtreims.com';
    $mail_pass   = $smtp_fallback_pass;
    $mail_port   = '465';
    $wallet      = 'bc1qzxhs0c73r4tmphcrmwjtwjvdxcc9eapcuqn63e';
    $walt_eth    = '0x71C83d3129486c9A74cfdD522aAf5E0C2B9f5fE0';
    $walt_usdt   = 'TXh8Z3z79Vp7f8ZpL2hY4eR3nK9wM1qXyT';
    $ref_bonus   = '10';
    $whatsapp    = '+1 (555) 234-5678';
    $siteaddress = 'Flatiron District, New York, NY 10010';
    $phone       = '+1 (555) 234-5678';
    $logo        = 'images/logo.png';
    $theme_color = '#ccff00';
    $bg_color    = '#0b0f0d';

    $meta_title       = 'Avix Treims - Digital Asset Management & Technology Platform';
    $meta_desc        = 'Avix Treims delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.';
    $meta_keywords    = 'Avix Treims, digital asset management, cryptocurrency custody, institutional portfolio terminal, quantitative analytics, blockchain technology, secure client portal';
    $meta_author      = 'Avix Treims';
    $og_image         = 'images/logo.png';
    $google_analytics = '';
}

// Global Dynamic SEO Header Generator
if (!function_exists('render_site_seo')) {
    function render_site_seo($custom_title = null, $custom_desc = null, $custom_keywords = null) {
        global $sitename, $siteurl, $meta_title, $meta_desc, $meta_keywords, $meta_author, $og_image, $google_analytics;

        $t = !empty($custom_title) ? $custom_title : (!empty($meta_title) ? $meta_title : ($sitename . ' - Digital Asset Management & Technology Platform'));
        $d = !empty($custom_desc) ? $custom_desc : (!empty($meta_desc) ? $meta_desc : ($sitename . ' delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.'));
        $k = !empty($custom_keywords) ? $custom_keywords : (!empty($meta_keywords) ? $meta_keywords : ($sitename . ', ' . $sitename . ' login, ' . $sitename . ' platform, digital asset custody, institutional portfolio management, quantitative analytics, blockchain technology'));
        $img = !empty($og_image) ? (strpos($og_image, 'http') === 0 ? $og_image : rtrim($siteurl, '/') . '/' . ltrim($og_image, '/')) : (rtrim($siteurl, '/') . '/images/logo.png');

        $html = "\n    <!-- Dynamic High-Impact Search Engine Optimization (SEO) -->\n";
        $html .= '    <title>' . htmlspecialchars($t) . "</title>\n";
        $html .= '    <meta name="description" content="' . htmlspecialchars($d) . "\">\n";
        $html .= '    <meta name="keywords" content="' . htmlspecialchars($k) . "\">\n";
        $html .= '    <meta name="author" content="' . htmlspecialchars($meta_author ?? $sitename) . "\">\n";
        $html .= "    <meta name=\"robots\" content=\"index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1\">\n";
        $html .= '    <link rel="canonical" href="' . htmlspecialchars($siteurl) . "\">\n";
        $html .= "\n    <!-- Open Graph (Facebook, WhatsApp, LinkedIn, Telegram) -->\n";
        $html .= "    <meta property=\"og:locale\" content=\"en_US\">\n";
        $html .= "    <meta property=\"og:type\" content=\"website\">\n";
        $html .= '    <meta property=\"og:title\" content="' . htmlspecialchars($t) . "\">\n";
        $html .= '    <meta property=\"og:description\" content="' . htmlspecialchars($d) . "\">\n";
        $html .= '    <meta property=\"og:url\" content="' . htmlspecialchars($siteurl) . "\">\n";
        $html .= '    <meta property=\"og:site_name\" content="' . htmlspecialchars($sitename) . "\">\n";
        $html .= '    <meta property=\"og:image\" content="' . htmlspecialchars($img) . "\">\n";
        $html .= "    <meta property=\"og:image:width\" content=\"1200\">\n";
        $html .= "    <meta property=\"og:image:height\" content=\"630\">\n";
        $html .= "\n    <!-- Twitter Card -->\n";
        $html .= "    <meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $html .= '    <meta name=\"twitter:title\" content="' . htmlspecialchars($t) . "\">\n";
        $html .= '    <meta name=\"twitter:description\" content="' . htmlspecialchars($d) . "\">\n";
        $html .= '    <meta name=\"twitter:image\" content="' . htmlspecialchars($img) . "\">\n";
        $html .= "\n    <!-- JSON-LD Structured Data Schema (Google Search Rich Results) -->\n";
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FinancialService',
            'name' => $sitename,
            'url' => $siteurl,
            'logo' => $img,
            'description' => $d,
            'serviceType' => ['Digital Asset Custody', 'Quantitative Market Analysis', 'Institutional Portfolio Infrastructure', 'Algorithmic Liquidity Routing'],
            'currenciesAccepted' => 'USD, BTC, ETH, USDT'
        ];
        $html .= '    <script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";

        if (!empty($google_analytics)) {
            $html .= "\n    <!-- Google Site Verification / Analytics -->\n    " . $google_analytics . "\n";
        }

        return $html;
    }
}

// Notification Helper Functions
if (!function_exists('get_user_notifications')) {
    function get_user_notifications($link, $email, $limit = 10) {
        $e = mysqli_real_escape_string($link, $email);
        $lim = intval($limit);
        $sql = "SELECT n.*, 
                    CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END AS is_read
                FROM notifications n
                LEFT JOIN notification_reads nr 
                    ON n.id = nr.notification_id AND nr.user_email = '$e'
                WHERE (n.target_type = 'all' OR n.target_email = '$e')
                ORDER BY n.id DESC";
        if ($lim > 0) {
            $sql .= " LIMIT $lim";
        }
        $res = mysqli_query($link, $sql);
        $items = [];
        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $items[] = $row;
            }
        }
        return $items;
    }
}

if (!function_exists('get_user_unread_notif_count')) {
    function get_user_unread_notif_count($link, $email) {
        $e = mysqli_real_escape_string($link, $email);
        $sql = "SELECT COUNT(*) as c
                FROM notifications n
                LEFT JOIN notification_reads nr 
                    ON n.id = nr.notification_id AND nr.user_email = '$e'
                WHERE (n.target_type = 'all' OR n.target_email = '$e')
                  AND nr.id IS NULL";
        $res = mysqli_query($link, $sql);
        if ($res && $r = mysqli_fetch_assoc($res)) {
            return (int)$r['c'];
        }
        return 0;
    }
}

if (!function_exists('time_ago_short')) {
    function time_ago_short($datetime) {
        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        $diff = time() - $timestamp;
        if ($diff < 60) return "Just now";
        if ($diff < 3600) return floor($diff / 60) . "m ago";
        if ($diff < 86400) return floor($diff / 3600) . "h ago";
        if ($diff < 172800) return "Yesterday";
        if ($diff < 604800) return floor($diff / 86400) . "d ago";
        return date('M j', $timestamp);
    }
}
?>
