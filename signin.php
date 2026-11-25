<?php 
session_start();
include 'db.php';
include 'config.php';

$err = "";
$wrn = "";
$msg = "";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $password = "";
    
if (isset($_POST['submit'])) {
    if (empty($_POST["email"])) {
        $wrn = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
    }
      
    if (empty($_POST["password"])) {
        $password_err = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    if ($email == "" || $password == "") {
        $err = "Email/Username or Password fields cannot be empty!";
    } else {
        $sql = mysqli_query($link, "SELECT id, email, password, username FROM users WHERE (email ='$email' OR username = '$email') AND password= '$password'");
        if (mysqli_num_rows($sql) > 0) {
            $data = mysqli_fetch_assoc($sql);
            $_SESSION['email'] = $data['email'];
            $_SESSION['id'] = $data['id'];
            $email = $data['email'];
            $username = $data['username'];

            mysqli_query($link, "UPDATE users SET last_access = NOW() WHERE email = '$email' ");

            $msg = "Login was successful! Redirecting to your dashboard...";
            echo "<meta http-equiv='refresh' Content='2; url=dashboard/customer.php' />";

            // Optional email notification
            if (file_exists("PHPMailer/PHPMailer.php")) {
                require_once "PHPMailer/PHPMailer.php";
                require_once 'PHPMailer/Exception.php';
                
                try {
                    $mail = new PHPMailer;
                    $mail->setFrom($sitemail, $sitename);
                    $mail->addAddress($email, $username);
                    $mail->isHTML(true);
                    $mail->Subject = "Security Alert: Login to " . $sitename;
                    $mail->Body = '
                    <div style="background: #0d120f; padding: 24px; font-family: sans-serif; color: #fff;">
                        <div style="background: #151c18; max-width: 540px; margin: 0 auto; border-radius: 16px; padding: 28px; border: 1px solid rgba(255,255,255,0.1);">
                            <h2 style="color: #ccff00; margin-top: 0;">Login Notification</h2>
                            <p>Dear <strong>'.$username.'</strong>,</p>
                            <p>Your account was logged in from IP ('.$_SERVER['REMOTE_ADDR'].') on '.date("F j, Y, g:i a").'.</p>
                            <p style="color: #999; font-size: 0.85rem;">If you did not authorize this login, please contact support immediately to lock your assets.</p>
                            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
                            <p style="color: #777; font-size: 0.75rem;">&copy; '.date("Y").' '.$sitename.'. All rights reserved.</p>
                        </div>
                    </div>';
                    @$mail->send();
                } catch (\Exception $e) {}
            }
        } else {
            $err = "Invalid email or password combination.";
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Investor Sign In - <?php echo htmlspecialchars($sitename); ?></title>
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
            padding: 24px 16px;
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
            max-width: 460px;
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
            margin-bottom: 28px;
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
            margin-bottom: 12px;
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

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-label);
            margin-bottom: 8px;
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
            font-size: 1rem;
            transition: color 0.2s ease;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--card-border);
            color: #ffffff;
            padding: 14px 16px 14px 46px;
            border-radius: 14px;
            font-size: 0.92rem;
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

        .pwd-toggle {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1rem;
            padding: 4px;
            transition: color 0.2s;
        }

        .pwd-toggle:hover {
            color: var(--text-main);
        }

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
            margin-top: 10px;
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
            margin-top: 24px;
            text-align: center;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 700;
            transition: opacity 0.2s;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.82rem;
            text-decoration: none;
            margin-top: 20px;
            transition: color 0.2s;
        }

        .back-home:hover {
            color: #090d16;
        }

        .notice-badge {
            background: rgba(0, 176, 116, 0.08);
            border: 1px solid var(--primary-accent);
            color: var(--primary-accent);
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
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
                <p class="auth-sub">Welcome back! Sign in to access your digital wealth portfolio.</p>
            </div>

            <?php if (isset($_GET['success'])) { ?>
            <div class="notice-badge">
                <i class="fa-solid fa-circle-check"></i> Registration successful! Please log in with your credentials.
            </div>
            <?php } ?>

            <?php if (isset($_GET['loginprompt'])) { ?>
            <div class="notice-badge" style="border-color:#ff5252; color:#ff5252; background:rgba(255,82,82,0.1);">
                <i class="fa-solid fa-lock"></i> Please sign in to access your account dashboard.
            </div>
            <?php } ?>

            <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success') { ?>
            <div class="notice-badge" style="border-color:#2ecc71; color:#2ecc71; background:rgba(46,204,113,0.1);">
                <i class="fa-solid fa-circle-check"></i> Password has been reset successfully! Please sign in with your new password.
            </div>
            <?php } ?>

            <form action="signin.php" method="post" id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="loginEmail">Email Address or Username</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user-shield input-icon"></i>
                        <input type="text" id="loginEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" placeholder="Email or Username" required autocomplete="username" autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <label class="form-label" for="loginPassword" style="margin-bottom:0;">Password</label>
                        <a href="forgot-password.php" style="font-size:0.75rem; color:var(--primary-accent); text-decoration:none; font-weight:700;">Forgot Password?</a>
                    </div>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="loginPassword" name="password" class="form-control" placeholder="••••••••••••" required autocomplete="current-password">
                        <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility()" title="Show/Hide Password">
                            <i class="fa-regular fa-eye" id="pwdEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-submit" id="submitBtn">
                    <span>Sign In to Portfolio</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account yet? <a href="register.php">Create Account</a>
            </div>

            <?php 
            $wa_signin = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '15552345678');
            $clean_wa_s = preg_replace('/[^0-9]/', '', $wa_signin);
            if (empty($clean_wa_s)) $clean_wa_s = '15552345678';
            $wa_url_s = "https://wa.me/" . $clean_wa_s . "?text=" . urlencode("Hello Admin, I need assistance accessing my account on " . $sitename);
            ?>
            <div style="text-align:center; margin-top:16px; margin-bottom:12px;">
                <a href="<?php echo htmlspecialchars($wa_url_s); ?>" target="_blank" style="display:inline-flex; align-items:center; gap:8px; font-size:0.82rem; color:#25d366; text-decoration:none; font-weight:700; background:rgba(37,211,102,0.1); border:1px solid rgba(37,211,102,0.3); padding:8px 16px; border-radius:20px; transition:all 0.2s;">
                    <i class="fa-brands fa-whatsapp font-size-16"></i> Need assistance? Chat Admin on WhatsApp
                </a>
            </div>

            <div style="text-align:center;">
                <a href="index.php" class="back-home">
                    <i class="fa-solid fa-arrow-left"></i> Return to Homepage
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if ($msg != "") { ?>
    <script>
        $(document).ready(function() {
            swal({
                title: "Welcome Back!",
                text: "<?php echo addslashes($msg); ?>",
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
                title: "Sign In Failed",
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
        function togglePasswordVisibility() {
            const input = document.getElementById('loginPassword');
            const icon = document.getElementById('pwdEyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

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
