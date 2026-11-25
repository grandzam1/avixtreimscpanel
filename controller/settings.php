<?php 
$page = "Settings";
include_once __DIR__ . '/header.php';
require_once __DIR__ . '/../mail-helper.php';

$msg = "";
$test_feedback = "";
$test_feedback_type = "";
$test_log = "";

if (isset($_POST['send_test_email'])) {
    $test_to = trim($_POST['test_recipient'] ?? '');
    $t_host  = trim($_POST['mail_host'] ?? '');
    $t_port  = (int)($_POST['mail_port'] ?? 465);
    $t_user  = trim($_POST['mail_user'] ?? '');
    $t_pass  = trim($_POST['mail_pass'] ?? '');

    if (empty($test_to) || !filter_var($test_to, FILTER_VALIDATE_EMAIL)) {
        $test_feedback = "Please provide a valid destination email address for the test.";
        $test_feedback_type = "danger";
    } else {
        $res = test_mail_connection($test_to, $t_host, $t_port, $t_user, $t_pass);
        if ($res['success']) {
            $test_feedback = "✓ Test email successfully sent to $test_to! Check your inbox or spam folder.";
            $test_feedback_type = "success";
        } else {
            $test_feedback = "✗ SMTP Test Failed: " . htmlspecialchars($res['error']);
            $test_feedback_type = "danger";
        }
        $test_log = $res['log'];
    }
}

