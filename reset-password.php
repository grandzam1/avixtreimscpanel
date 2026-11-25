<?php 
session_start();
include 'db.php';
include 'config.php';

$err = "";
$msg = "";
$link_expired = false;

$param_token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
$param_email = trim($_GET['email'] ?? ($_POST['email'] ?? ''));
$param_otp   = trim($_GET['otp'] ?? ($_POST['otp_code'] ?? ''));

// Validate token or OTP on initial load if provided
if (!empty($param_token) && !empty($param_email)) {
    $tok_esc = mysqli_real_escape_string($link, $param_token);
    $em_esc  = mysqli_real_escape_string($link, $param_email);
    
    $chk = mysqli_query($link, "SELECT * FROM password_resets WHERE token = '$tok_esc' AND email = '$em_esc' AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
    if (!$chk || mysqli_num_rows($chk) === 0) {
        $link_expired = true;
    }
}

// Handle Reset Password Submission
if (isset($_POST['do_reset'])) {
    $email    = mysqli_real_escape_string($link, trim($_POST['email'] ?? ''));
    $token    = mysqli_real_escape_string($link, trim($_POST['token'] ?? ''));
    $otp      = mysqli_real_escape_string($link, trim($_POST['otp_code'] ?? ''));
    $new_pass = trim($_POST['new_password'] ?? '');
    $cfm_pass = trim($_POST['confirm_password'] ?? '');

    if (empty($email)) {
        $err = "Email address is required.";
    } elseif (empty($token) && empty($otp)) {
        $err = "Reset token or 6-digit verification code is required.";
    } elseif (strlen($new_pass) < 6) {
        $err = "New password must be at least 6 characters long.";
    } elseif ($new_pass !== $cfm_pass) {
        $err = "New passwords do not match. Please verify and re-type.";
    } else {
        // Query matching valid reset request
        $where_clause = "";
        if (!empty($token)) {
            $where_clause = "token = '$token' AND email = '$email'";
        } else {
            $where_clause = "otp_code = '$otp' AND email = '$email'";
        }

        $q_chk = mysqli_query($link, "SELECT * FROM password_resets WHERE $where_clause AND used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        
        if ($q_chk && mysqli_num_rows($q_chk) > 0) {
            $reset_row = mysqli_fetch_assoc($q_chk);
            $reset_id  = intval($reset_row['id']);
            $user_id   = intval($reset_row['user_id']);
            $n_pwd_esc = mysqli_real_escape_string($link, $new_pass);

            // Update user's password
            $upd_user = mysqli_query($link, "UPDATE users SET password = '$n_pwd_esc' WHERE id = '$user_id'");
            
            if ($upd_user) {
                // Mark reset token as used
                mysqli_query($link, "UPDATE password_resets SET used = 1 WHERE id = '$reset_id'");

                // Send confirmation email
                if (function_exists('send_platform_email')) {
                    $u_res = mysqli_query($link, "SELECT email, fullname, username FROM users WHERE id = '$user_id' LIMIT 1");
                    if ($u_res && $u_data = mysqli_fetch_assoc($u_res)) {
                        $subj = "Security Notice: Password Reset Successful - " . $sitename;
                        $body = "Hello " . htmlspecialchars($u_data['fullname'] ?: $u_data['username']) . ",\n\n"
                              . "This is a confirmation that your password for your " . $sitename . " account was successfully updated on " . date('F j, Y, g:i a') . ".\n\n"
                              . "You can now sign in using your new credentials.\n\n"
                              . "If you did not authorize this change, please contact our security team immediately.";
                        send_platform_email($u_data['email'], $u_data['fullname'] ?: $u_data['username'], $subj, $body);
                    }
                }

                $msg = "Your password has been reset successfully! Redirecting you to sign in...";
                echo "<meta http-equiv='refresh' content='2; url=signin.php?reset=success' />";
            } else {
                $err = "Database error updating password: " . mysqli_error($link);
            }
        } else {
            $err = "The password reset link or verification code is invalid, has expired, or was already used. Please request a new one.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Set New Password - <?php echo htmlspecialchars($sitename); ?></title>
    <link rel="shortcut icon" href="images/bitcoin31.png">

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#090e0b">
    <link rel="stylesheet" href="pwa.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome & SweetAlert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="dashboard/style/jquery.js"></script>

    <style>
        :root {
            --primary-accent: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : '#ccff00'; ?>;
            --primary-accent-glow: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) . '40' : 'rgba(204, 255, 0, 0.25)'; ?>;
            --bg-dark-canvas: #090e0b;
            --bg-dark-card: #141c17;
            --card-border: rgba(255, 255, 255, 0.1);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-dark-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 24px 16px;
        }

        #matrixBg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            opacity: 0.35;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
        }

        .auth-card {
            background: linear-gradient(180deg, #16201a 0%, #0e1411 100%);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 40px 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8), 0 0 35px rgba(204, 255, 0, 0.08);
            position: relative;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 26px;
        }

        .brand-logo-pod {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            margin-bottom: 12px;
        }

        .brand-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary-accent);
            box-shadow: 0 0 14px var(--primary-accent);
        }

        .brand-title {
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .auth-sub {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-top: 6px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: var(--primary-accent);
            font-size: 1rem;
            pointer-events: none;
        }

        .pwd-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.05rem;
            cursor: pointer;
            padding: 4px;
        }

        .form-control {
            width: 100%;
            background: #0b100d;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            padding: 14px 16px 14px 46px;
            border-radius: 14px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .form-control:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 16px var(--primary-accent-glow);
            background: #0f1612;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-accent);
            color: #000000;
            border: none;
            border-radius: 14px;
            padding: 15px;
            font-size: 0.98rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px var(--primary-accent-glow);
            transition: all 0.25s ease;
            margin-top: 24px;
            text-decoration: none;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px var(--primary-accent-glow);
        }

        .notice-badge {
            background: rgba(204, 255, 0, 0.08);
            border: 1px solid var(--primary-accent);
            color: var(--primary-accent);
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 0.84rem;
            line-height: 1.5;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .error-badge {
            background: rgba(255, 82, 82, 0.1);
            border: 1px solid #ff5252;
            color: #ff5252;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 0.84rem;
            line-height: 1.5;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
    </style>
</head>
<body>

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
                <h3 style="color:#fff; font-size:1.35rem; font-weight:800; margin-top:4px;">Set New Password</h3>
                <p class="auth-sub">Choose a strong, confidential password to safeguard your portfolio.</p>
            </div>

            <?php if (!empty($msg)) { ?>
            <div class="notice-badge">
                <i class="fa-solid fa-circle-check font-size-18" style="margin-top:2px;"></i>
                <div><?php echo $msg; ?></div>
            </div>
            <?php } ?>

            <?php if (!empty($err)) { ?>
            <div class="error-badge">
                <i class="fa-solid fa-circle-exclamation font-size-18" style="margin-top:2px;"></i>
                <div><?php echo htmlspecialchars($err); ?></div>
            </div>
            <?php } ?>

            <?php if ($link_expired) { ?>
            <div class="error-badge">
                <i class="fa-solid fa-clock font-size-18" style="margin-top:2px;"></i>
                <div>
                    <strong>Reset Link Expired or Invalid</strong>
                    <p style="margin-top:4px; font-size:0.8rem; color:#fca5a5;">This password reset session has expired or has already been used.</p>
                </div>
            </div>
            <a href="forgot-password.php" class="btn-submit" style="margin-top:14px;">
                <i class="fa-solid fa-rotate-right"></i> Request New Reset Link
            </a>
            <?php } elseif (empty($msg)) { ?>
            <form action="reset-password.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($param_token); ?>">

                <div class="form-group">
                    <label class="form-label" for="resetEmail">Registered Email</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input type="email" id="resetEmail" name="email" value="<?php echo htmlspecialchars($param_email); ?>" class="form-control" placeholder="your@email.com" required <?php echo !empty($param_email) ? 'readonly' : ''; ?>>
                    </div>
                </div>

                <?php if (empty($param_token)) { ?>
                <div class="form-group">
                    <label class="form-label" for="otpCode">6-Digit Verification Code</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-shield-halved input-icon"></i>
                        <input type="text" id="otpCode" name="otp_code" value="<?php echo htmlspecialchars($param_otp); ?>" class="form-control" placeholder="e.g. 582914" maxlength="10" required>
                    </div>
                </div>
                <?php } ?>

                <div class="form-group">
                    <label class="form-label" for="newPassword">New Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="At least 6 characters" minlength="6" required autofocus>
                        <button type="button" class="pwd-toggle" onclick="togglePwdVisibility('newPassword', 'eye1')">
                            <i class="fa-regular fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirmPassword">Confirm New Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-shield-check input-icon"></i>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Re-type new password" minlength="6" required>
                        <button type="button" class="pwd-toggle" onclick="togglePwdVisibility('confirmPassword', 'eye2')">
                            <i class="fa-regular fa-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="do_reset" class="btn-submit">
                    <span>Save & Update Password</span>
                    <i class="fa-solid fa-check"></i>
                </button>
            </form>
            <?php } ?>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; font-size:0.86rem;">
                <a href="signin.php" style="color:var(--primary-accent); text-decoration:none; font-weight:700;"><i class="fa-solid fa-arrow-left me-1"></i> Sign In</a>
                <a href="register.php" style="color:var(--text-muted); text-decoration:none;">Open New Account</a>
            </div>

        </div>
    </div>

    <script>
    function togglePwdVisibility(inputId, eyeId) {
        const inp = document.getElementById(inputId);
        const icon = document.getElementById(eyeId);
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            inp.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }

    // Canvas Background
    (function() {
        const canvas = document.getElementById('matrixBg');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        const accent = '<?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : "#ccff00"; ?>';

        function resize() { width = canvas.width = window.innerWidth; height = canvas.height = window.innerHeight; }
        function init() {
            particles = [];
            for (let i = 0; i < 40; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.4,
                    vy: (Math.random() - 0.5) * 0.4,
                    r: Math.random() * 2 + 1,
                    alpha: Math.random() * 0.5 + 0.2
                });
            }
        }
        function draw() {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0) p.x = width; if (p.x > width) p.x = 0;
                if (p.y < 0) p.y = height; if (p.y > height) p.y = 0;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = accent;
                ctx.globalAlpha = p.alpha;
                ctx.fill();
            }
            requestAnimationFrame(draw);
        }
        window.addEventListener('resize', () => { resize(); init(); });
        resize(); init(); draw();
    })();
    </script>
</body>
</html>
