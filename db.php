<?php
/**
 * Database Connection & Environment Configuration
 * Smart Auto-Detection: Live cPanel Production & Local Developer (MAMP/XAMPP)
 */

if (function_exists('mysqli_report')) {
    @mysqli_report(MYSQLI_REPORT_OFF);
}

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$db_connected = false;
$link = null;

// Real credentials live in config.local.php (gitignored) or env vars
if (file_exists(__DIR__ . '/config.local.php')) {
    include_once __DIR__ . '/config.local.php';
}

// Target production database credentials
$prod_user = $prod_user ?? (getenv('DB_USER') ?: 'YOUR_DB_USER');
$prod_pass = $prod_pass ?? (getenv('DB_PASS') ?: 'YOUR_DB_PASSWORD');
$prod_name = $prod_name ?? (getenv('DB_NAME') ?: 'YOUR_DB_NAME');

// Target database names in order of preference
$target_dbs = $target_dbs ?? ['YOUR_DB_NAME'];

// Configuration profiles to try
$profiles = [];

// 0. Local Laravel Herd (Windows) — MariaDB/MySQL on 127.0.0.1:3306
$is_herd_windows = PHP_OS_FAMILY === 'Windows'
    || (isset($_SERVER['DOCUMENT_ROOT']) && strpos(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/Herd/') !== false);
if ($is_herd_windows) {
    foreach ($target_dbs as $dbname) {
        $profiles[] = ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'name' => $dbname, 'port' => 3306];
    }
}

// 1. Production cPanel credentials (primary on live server)
foreach ($target_dbs as $cdb) {
    $profiles[] = ['host' => 'localhost', 'user' => $prod_user, 'pass' => $prod_pass, 'name' => $cdb, 'port' => 3306];
    $profiles[] = ['host' => '127.0.0.1', 'user' => $prod_user, 'pass' => $prod_pass, 'name' => $cdb, 'port' => 3306];
    if (file_exists('/var/lib/mysql/mysql.sock')) {
        $profiles[] = ['host' => 'localhost', 'user' => $prod_user, 'pass' => $prod_pass, 'name' => $cdb, 'port' => 0, 'socket' => '/var/lib/mysql/mysql.sock'];
    }
}

// 2. Local developer environment (macOS MAMP / XAMPP)
if (file_exists('/Applications/MAMP') || (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['DOCUMENT_ROOT'], 'Users') !== false)) {
    foreach ($target_dbs as $dbname) {
        $profiles[] = ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root', 'name' => $dbname, 'port' => 8889];
        if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
            $profiles[] = ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'name' => $dbname, 'port' => 8889, 'socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'];
        }
        $profiles[] = ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'name' => $dbname, 'port' => 3306];
    }
}

// Attempt connection through profiles
$active_cfg = null;
foreach ($profiles as $cfg) {
    try {
        $port = $cfg['port'] ?? 3306;
        $socket = $cfg['socket'] ?? null;
        if ($socket && file_exists($socket)) {
            $test_link = @mysqli_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $port, $socket);
        } else {
            $test_link = @mysqli_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $port);
        }
        if ($test_link && !mysqli_connect_errno()) {
            $link = $test_link;
            $active_cfg = $cfg;
            $db_connected = true;
            break;
        }
    } catch (\Throwable $e) {
        // try next profile
    }
}

if (!$db_connected || !$link) {
    die("Database Connection Error. Please verify your MySQL credentials in cPanel: " . mysqli_connect_error());
}

@define('DB_SERVER', $active_cfg['host']);
@define('DB_USERNAME', $active_cfg['user']);
@define('DB_PASSWORD', $active_cfg['pass']);
@define('DB_NAME', $active_cfg['name']);

// Enforce modern UTF-8 multi-byte encoding for crypto symbols and text
@mysqli_set_charset($link, "utf8mb4");
?>
