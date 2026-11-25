<?php
/**
 * LOCAL DEV ONLY - seeds a demo admin and demo user.
 * Passwords are stored in plaintext because this app compares them directly
 * (see signin.php / controller/signin.php). Safe to re-run: upserts by email.
 * Remove with: php seed_demo.php --remove   (or delete the rows manually)
 */

include_once __DIR__ . '/db.php';

$demo_admin_email = 'admin@demo.test';
$demo_admin_pass  = 'Admin@12345';
$demo_user_email  = 'user@demo.test';
$demo_user_pass   = 'User@12345';
$demo_user_name   = 'demouser';

$remove = in_array('--remove', $argv ?? []);

if ($remove) {
    mysqli_query($link, "DELETE FROM admin WHERE email='$demo_admin_email'");
    mysqli_query($link, "DELETE FROM users WHERE email='$demo_user_email'");
    echo "Removed demo accounts.\n";
    exit(0);
}

// --- Admin ---
$exists = mysqli_query($link, "SELECT id FROM admin WHERE email='$demo_admin_email'");
if ($exists && mysqli_num_rows($exists) > 0) {
    mysqli_query($link, "UPDATE admin SET password='$demo_admin_pass' WHERE email='$demo_admin_email'");
    echo "Admin updated: $demo_admin_email\n";
} else {
    mysqli_query($link, "INSERT INTO admin (email, password) VALUES ('$demo_admin_email', '$demo_admin_pass')");
    echo "Admin created: $demo_admin_email\n";
}

// --- User ---
$exists = mysqli_query($link, "SELECT id FROM users WHERE email='$demo_user_email'");
if ($exists && mysqli_num_rows($exists) > 0) {
    mysqli_query($link, "UPDATE users SET password='$demo_user_pass' WHERE email='$demo_user_email'");
    echo "User updated: $demo_user_email\n";
} else {
    mysqli_query($link, "INSERT INTO users (fullname, username, email, password, phone, verification, kyc_status, dbalance, mbalance, refbalance, abonus)
        VALUES ('Demo User', '$demo_user_name', '$demo_user_email', '$demo_user_pass', '+10000000000', 1, 'verified', '0.00', '0.00', '0.00', '0.00')");
    echo "User created: $demo_user_email\n";
}

echo "Done. Admin login: /uadmin (or /controller/signin.php) | User login: /signin\n";
