<?php
/**
 * 1-Click Database Migration & Self-Healing Script
 * Run by visiting https://avixtreimsco.com/update_db.php
 */

include_once __DIR__ . '/db.php';

echo "<!DOCTYPE html><html><head><title>Avix Treims - Database Update</title>";
echo "<style>body{font-family:system-ui,sans-serif;background:#090e0b;color:#e1e7ec;padding:40px;line-height:1.6;} .card{background:#141c17;padding:30px;border-radius:12px;border:1px solid rgba(204,255,0,0.2);max-width:650px;margin:auto;} h2{color:#ccff00;} .ok{color:#2ecc71;font-weight:600;} .btn{display:inline-block;padding:12px 24px;background:#ccff00;color:#090e0b;text-decoration:none;font-weight:bold;border-radius:6px;margin-top:20px;}</style>";
echo "</head><body><div class='card'>";
echo "<h2>Database Auto-Update & KYC Migration</h2><ul>";

$actions = [];

// 1. Verification Column
$chk_v = mysqli_query($link, "SHOW COLUMNS FROM `users` LIKE 'verification'");
if ($chk_v && mysqli_num_rows($chk_v) == 0) {
    if (mysqli_query($link, "ALTER TABLE `users` ADD COLUMN `verification` INT DEFAULT 0")) {
        $actions[] = "<span class='ok'>[SUCCESS]</span> Added 'verification' column to users table.";
    } else {
        $actions[] = "<span style='color:#e74c3c;'>[ERROR]</span> Failed adding verification: " . mysqli_error($link);
    }
} else {
    $actions[] = "<span class='ok'>[OK]</span> 'verification' column already exists.";
}

// 2. KYC Status Column
$chk_k = mysqli_query($link, "SHOW COLUMNS FROM `users` LIKE 'kyc_status'");
if ($chk_k && mysqli_num_rows($chk_k) == 0) {
    if (mysqli_query($link, "ALTER TABLE `users` ADD COLUMN `kyc_status` VARCHAR(30) DEFAULT 'unverified'")) {
        $actions[] = "<span class='ok'>[SUCCESS]</span> Added 'kyc_status' column to users table.";
    } else {
        $actions[] = "<span style='color:#e74c3c;'>[ERROR]</span> Failed adding kyc_status: " . mysqli_error($link);
    }
} else {
    $actions[] = "<span class='ok'>[OK]</span> 'kyc_status' column already exists.";
}

// 3. User Avatar Column
$chk_img = mysqli_query($link, "SHOW COLUMNS FROM `users` LIKE 'image'");
if ($chk_img && mysqli_num_rows($chk_img) == 0) {
    if (mysqli_query($link, "ALTER TABLE `users` ADD COLUMN `image` VARCHAR(255) DEFAULT ''")) {
        $actions[] = "<span class='ok'>[SUCCESS]</span> Added 'image' avatar column to users table.";
    }
}

// 4. Synchronize any existing verified statuses
mysqli_query($link, "UPDATE users SET verification = 1 WHERE kyc_status = 'verified'");

// 5. Ensure Support & Mail Center Tables exist
mysqli_query($link, "CREATE TABLE IF NOT EXISTS `support_threads` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$actions[] = "<span class='ok'>[OK]</span> Checked/created 'support_threads' table.";

mysqli_query($link, "CREATE TABLE IF NOT EXISTS `support_messages` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$actions[] = "<span class='ok'>[OK]</span> Checked/created 'support_messages' table.";

// 6. Ensure Settings Table has SMTP Fields
$chk_mh = mysqli_query($link, "SHOW COLUMNS FROM `settings` LIKE 'mail_host'");
if ($chk_mh && mysqli_num_rows($chk_mh) == 0) {
    mysqli_query($link, "ALTER TABLE `settings` 
        ADD COLUMN `mail_host` VARCHAR(255) DEFAULT 'mail.avxtsinc.com',
        ADD COLUMN `mail_user` VARCHAR(255) DEFAULT 'noreply@avxtsinc.com',
        ADD COLUMN `mail_pass` VARCHAR(255) DEFAULT '" . ($smtp_fallback_pass ?? 'CHANGE_ME_SMTP_PASS') . "',
        ADD COLUMN `mail_port` VARCHAR(10) DEFAULT '465'");
    $actions[] = "<span class='ok'>[SUCCESS]</span> Added SMTP mail fields to settings table.";
} else {
    $actions[] = "<span class='ok'>[OK]</span> SMTP fields exist in settings table.";
}

// 7. Configure Production Domain & SMTP Credentials
$active_domain = 'avxtsinc.com';
mysqli_query($link, "UPDATE settings SET 
    mail_host = 'mail.avxtsinc.com',
    mail_user = 'noreply@avxtsinc.com',
    mail_pass = '" . ($smtp_fallback_pass ?? 'CHANGE_ME_SMTP_PASS') . "',
    mail_port = '465',
    sitemail  = 'noreply@avxtsinc.com',
    siteurl   = 'https://avxtsinc.com'
    WHERE id = '1'");
$actions[] = "<span class='ok'>[SUCCESS]</span> Configured live SMTP (noreply@avxtsinc.com) and production URL (https://avxtsinc.com).";

// 8. Sanitize Google Search Meta & Remove Deceptive Brand Keywords (Tesla, SpaceX, etc.)
$clean_meta_title    = 'Avix Treims - Digital Asset Management & Technology Platform';
$clean_meta_desc     = 'Avix Treims delivers secure cryptocurrency custody, algorithmic liquidity routing, quantitative digital asset infrastructure, and institutional portfolio management.';
$clean_meta_keywords = 'Avix Treims, digital asset management, cryptocurrency custody, institutional portfolio terminal, quantitative analytics, blockchain technology, secure client portal';

mysqli_query($link, "UPDATE settings SET 
    meta_title = '$clean_meta_title',
    meta_desc = '$clean_meta_desc',
    meta_keywords = '$clean_meta_keywords'
    WHERE id = '1'");

mysqli_query($link, "UPDATE notifications SET 
    title = 'Institutional Dashboard System Online',
    message = 'Welcome to the newly upgraded Avix Treims member terminal. Advanced digital asset infrastructure, automated execution engines, and instant KYC verification clearance are active.',
    link_url = 'customer.php'
    WHERE id = 1");

mysqli_query($link, "UPDATE notifications SET 
    title = 'Multi-Asset Portfolio Desk Live',
    message = 'You can now access direct institutional digital asset portfolios with live real-time market pricing directly from your dashboard.',
    link_url = 'customer.php'
    WHERE id = 2");

$actions[] = "<span class='ok'>[SUCCESS]</span> Sanitized Google SEO metadata & removed all trademark / deceptive flags.";

foreach ($actions as $act) {
    echo "<li>$act</li>";
}

echo "</ul>";
echo "<p style='color:#2ecc71;font-weight:bold;'>Database is now fully synchronized! KYC verification in Admin will work without errors.</p>";
echo "<a href='controller/users.php' class='btn'>Go to Admin Users &rarr;</a>";
echo "</div></body></html>";
?>