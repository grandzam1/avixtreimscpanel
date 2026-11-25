<?php 
session_start();
include 'db.php';
include 'config.php';

$err = "";
$msg = "";
$dev_reset_url = "";
$dev_otp = "";

if (isset($_POST['request_reset'])) {
    $identity = mysqli_real_escape_string($link, trim($_POST['identity'] ?? ''));

    if (empty($identity)) {
        $err = "Please enter your registered email address or username.";
    } else {
        $q_usr = mysqli_query($link, "SELECT id, username, email, fullname FROM users WHERE email = '$identity' OR username = '$identity' LIMIT 1");
        
        if ($q_usr && mysqli_num_rows($q_usr) > 0) {
            $u_row = mysqli_fetch_assoc($q_usr);
            $user_id = intval($u_row['id']);
            $u_name  = mysqli_real_escape_string($link, $u_row['username']);
            $u_email = mysqli_real_escape_string($link, $u_row['email']);

            // Generate secure 64-char token & 6-digit OTP code
            $token = bin2hex(random_bytes(32));
            $otp   = strval(mt_rand(100000, 999999));

            // Record reset request in database (valid for 60 minutes)
            $q_ins = mysqli_query($link, "INSERT INTO password_resets 
                (user_id, username, email, token, otp_code, expires_at) 
                VALUES 
                ('$user_id', '$u_name', '$u_email', '$token', '$otp', DATE_ADD(NOW(), INTERVAL 1 HOUR))");

            if ($q_ins) {
                // Build dynamic reset URL
                $base_url = !empty($siteurl) ? rtrim($siteurl, '/') : 'http://localhost:8000';
                $reset_url = $base_url . "/reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($u_row['email']);
                $dev_reset_url = "reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($u_row['email']);
                $dev_otp = $otp;

                // Send email using platform mail helper
                $subject = "Password Reset Request - " . $sitename;
                $display_user = !empty($u_row['fullname']) ? $u_row['fullname'] : $u_row['username'];
                $mail_text = "Hello " . $display_user . ",\n\n"
                           . "We received a request to reset the password for your account on " . $sitename . ".\n\n"
                           . "Click the link below to set a new password:\n"
                           . $reset_url . "\n\n"
                           . "Alternatively, you can enter this 6-digit verification code on the reset page: " . $otp . "\n\n"
                           . "This link and code will remain valid for 60 minutes.\n\n"
                           . "If you did not request this, you can safely ignore this email. Your account remains completely protected.";

                if (function_exists('send_platform_email')) {
                    send_platform_email($u_row['email'], $display_user, $subject, $mail_text);
                }

                $msg = "Password reset instructions and verification code have been dispatched to <strong>" . htmlspecialchars($u_row['email']) . "</strong>. Please check your inbox or spam folder.";
            } else {
                $err = "Database error generating reset token: " . mysqli_error($link);
            }
        } else {
            $err = "No account found matching that email address or username. Please verify and try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Forgot Password - <?php echo htmlspecialchars($sitename); ?></title>
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
            margin-bottom: 20px;
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

        .auth-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 0.86rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-footer a:hover {
            text-decoration: underline;
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
                <h3 style="color:#fff; font-size:1.35rem; font-weight:800; margin-top:4px;">Recover Password</h3>
                <p class="auth-sub">Enter your registered email address or username to receive password reset instructions.</p>
            </div>

            <?php if (!empty($msg)) { ?>
            <div class="notice-badge">
                <i class="fa-solid fa-circle-check font-size-18" style="margin-top:2px;"></i>
                <div>
                    <div><?php echo $msg; ?></div>
                    <?php if (!empty($dev_reset_url)) { ?>
                    <div style="margin-top: 14px;">
                        <a href="<?php echo htmlspecialchars($dev_reset_url); ?>" class="btn-submit" style="padding: 11px 18px; font-size: 0.88rem; margin-top:0;">
                            <i class="fa-solid fa-key"></i> Proceed to Reset Password (OTP: <?php echo htmlspecialchars($dev_otp); ?>)
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <?php if (!empty($err)) { ?>
            <div class="error-badge">
                <i class="fa-solid fa-circle-exclamation font-size-18" style="margin-top:2px;"></i>
                <div><?php echo htmlspecialchars($err); ?></div>
            </div>
            <?php } ?>

            <?php if (empty($msg)) { ?>
            <form action="forgot-password.php" method="post">
                <div class="form-group">
                    <label class="form-label" for="identity">Registered Email or Username</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope-open-text input-icon"></i>
                        <input type="text" id="identity" name="identity" class="form-control" placeholder="Enter your email or username" required autofocus autocomplete="username">
                    </div>
                </div>

                <button type="submit" name="request_reset" class="btn-submit">
                    <span>Send Reset Instructions</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
            <?php } ?>

            <div class="auth-footer" style="display:flex; justify-content:space-between; align-items:center; margin-top:26px;">
                <a href="signin.php"><i class="fa-solid fa-arrow-left me-1"></i> Back to Sign In</a>
                <a href="register.php">Create Account</a>
            </div>

            <?php 
            $wa_forgot = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '15552345678');
            $clean_wa_f = preg_replace('/[^0-9]/', '', $wa_forgot);
            if (empty($clean_wa_f)) $clean_wa_f = '15552345678';
            $wa_url_f = "https://wa.me/" . $clean_wa_f . "?text=" . urlencode("Hello Admin, I need assistance resetting my password on " . $sitename);
            ?>
            <div style="text-align:center; margin-top:22px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.06);">
                <a href="<?php echo htmlspecialchars($wa_url_f); ?>" target="_blank" style="display:inline-flex; align-items:center; gap:8px; font-size:0.8rem; color:#25d366; text-decoration:none; font-weight:700;">
                    <i class="fa-brands fa-whatsapp font-size-16"></i> Need instant help? Chat Admin on WhatsApp
                </a>
            </div>

        </div>
    </div>

    <!-- Animated Particle Background Canvas -->
    <script>
    (function() {
        const canvas = document.getElementById('matrixBg');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        const particleCount = 45;
        const accent = '<?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : "#ccff00"; ?>';

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }

        function init() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
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
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0) p.x = width;
                if (p.x > width) p.x = 0;
                if (p.y < 0) p.y = height;
                if (p.y > height) p.y = 0;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = accent;
                ctx.globalAlpha = p.alpha;
                ctx.fill();
            }
            ctx.globalAlpha = 1.0;
            requestAnimationFrame(draw);
        }

        window.addEventListener('resize', () => { resize(); init(); });
        resize();
        init();
        draw();
    })();
    </script>
</body>
</html>
