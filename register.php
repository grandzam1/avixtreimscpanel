<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
include 'db.php';
include 'config.php';

$fname = $username = $email = $password = $phone = "";
$err = "";
$suc = "";
$wrn = "";

$ref_user = isset($_GET['ref']) ? strtolower(trim($_GET['ref'])) : "";

if (isset($_POST['submit'])) {

    // Validate name
    if (empty(test_input($_POST["name"]))) {
        $wrn = "Please enter your full name.";
    } else {
        $fname = test_input($_POST["name"]);
    }

    // Validate phone
    if (empty(test_input($_POST["phone"]))) {
        $wrn = "Please enter your phone number.";
    } else {
        $phone = test_input($_POST["phone"]);
    }

    // Validate username
    if (empty(test_input($_POST["username"]))) {
        $wrn = "Please choose a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $err = "This username is already taken. Please choose another.";
                } else {
                    $username = ucwords(test_input($_POST["username"]));
                }
            } else {
                $err = "Database error. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if (empty(test_input($_POST["email"]))) {
        $wrn = "Please enter your email address.";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $err = "An account with this email already exists.";
                } else {
                    $email = test_input($_POST["email"]);
                }
            } else {
                $err = "Database error. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(test_input($_POST["password"]))) {
        $wrn = "Please enter a password.";
    } elseif (strlen(test_input($_POST["password"])) < 6) {
        $wrn = "Password must be at least 6 characters long.";
    } else {
        $password = test_input($_POST["password"]);
    }

    if (empty(test_input($_POST["password2"]))) {
        $wrn = "Please confirm your password.";
    } elseif ($password !== test_input($_POST["password2"])) {
        $err = "Passwords do not match.";
    }

    // Invisible anti-spam honeypot (silent block for automated bots)
    if (!empty($_POST['website_hp'])) {
        $err = "Spam detection triggered. Please try again.";
    }

    if (empty($err) && empty($wrn)) {
        $refuser = test_input($_POST['ref'] ?? '');
        $query = "INSERT INTO users (fullname, username, email, password, phone, referral) 
                  VALUES ('$fname', '$username', '$email', '$password', '$phone', '$refuser')";
        $save = mysqli_query($link, $query);

        if ($save) {
            if (!empty($refuser)) {
                $ref_bonus_val = isset($ref_bonus) ? floatval($ref_bonus) : 0;
                if ($ref_bonus_val > 0) {
                    mysqli_query($link, "UPDATE users SET refbalance = refbalance + $ref_bonus_val WHERE username = '$refuser'");
                }
            }

            // Welcome Email
            $subject = "Welcome to " . $sitename;
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: $sitename <$sitemail>\r\n";

            $message = '
            <div style="background:#0d120f; padding:24px; font-family:sans-serif; color:#fff;">
                <div style="background:#151c18; max-width:540px; margin:auto; padding:28px; border-radius:16px; border:1px solid rgba(255,255,255,0.1);">
                    <h2 style="color:#ccff00; margin-top:0;">Welcome to '.$sitename.'!</h2>
                    <p>Dear <strong>'.$username.'</strong>,</p>
                    <p>Thank you for registering your investment account. You now have access to high-yield institutional cryptocurrency trading and portfolio management.</p>
                    <p style="color:#8c9e94;">Login to your dashboard to make your first deposit and activate an investment plan.</p>
                    <hr style="border:none; border-top:1px solid rgba(255,255,255,0.1); margin:20px 0;">
                    <p style="color:#777; font-size:0.75rem;">&copy; '.date("Y").' '.$sitename.'. All rights reserved.</p>
                </div>
            </div>';

            @mail($email, $subject, $message, $headers);
            $suc = "Registration successful! Redirecting to sign in...";
            echo "<meta http-equiv='refresh' content='2; url=signin.php?success' />";
        } else {
            $err = "Sign up could not be completed. Please try again.";
        }
    }
}

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Create Investor Account - <?php echo htmlspecialchars($sitename); ?></title>
    <link rel="shortcut icon" href="images/bitcoin31.png">

    <!-- Progressive Web App (PWA) & Native Mobile App Meta -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#090e0b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($sitename); ?>">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="192x192" href="images/icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="images/icons/icon-512.png">
    <link rel="stylesheet" href="pwa.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- SweetAlert & jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <style>
        :root {
            --primary-accent: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : '#ccff00'; ?>;
            --primary-accent-glow: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) . '44' : 'rgba(204, 255, 0, 0.28)'; ?>;
            --accent-crimson: #e82127;
            --accent-cyan: #00e5ff;
            /* High-Contrast Obsidian Vault Palette with Ambient Clean-Energy Halo */
            --bg-canvas: #090e0b;
            --bg-card: rgba(15, 22, 17, 0.94);
            --bg-input: rgba(9, 14, 11, 0.88);
            --card-border: rgba(255, 255, 255, 0.11);
            --card-border-glow: rgba(204, 255, 0, 0.35);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --text-label: #cbd5e1;
            --font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #090e0b;
            color: #ffffff;
            font-family: var(--font-family);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 40px 16px;
            /* Ambient Clean-Energy Daylight Halo behind the Central Vault Card */
            background-image: 
                radial-gradient(circle at 50% 48%, rgba(204, 255, 0, 0.12) 0%, rgba(0, 229, 255, 0.08) 25%, rgba(248, 250, 252, 0.04) 50%, transparent 70%),
                radial-gradient(circle at 15% 15%, rgba(0, 176, 116, 0.14) 0%, transparent 45%),
                radial-gradient(circle at 85% 35%, rgba(2, 132, 199, 0.10) 0%, transparent 50%),
                radial-gradient(circle at 50% 90%, rgba(0, 0, 0, 0.85) 0%, transparent 60%);
            background-attachment: fixed;
        }

        #matrixBg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.35;
        }

        .auth-container {
            width: 100%;
            max-width: 580px;
            position: relative;
            z-index: 10;
        }

        .auth-card {
            background: var(--bg-card);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 38px 32px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.8), 0 0 30px rgba(204, 255, 0, 0.06);
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary-accent), var(--accent-cyan), transparent);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 26px;
        }

        /* High-Contrast Brand Plinth: Ensures Logo ALWAYS Pops Out Brilliantly */
        .brand-logo-pod {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 12px 26px;
            background: #080c09;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.7), 0 0 16px rgba(204, 255, 0, 0.12);
            text-decoration: none;
            margin-bottom: 12px;
            transition: all 0.25s ease;
        }

        .brand-logo-pod:hover {
            border-color: var(--primary-accent);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8), 0 0 24px var(--primary-accent-glow);
            transform: translateY(-2px);
        }

        .brand-logo-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .brand-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary-accent);
            box-shadow: 0 0 16px var(--primary-accent);
            animation: pulseDot 2s infinite ease-in-out;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); box-shadow: 0 0 12px var(--primary-accent); }
            50% { transform: scale(1.2); box-shadow: 0 0 22px var(--primary-accent); }
        }

        .brand-title {
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .auth-sub {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .form-grid, .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 600px) {
            .form-grid, .form-grid-2 {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            .auth-card {
                padding: 28px 20px;
            }
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-label);
            margin-bottom: 7px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 0.95rem;
            transition: color 0.2s ease;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--card-border);
            color: #ffffff;
            padding: 13px 16px 13px 44px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
            transition: all 0.25s ease;
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-control:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px rgba(204, 255, 0, 0.2);
            background: rgba(14, 20, 16, 0.95);
        }

        .form-control:focus + .input-icon,
        .input-wrap:focus-within .input-icon {
            color: var(--primary-accent);
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            padding-right: 40px;
        }

        select.form-control option {
            background: #ffffff;
            color: #090d16;
        }

        .pwd-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.95rem;
            padding: 4px;
            transition: color 0.2s;
        }

        .pwd-toggle:hover {
            color: var(--text-main);
        }

        .pin-badge {
            background: rgba(0, 176, 116, 0.08);
            border: 1px solid rgba(0, 176, 116, 0.25);
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            color: var(--primary-accent);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Spacious & Premium Terms Agreement Row */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 22px;
            margin-bottom: 26px;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            transition: all 0.25s ease;
        }

        .terms-row:hover {
            background: rgba(204, 255, 0, 0.03);
            border-color: rgba(204, 255, 0, 0.25);
        }

        .terms-row input[type="checkbox"] {
            width: 19px;
            height: 19px;
            min-width: 19px;
            accent-color: var(--primary-accent);
            cursor: pointer;
            border-radius: 5px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .terms-row label {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
            cursor: pointer;
            user-select: none;
            margin: 0;
        }

        .terms-row label a {
            color: var(--primary-accent);
            text-decoration: underline;
            text-underline-offset: 3px;
            font-weight: 700;
            transition: opacity 0.2s;
        }

        .terms-row label a:hover {
            opacity: 0.85;
        }

        /* Next-Gen High-Converting Luxury CTA Button */
        .btn-submit {
            width: 100%;
            padding: 16px 28px;
            background: #ccff00;
            background: linear-gradient(135deg, #ccff00 0%, #b8f000 100%);
            color: #060907;
            font-weight: 800;
            font-size: 1.02rem;
            letter-spacing: 0.3px;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 28px rgba(204, 255, 0, 0.35), 0 2px 4px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: skewX(-20deg);
            transition: 0.6s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px rgba(204, 255, 0, 0.5), 0 4px 8px rgba(0, 0, 0, 0.4);
            filter: brightness(1.05);
            color: #000000;
        }

        .btn-submit:hover::after {
            left: 140%;
        }

        .btn-submit:hover i {
            transform: translateX(4px);
        }

        .btn-submit i {
            font-size: 0.95rem;
            transition: transform 0.25s ease;
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 6px 18px rgba(204, 255, 0, 0.3);
        }

        .auth-footer {
            margin-top: 26px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 800;
            transition: opacity 0.2s;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.84rem;
            text-decoration: none;
            margin-top: 14px;
            transition: color 0.2s;
        }

        .back-home:hover {
            color: var(--text-main);
        }

        .back-home:hover i {
            transform: translateX(-3px);
        }

        .back-home i {
            transition: transform 0.2s ease;
        }
    </style>
</head>
<body>

    <!-- 3D Canvas Background -->
    <canvas id="matrixBg"></canvas>

    <div class="auth-container">
        <div class="auth-card">
            <div class="brand-header">
                <a href="index.php" class="brand-logo-pod" title="<?php echo htmlspecialchars($sitename); ?>">
                    <?php if (!empty($logo) && file_exists($logo)) { ?>
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($sitename); ?>" style="max-height: 48px; width: auto; object-fit: contain;">
                    <?php } else { ?>
                        <span class="brand-dot"></span>
                        <span class="brand-title"><?php echo htmlspecialchars($sitename); ?></span>
                    <?php } ?>
                </a>
                <p class="auth-sub">Open an institutional investment account in seconds.</p>
            </div>

            <form action="register.php" method="post" id="regForm">
                <div class="form-grid">
                    <!-- Full Name -->
                    <div class="form-group">
                        <label class="form-label" for="regName">Full Name</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" id="regName" name="name" value="<?php echo htmlspecialchars($fname); ?>" class="form-control" placeholder="John Doe" required autofocus>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="form-group">
                        <label class="form-label" for="regUsername">Username</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-id-badge input-icon"></i>
                            <input type="text" id="regUsername" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control" placeholder="johndoe88" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="regEmail">Email Address</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="regEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" placeholder="john@example.com" required>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="form-label" for="regPhone">Phone Number</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="tel" id="regPhone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control" placeholder="+1 (555) 000-0000" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="regPassword">Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min. 6 chars" required minlength="6">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="regPassword2">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-shield-halved input-icon"></i>
                            <input type="password" id="regPassword2" name="password2" class="form-control" placeholder="Re-type password" required>
                        </div>
                    </div>

                    <!-- Invisible Honeypot Anti-Bot Field -->
                    <input type="text" name="website_hp" style="display:none !important; visibility:hidden !important;" tabindex="-1" autocomplete="off">
                    <input type="hidden" name="ref" value="<?php echo htmlspecialchars($ref_user); ?>">
                </div>

                <div class="terms-row">
                    <input type="checkbox" name="agree" id="agreeTerms" required checked value="1">
                    <label for="agreeTerms">I agree to the <a href="index.php#faq" target="_blank">Terms of Service & Privacy Policy</a> of <?php echo htmlspecialchars($sitename); ?>.</label>
                </div>

                <button type="submit" name="submit" class="btn-submit" id="btnSubmitReg">
                    <span>Create Investor Account</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="signin.php">Sign In</a>
            </div>

            <?php 
            $wa_reg = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '15552345678');
            $clean_wa_r = preg_replace('/[^0-9]/', '', $wa_reg);
            if (empty($clean_wa_r)) $clean_wa_r = '15552345678';
            $wa_url_r = "https://wa.me/" . $clean_wa_r . "?text=" . urlencode("Hello Admin, I am creating an account on " . $sitename . " and would like more information on investment plans");
            ?>
            <div style="text-align:center; margin-top:20px; margin-bottom:14px;">
                <a href="<?php echo htmlspecialchars($wa_url_r); ?>" target="_blank" style="display:inline-flex; align-items:center; gap:8px; font-size:0.84rem; color:#25d366; text-decoration:none; font-weight:700; background:rgba(37,211,102,0.1); border:1px solid rgba(37,211,102,0.3); padding:10px 20px; border-radius:24px; transition:all 0.2s;">
                    <i class="fa-brands fa-whatsapp" style="font-size:16px;"></i> Questions? Chat Admin on WhatsApp
                </a>
            </div>

            <div style="text-align:center;">
                <a href="index.php" class="back-home">
                    <i class="fa-solid fa-arrow-left"></i> Return to Homepage
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($suc != "") { ?>
    <script>
        $(document).ready(function() {
            swal({
                title: "Registration Complete!",
                text: "<?php echo addslashes($suc); ?>",
                icon: "success",
                buttons: false,
                timer: 2500
            });
        });
    </script>
    <?php } ?>

    <?php if ($err != "") { ?>
    <script>
        $(document).ready(function() {
            swal({
                title: "Registration Error",
                text: "<?php echo addslashes($err); ?>",
                icon: "error",
                button: "Try Again"
            });
        });
    </script>
    <?php } ?>

    <?php if ($wrn != "") { ?>
    <script>
        $(document).ready(function() {
            swal({
                title: "Attention",
                text: "<?php echo addslashes($wrn); ?>",
                icon: "warning",
                button: "OK"
            });
        });
    </script>
    <?php } ?>

    <script>
        // Animated 3D Floating Particle Canvas
        (function() {
            const canvas = document.getElementById('matrixBg');
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];
            const particleCount = 55;
            const accentColor = '<?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : "#ccff00"; ?>';

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }

            function initParticles() {
                particles = [];
                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        radius: Math.random() * 2 + 1,
                        vx: (Math.random() - 0.5) * 0.8,
                        vy: (Math.random() - 0.5) * 0.8,
                        alpha: Math.random() * 0.5 + 0.2
                    });
                }
            }

            function draw() {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;

                    if (p.x < 0) p.x = width;
                    if (p.x > width) p.x = 0;
                    if (p.y < 0) p.y = height;
                    if (p.y > height) p.y = 0;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = accentColor;
                    ctx.globalAlpha = p.alpha;
                    ctx.fill();

                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const dx = p.x - p2.x;
                        const dy = p.y - p2.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 110) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = accentColor;
                            ctx.globalAlpha = (1 - dist / 110) * 0.2;
                            ctx.stroke();
                        }
                    }
                }

                ctx.globalAlpha = 1.0;
                requestAnimationFrame(draw);
            }

            window.addEventListener('resize', () => {
                resize();
                initParticles();
            });

            resize();
            initParticles();
            draw();
        })();
    </script>
    <!-- Progressive Web App Controller -->
    <script src="pwa.js"></script>
</body>
</html>
