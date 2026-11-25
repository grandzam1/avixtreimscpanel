<?php
// Avix Treims - Database Connection Diagnostic Tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

if (file_exists(__DIR__ . '/config.local.php')) {
    include_once __DIR__ . '/config.local.php';
}
$db_user = $prod_user ?? (getenv('DB_USER') ?: 'YOUR_DB_USER');
$db_pass = $prod_pass ?? (getenv('DB_PASS') ?: 'YOUR_DB_PASSWORD');
$db_name = $prod_name ?? (getenv('DB_NAME') ?: 'YOUR_DB_NAME');

echo "<!DOCTYPE html><html><head><title>Database Diagnostic - Avix Treims</title>";
echo "<style>body{background:#0b0f0d; color:#fff; font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif; padding:30px;} table{border-collapse:collapse; width:100%; max-width:800px; margin:20px 0;} th,td{border:1px solid rgba(255,255,255,0.15); padding:12px; text-align:left;} th{background:#141c17;} .success{color:#ccff00; font-weight:bold;} .danger{color:#f43f5e;} .card{background:#141c17; border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:24px; max-width:800px;}</style></head><body>";

echo "<div class='card'>";
echo "<h2 style='color:#ccff00; margin-top:0;'>Avix Treims MySQL Connection Diagnostic</h2>";
echo "<p>Testing connection parameters for user: <code>$db_user</code> on database: <code>$db_name</code></p>";

$tests = [
    'Default localhost (PHP socket)' => ['host' => 'localhost', 'port' => null, 'socket' => null],
    '127.0.0.1 on port 3306' => ['host' => '127.0.0.1', 'port' => 3306, 'socket' => null],
    'localhost on port 3306' => ['host' => 'localhost', 'port' => 3306, 'socket' => null],
    'Unix Socket: /var/lib/mysql/mysql.sock' => ['host' => 'localhost', 'port' => 0, 'socket' => '/var/lib/mysql/mysql.sock'],
    'Unix Socket: /tmp/mysql.sock' => ['host' => 'localhost', 'port' => 0, 'socket' => '/tmp/mysql.sock'],
    'Unix Socket: /var/run/mysqld/mysqld.sock' => ['host' => 'localhost', 'port' => 0, 'socket' => '/var/run/mysqld/mysqld.sock'],
];

$success_method = null;

echo "<table>";
echo "<tr><th>Connection Candidate</th><th>Socket Exists?</th><th>Status</th><th>MySQL Response</th></tr>";

foreach ($tests as $label => $t) {
    echo "<tr><td><strong>$label</strong></td>";
    
    $sock_exists = '-';
    if ($t['socket'] !== null) {
        $sock_exists = file_exists($t['socket']) ? '<span class="success">YES</span>' : '<span class="danger">NO</span>';
    }
    echo "<td align='center'>$sock_exists</td>";
    
    $conn = false;
    try {
        if ($t['socket'] !== null) {
            $conn = @mysqli_connect($t['host'], $db_user, $db_pass, $db_name, 0, $t['socket']);
        } elseif ($t['port'] !== null) {
            $conn = @mysqli_connect($t['host'], $db_user, $db_pass, $db_name, $t['port']);
        } else {
            $conn = @mysqli_connect($t['host'], $db_user, $db_pass, $db_name);
        }
    } catch (\Throwable $e) {
        // catch exception
    }
    
    if ($conn) {
        echo "<td class='success'>CONNECTED!</td>";
        echo "<td>Server Info: " . mysqli_get_server_info($conn) . "</td>";
        mysqli_close($conn);
        if ($success_method === null) {
            $success_method = $t;
        }
    } else {
        $err = mysqli_connect_error();
        $errno = mysqli_connect_errno();
        echo "<td class='danger'>FAILED</td>";
        echo "<td>[$errno] " . htmlspecialchars($err) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

if ($success_method !== null) {
    echo "<div style='background:rgba(204,255,0,0.1); border:1px solid #ccff00; border-radius:8px; padding:15px; margin-top:20px;'>";
    echo "<h4 style='color:#ccff00; margin:0 0 8px 0;'>Connection Successful!</h4>";
    echo "<p style='margin:0;'>At least one connection method succeeded. Your MySQL database is active and accepting connections.</p>";
    echo "</div>";
} else {
    echo "<div style='background:rgba(244,63,94,0.1); border:1px solid #f43f5e; border-radius:8px; padding:15px; margin-top:20px;'>";
    echo "<h4 style='color:#f43f5e; margin:0 0 8px 0;'>All Connection Attempts Failed</h4>";
    echo "<p style='margin:0 0 8px 0;'>Review the error codes in the table above:</p>";
    echo "<ul style='margin:0;'>";
    echo "<li><strong>[1045] Access denied:</strong> The password or username in cPanel does not match, or user has not been granted 'ALL PRIVILEGES' on the database.</li>";
    echo "<li><strong>[1049] Unknown database:</strong> The database name does not exist or has a typo in cPanel.</li>";
    echo "<li><strong>[2002] Connection refused:</strong> MySQL service is not listening on that port or MySQL service is stopped.</li>";
    echo "</ul>";
    echo "</div>";
}
echo "</div></body></html>";
?>