if (isset($_POST['save'])) {
    $site_name   = test_input($_POST['site_name']);
    $site_email  = test_input($_POST['site_email']);
    $site_url    = test_input($_POST['site_url']);
    $btcwallet   = test_input($_POST['btcwallet']);
    $ethwallet   = test_input($_POST['ethwallet']);
    $usdtwallet  = test_input($_POST['usdtwallet']);
    $ref         = test_input($_POST['ref']);
    $whatsapp    = test_input($_POST['whatsapp']);
    $phone       = test_input($_POST['phone']);
    $address     = test_input($_POST['address']);
    $logo_url    = test_input($_POST['logo']);
    $theme_color = test_input($_POST['theme_color']);
    $bg_color    = test_input($_POST['bg_color']);

    // SEO Meta Fields
    $meta_title_in    = test_input($_POST['meta_title'] ?? '');
    $meta_desc_in     = test_input($_POST['meta_desc'] ?? '');
    $meta_keywords_in = test_input($_POST['meta_keywords'] ?? '');
    $meta_author_in   = test_input($_POST['meta_author'] ?? '');
    $og_image_in      = test_input($_POST['og_image'] ?? '');
    $google_analytics_in = trim($_POST['google_analytics'] ?? '');

    // Authenticated SMTP Configuration Fields
    $mail_host_in = test_input($_POST['mail_host'] ?? 'mail.avxtsinc.com');
    $mail_user_in = test_input($_POST['mail_user'] ?? 'noreply@avxtsinc.com');
    $mail_pass_in = trim($_POST['mail_pass'] ?? ($smtp_fallback_pass ?? 'CHANGE_ME_SMTP_PASS'));
    $mail_port_in = test_input($_POST['mail_port'] ?? '465');

    // Check if an image file was uploaded
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['logo_file']['tmp_name'];
        $file_name = $_FILES['logo_file']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
        if (in_array($ext, $allowed)) {
            $dest = "../images/logo_" . time() . "." . $ext;
            if (move_uploaded_file($file_tmp, $dest)) {
                $logo_url = "images/" . basename($dest);
            }
        }
    }

    if (empty($site_name) || empty($site_email) || empty($site_url)) {
        $msg = "Site name, email, and URL cannot be empty.";
    } else {
        // Ensure columns exist before updating
        ensure_column_exists($link, 'settings', 'address', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'phone', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'logo', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'theme_color', "VARCHAR(30) DEFAULT '#ccff00'");
        ensure_column_exists($link, 'settings', 'bg_color', "VARCHAR(30) DEFAULT '#0b0f0d'");
        ensure_column_exists($link, 'settings', 'walt_eth', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'walt_usdt', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'qr_btc', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'qr_eth', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'qr_usdt', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'meta_title', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'meta_desc', "TEXT DEFAULT NULL");
        ensure_column_exists($link, 'settings', 'meta_keywords', "TEXT DEFAULT NULL");
        ensure_column_exists($link, 'settings', 'meta_author', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'og_image', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'settings', 'google_analytics', "TEXT DEFAULT NULL");
        ensure_column_exists($link, 'settings', 'mail_host', "VARCHAR(255) DEFAULT 'mail.avxtsinc.com'");
        ensure_column_exists($link, 'settings', 'mail_user', "VARCHAR(255) DEFAULT 'noreply@avxtsinc.com'");
        ensure_column_exists($link, 'settings', 'mail_pass', "VARCHAR(255) DEFAULT '" . ($smtp_fallback_pass ?? 'CHANGE_ME_SMTP_PASS') . "'");
        ensure_column_exists($link, 'settings', 'mail_port', "VARCHAR(10) DEFAULT '465'");

        $s_name = mysqli_real_escape_string($link, $site_name);
        $s_mail = mysqli_real_escape_string($link, $site_email);
        $s_url  = mysqli_real_escape_string($link, $site_url);
        $b_walt = mysqli_real_escape_string($link, $btcwallet);
        $e_walt = mysqli_real_escape_string($link, $ethwallet);
        $u_walt = mysqli_real_escape_string($link, $usdtwallet);
        $s_ref  = mysqli_real_escape_string($link, $ref);
        $s_wa   = mysqli_real_escape_string($link, $whatsapp);
        $s_ph   = mysqli_real_escape_string($link, $phone);
        $s_addr = mysqli_real_escape_string($link, $address);
        $s_logo = mysqli_real_escape_string($link, $logo_url);
        $s_thm  = mysqli_real_escape_string($link, $theme_color);
        $s_bg   = mysqli_real_escape_string($link, $bg_color);
        $s_mttl = mysqli_real_escape_string($link, $meta_title_in);
        $s_mdsc = mysqli_real_escape_string($link, $meta_desc_in);
        $s_mkey = mysqli_real_escape_string($link, $meta_keywords_in);
        $s_math = mysqli_real_escape_string($link, $meta_author_in);
        $s_mogi = mysqli_real_escape_string($link, $og_image_in);
        $s_mga  = mysqli_real_escape_string($link, $google_analytics_in);
        $s_mhost = mysqli_real_escape_string($link, $mail_host_in);
        $s_muser = mysqli_real_escape_string($link, $mail_user_in);
        $s_mpass = mysqli_real_escape_string($link, $mail_pass_in);
        $s_mport = mysqli_real_escape_string($link, $mail_port_in);

        // Handle Admin QR code image uploads for BTC, ETH, USDT (100% Admin Hosted, 0% 3rd party)
        $wallet_dir = __DIR__ . '/../images/wallets/';
        if (!is_dir($wallet_dir)) {
            @mkdir($wallet_dir, 0777, true);
        }

        $qr_update_sql = "";
        $handle_qr_upload = function($field, $name_prefix) use ($wallet_dir) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
                if (in_array($ext, $allowed)) {
                    $target = $wallet_dir . $name_prefix . '_qr.' . $ext;
                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
                        return "images/wallets/" . $name_prefix . '_qr.' . $ext;
                    }
                }
            }
            return null;
        };

        $new_qr_btc = $handle_qr_upload('qr_btc_file', 'btc');
        if ($new_qr_btc) {
            $qr_update_sql .= ", qr_btc = '" . mysqli_real_escape_string($link, $new_qr_btc) . "'";
            $qr_btc = $new_qr_btc;
        }

        $new_qr_eth = $handle_qr_upload('qr_eth_file', 'eth');
        if ($new_qr_eth) {
            $qr_update_sql .= ", qr_eth = '" . mysqli_real_escape_string($link, $new_qr_eth) . "'";
            $qr_eth = $new_qr_eth;
        }

        $new_qr_usdt = $handle_qr_upload('qr_usdt_file', 'usdt');
        if ($new_qr_usdt) {
            $qr_update_sql .= ", qr_usdt = '" . mysqli_real_escape_string($link, $new_qr_usdt) . "'";
            $qr_usdt = $new_qr_usdt;
        }

        $save = mysqli_query($link, "UPDATE settings SET 
            sitename = '$s_name', 
            sitemail = '$s_mail', 
            siteurl = '$s_url', 
            walt = '$b_walt', 
            walt_eth = '$e_walt', 
            walt_usdt = '$u_walt', 
            ref_bonus = '$s_ref', 
            whatsapp = '$s_wa', 
            phone = '$s_ph', 
            address = '$s_addr', 
            logo = '$s_logo', 
            theme_color = '$s_thm', 
            bg_color = '$s_bg',
            meta_title = '$s_mttl',
            meta_desc = '$s_mdsc',
            meta_keywords = '$s_mkey',
            meta_author = '$s_math',
            og_image = '$s_mogi',
            google_analytics = '$s_mga',
            mail_host = '$s_mhost',
            mail_user = '$s_muser',
            mail_pass = '$s_mpass',
            mail_port = '$s_mport'
            $qr_update_sql
            WHERE id = '1'");

        if ($save) {
            $msg = "Settings, wallet addresses, QR codes, and SMTP configurations have been successfully updated!";
            $sitename    = $site_name;
            $sitemail    = $site_email;
            $siteurl     = $site_url;
            $wallet      = $btcwallet;
            $walt_eth    = $ethwallet;
            $walt_usdt   = $usdtwallet;
            $ref_bonus   = $ref;
            $whatsapp    = $whatsapp;
            $phone       = $phone;
            $siteaddress = $address;
            $logo        = $logo_url;
            $meta_title  = $meta_title_in;
            $meta_desc   = $meta_desc_in;
            $meta_keywords = $meta_keywords_in;
            $meta_author = $meta_author_in;
            $og_image    = $og_image_in;
            $google_analytics = $google_analytics_in;
            $mail_host   = $mail_host_in;
            $mail_user   = $mail_user_in;
            $mail_pass   = $mail_pass_in;
            $mail_port   = $mail_port_in;
        } else {
            $msg = "Error updating settings: " . mysqli_error($link);
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<div class="row">
    <?php if ($msg != "") { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">General Site Configuration & Theme Settings</h4>
                <p class="card-title-desc">Control site branding, contact information, theme colors, and admin-managed deposit wallets dynamically across the entire website.</p>
            </div>
            <div class="card-body">
                <form class="needs-validation" method="post" action="settings.php" enctype="multipart/form-data" novalidate>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="siteName">Site Name</label>
                                <input type="text" name="site_name" class="form-control" id="siteName" placeholder="e.g. Avix Treims" value="<?php echo htmlspecialchars($sitename); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="siteUrl">Site URL</label>
                                <input type="text" name="site_url" class="form-control" id="siteUrl" placeholder="https://example.com" value="<?php echo htmlspecialchars($siteurl); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="siteEmail">Site Email</label>
                                <input type="email" name="site_email" class="form-control" id="siteEmail" value="<?php echo htmlspecialchars($sitemail); ?>" placeholder="mail@domain.com" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="sitePhone">Phone Number</label>
                                <input type="text" name="phone" class="form-control" id="sitePhone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="+1 (555) 000-0000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="siteWa" style="color: #25d366; font-weight: bold;"><i class="mdi mdi-whatsapp"></i> Admin WhatsApp Number (Direct Chat)</label>
                                <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>" class="form-control" id="siteWa" placeholder="+1(555)234-5678">
                                <small class="text-muted">Controls the floating WhatsApp widget and header buttons across the site.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="siteLogo">Site Logo</label>
                                <?php if (!empty($logo) && file_exists("../" . $logo)) { ?>
                                <div class="mb-2 p-2 bg-dark rounded text-center">
                                    <img src="../<?php echo htmlspecialchars($logo); ?>" alt="Site Logo" style="max-height: 40px; max-width: 100%;">
                                </div>
                                <?php } ?>
                                <div class="mb-2">
                                    <label class="form-label font-size-12 text-muted">Upload New Logo Image</label>
                                    <input type="file" name="logo_file" class="form-control" accept="image/*">
                                </div>
                                <div>
                                    <label class="form-label font-size-12 text-muted">Or Logo Path / URL</label>
                                    <input type="text" name="logo" value="<?php echo htmlspecialchars($logo); ?>" class="form-control" id="siteLogo" placeholder="images/logo.png">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label" for="siteAddress">Physical Address</label>
                                <input type="text" name="address" value="<?php echo htmlspecialchars($siteaddress); ?>" class="form-control" id="siteAddress" placeholder="123 Financial Way, New York, NY">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="refBonus">Referral Bonus (%)</label>
                                <input type="text" name="ref" value="<?php echo htmlspecialchars($ref_bonus); ?>" class="form-control" id="refBonus" placeholder="10" required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3 text-primary"><i class="mdi mdi-palette-outline me-1"></i> Dynamic Theme Colors</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="themeColor">Primary Accent Color (Buttons, Glow, Indicators)</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="themeColorPicker" value="<?php echo htmlspecialchars($theme_color); ?>" onchange="document.getElementById('themeColorText').value = this.value; document.getElementById('previewBadge').style.backgroundColor = this.value;">
                                    <input type="text" name="theme_color" class="form-control" id="themeColorText" value="<?php echo htmlspecialchars($theme_color); ?>" onchange="document.getElementById('themeColorPicker').value = this.value; document.getElementById('previewBadge').style.backgroundColor = this.value;" placeholder="#ccff00">
                                    <span class="input-group-text" id="previewBadge" style="background-color: <?php echo htmlspecialchars($theme_color); ?>; color: #000; font-weight: bold; width: 90px; text-align: center;">Live</span>
                                </div>
                                <small class="text-muted">Controls the neon lime accent color matching the modern fintech dashboard.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bgColor">Dark Background Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="bgColorPicker" value="<?php echo htmlspecialchars($bg_color); ?>" onchange="document.getElementById('bgColorText').value = this.value;">
                                    <input type="text" name="bg_color" class="form-control" id="bgColorText" value="<?php echo htmlspecialchars($bg_color); ?>" onchange="document.getElementById('bgColorPicker').value = this.value;" placeholder="#0b0f0d">
                                </div>
                                <small class="text-muted">Controls the deep dark background tone of the dashboard and site.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3 text-warning"><i class="mdi mdi-currency-btc me-1"></i> Admin Controlled Deposit Wallets & QR Codes (100% Admin Hosted, 0% 3rd Party)</h5>
                    <p class="text-muted">These wallet addresses and QR codes are strictly configured here by the admin. The user deposit interface displays these exact addresses and renders QR codes locally from this admin data with zero third-party external calls.</p>

                    <div class="row g-4">
                        <!-- Bitcoin (BTC) -->
                        <div class="col-md-4">
                            <div class="p-3 border rounded h-100 bg-light">
                                <h6 class="fw-bold text-primary mb-3"><i class="mdi mdi-currency-btc me-1"></i> Bitcoin (BTC)</h6>
                                <div class="mb-3">
                                    <label class="form-label font-size-12 fw-bold" for="btcWallet">BTC Wallet Address</label>
                                    <input type="text" class="form-control" name="btcwallet" id="btcWallet" value="<?php echo htmlspecialchars($wallet); ?>" placeholder="bc1q..." required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label font-size-12 fw-bold">Upload Official BTC QR Code Image</label>
                                    <input type="file" name="qr_btc_file" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted d-block mt-1">Leave empty to auto-generate locally from address.</small>
                                </div>
                                <?php if (!empty($qr_btc) && file_exists(__DIR__ . '/../' . explode('?', $qr_btc)[0])) { ?>
                                    <div class="mt-2 text-center p-2 bg-white rounded border">
                                        <small class="text-success fw-bold d-block mb-1"><i class="mdi mdi-check-circle"></i> Custom Admin QR Active</small>
                                        <img src="../<?php echo htmlspecialchars($qr_btc); ?>" alt="BTC QR" style="max-height: 110px; width: auto; object-fit: contain;">
                                    </div>
                                <?php } else { ?>
                                    <div class="mt-2 text-center p-2 bg-white rounded border text-muted small">
                                        <i class="mdi mdi-qrcode me-1"></i> Auto-rendered locally from BTC address
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Ethereum (ETH) -->
                        <div class="col-md-4">
                            <div class="p-3 border rounded h-100 bg-light">
                                <h6 class="fw-bold text-info mb-3"><i class="mdi mdi-ethereum me-1"></i> Ethereum (ETH / ERC20)</h6>
                                <div class="mb-3">
                                    <label class="form-label font-size-12 fw-bold" for="ethWallet">ETH Wallet Address</label>
                                    <input type="text" class="form-control" name="ethwallet" id="ethWallet" value="<?php echo htmlspecialchars($walt_eth); ?>" placeholder="0x...">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label font-size-12 fw-bold">Upload Official ETH QR Code Image</label>
                                    <input type="file" name="qr_eth_file" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted d-block mt-1">Leave empty to auto-generate locally from address.</small>
                                </div>
                                <?php if (!empty($qr_eth) && file_exists(__DIR__ . '/../' . explode('?', $qr_eth)[0])) { ?>
                                    <div class="mt-2 text-center p-2 bg-white rounded border">
                                        <small class="text-success fw-bold d-block mb-1"><i class="mdi mdi-check-circle"></i> Custom Admin QR Active</small>
                                        <img src="../<?php echo htmlspecialchars($qr_eth); ?>" alt="ETH QR" style="max-height: 110px; width: auto; object-fit: contain;">
                                    </div>
                                <?php } else { ?>
                                    <div class="mt-2 text-center p-2 bg-white rounded border text-muted small">
                                        <i class="mdi mdi-qrcode me-1"></i> Auto-rendered locally from ETH address
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- USDT (TRC20) -->
                        <div class="col-md-4">
                            <div class="p-3 border rounded h-100 bg-light">
                                <h6 class="fw-bold text-success mb-3"><i class="mdi mdi-currency-usd me-1"></i> USDT (TRC20 / ERC20)</h6>
                                <div class="mb-3">
                                    <label class="form-label font-size-12 fw-bold" for="usdtWallet">USDT Wallet Address</label>
                                    <input type="text" class="form-control" name="usdtwallet" id="usdtWallet" value="<?php echo htmlspecialchars($walt_usdt); ?>" placeholder="T...">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label font-size-12 fw-bold">Upload Official USDT QR Code Image</label>
                                    <input type="file" name="qr_usdt_file" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted d-block mt-1">Leave empty to auto-generate locally from address.</small>
                                </div>
                                <?php if (!empty($qr_usdt) && file_exists(__DIR__ . '/../' . explode('?', $qr_usdt)[0])) { ?>
                                    <div class="mt-2 text-center p-2 bg-white rounded border">
                                        <small class="text-success fw-bold d-block mb-1"><i class="mdi mdi-check-circle"></i> Custom Admin QR Active</small>
                                        <img src="../<?php echo htmlspecialchars($qr_usdt); ?>" alt="USDT QR" style="max-height: 110px; width: auto; object-fit: contain;">
                                    </div>
                                <?php } else { ?>
                                    <div class="mt-2 text-center p-2 bg-white rounded border text-muted small">
                                        <i class="mdi mdi-qrcode me-1"></i> Auto-rendered locally from USDT address
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-2 text-success"><i class="mdi mdi-email-lock me-1"></i> Authenticated Production SMTP Mail Server Gateway</h5>
                    <p class="text-muted">Direct TLS/SSL SMTP authentication used for outgoing system notifications, password resets, and bidirectional customer support communications.</p>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label" for="mailHost">SMTP Host / Server</label>
                                <input type="text" class="form-control" name="mail_host" id="mailHost" value="<?php echo htmlspecialchars($mail_host ?? 'mail.avixtreimsco.com'); ?>" placeholder="mail.avixtreimsco.com" required>
                                <small class="text-muted">e.g. mail.avixtreimsco.com or localhost</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label" for="mailPort">SMTP Port</label>
                                <input type="number" class="form-control" name="mail_port" id="mailPort" value="<?php echo htmlspecialchars($mail_port ?? '465'); ?>" placeholder="465" required>
                                <small class="text-muted">465 (SSL), 587 (TLS), or 25</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="mailUser">SMTP Username / Dispatch Email</label>
                                <input type="email" class="form-control" name="mail_user" id="mailUser" value="<?php echo htmlspecialchars($mail_user ?? 'noreply@avixtreimsco.com'); ?>" placeholder="noreply@avixtreimsco.com" required>
                                <small class="text-muted">Authenticated email account</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label" for="mailPass">SMTP Password</label>
                                <input type="password" class="form-control" name="mail_pass" id="mailPass" value="<?php echo htmlspecialchars($mail_pass ?? ''); ?>" placeholder="••••••••••••">
                                <small class="text-muted">cPanel mailbox secret</small>
                            </div>
                        </div>
                    </div>

                    <!-- SMTP Diagnostic Testing Box -->
                    <div class="p-3 mb-3 rounded" style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(204,255,0,0.3);">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <strong class="text-warning"><i class="mdi mdi-flash me-1"></i> Live Email Diagnostic Tester</strong>
                                <p class="text-muted small mb-0">Send a live test verification email right now to confirm your outgoing mail server is functioning.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="email" name="test_recipient" class="form-control form-control-sm" style="max-width: 260px;" placeholder="Enter your email to test..." value="<?php echo htmlspecialchars($_POST['test_recipient'] ?? ($aemail ?? '')); ?>">
                                <button type="submit" name="send_test_email" class="btn btn-warning btn-sm px-3" onclick="return confirm('Dispatch live test email with these SMTP credentials?');">
                                    <i class="mdi mdi-send-check me-1"></i> Send Test Email
                                </button>
                            </div>
                        </div>

                        <?php if (!empty($test_feedback)) { ?>
                            <div class="mt-3 alert alert-<?php echo $test_feedback_type; ?> mb-2">
                                <?php echo $test_feedback; ?>
                            </div>
                            <?php if (!empty($test_log)) { ?>
                                <details class="mt-2">
                                    <summary class="small text-muted" style="cursor: pointer;"><i class="mdi mdi-console"></i> View SMTP Handshake Log</summary>
                                    <pre class="p-2 mt-1 rounded bg-dark text-white font-monospace small" style="max-height: 180px; overflow-y: auto;"><?php echo htmlspecialchars($test_log); ?></pre>
                                </details>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-2 text-info"><i class="mdi mdi-google me-1"></i> Search Engine Optimization (SEO) & Google Ranking Meta Words</h5>
                    <p class="text-muted">Master control over Google indexing, meta titles, descriptions, and keywords. Any site name set above is dynamically prioritized so Google searches immediately pick up this exact brand name.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="metaTitle">SEO Site Title (Appears in Google Search & Browser Tabs)</label>
                                <input type="text" class="form-control" name="meta_title" id="metaTitle" value="<?php echo htmlspecialchars($meta_title ?? ''); ?>" placeholder="<?php echo htmlspecialchars($sitename); ?> - Digital Asset Management & Technology Platform">
                                <small class="text-muted">Recommended length: 50-60 characters. Defaults dynamically to brand title.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="metaAuthor">Meta Author & Publisher</label>
                                <input type="text" class="form-control" name="meta_author" id="metaAuthor" value="<?php echo htmlspecialchars($meta_author ?? $sitename); ?>" placeholder="<?php echo htmlspecialchars($sitename); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="metaDesc">Google Meta Description (Snippet displayed beneath link in search results)</label>
                                <textarea class="form-control" name="meta_desc" id="metaDesc" rows="3" placeholder="<?php echo htmlspecialchars($sitename); ?> provides institutional digital asset infrastructure, quantitative analytics, cold-storage custody, and multi-asset portfolio management solutions."><?php echo htmlspecialchars($meta_desc ?? ''); ?></textarea>
                                <small class="text-muted">Recommended length: 140-160 characters for maximum search conversion.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="metaKeywords" style="font-weight:700; color:#0284c7;"><i class="mdi mdi-tag-multiple me-1"></i> Search Keywords & Meta Words (Comma-Separated)</label>
                                <textarea class="form-control" name="meta_keywords" id="metaKeywords" rows="3" placeholder="<?php echo htmlspecialchars($sitename); ?>, digital asset management, cryptocurrency custody, institutional portfolio terminal, quantitative analytics, blockchain technology, secure client portal"><?php echo htmlspecialchars($meta_keywords ?? ''); ?></textarea>
                                <small class="text-muted">Input high-ranking search words. Separate each keyword with a comma. Google and AI indexing bots rank these search words with priority.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="ogImage">Social Share & WhatsApp Preview Image (og:image)</label>
                                <input type="text" class="form-control" name="og_image" id="ogImage" value="<?php echo htmlspecialchars($og_image ?? $logo); ?>" placeholder="images/logo.png">
                                <small class="text-muted">Image displayed when sharing links on WhatsApp, Facebook, X (Twitter), and Telegram.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="googleAnalytics">Google Search Console Verification Tag / Header Code</label>
                                <input type="text" class="form-control" name="google_analytics" id="googleAnalytics" value="<?php echo htmlspecialchars($google_analytics ?? ''); ?>" placeholder='<meta name="google-site-verification" content="..." />'>
                                <small class="text-muted">Optional: Paste Google verification tag or Google Analytics tracking tag.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary px-4 py-2" name="save" type="submit">
                            <i class="mdi mdi-content-save me-1"></i> Save All Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
