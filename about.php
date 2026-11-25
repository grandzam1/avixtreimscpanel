<?php 
include("front-config.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <!-- Dynamic High-Impact Google SEO & Meta Engine -->
    <?php echo render_site_seo("About Us - Institutional Digital Wealth | " . $site_name, "Discover " . $site_name . ": premier global digital asset platform providing quantitative market analysis, cold-storage custody, and institutional portfolio solutions."); ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary-accent: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : '#00b074'; ?>;
            --primary-accent-glow: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) . '44' : 'rgba(0, 176, 116, 0.28)'; ?>;
            --accent-crimson: #e82127;
            --accent-cyan: #0284c7;

            /* Deep Obsidian & Space Dark (Dominant Tone) */
            --bg-dark-canvas: #090e0b;
            --bg-dark-surface: #0e1411;
            --bg-dark-card: #131b16;
            --bg-dark-card-hover: #1b2620;
            --card-dark-border: rgba(255, 255, 255, 0.09);
            --card-dark-border-strong: rgba(255, 255, 255, 0.16);
            --card-dark-shadow: 0 18px 40px rgba(0, 0, 0, 0.55);
            --text-dark-main: #ffffff;
            --text-dark-muted: #94a3b8;
            --text-dark-dim: #64748b;

            /* Luminous Frosted Platinum & Titanium (Breath of Fresh Air) */
            --bg-light-canvas: #f8fafc;
            --bg-light-surface: #f1f5f9;
            --bg-light-card: #ffffff;
            --card-light-border: rgba(226, 232, 240, 0.9);
            --card-light-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.08), 0 0 1px rgba(15, 23, 42, 0.1);
            --text-light-main: #090d16;
            --text-light-muted: #475569;
            --text-light-dim: #64748b;

            --color-green: #059669;
            --color-red: #dc2626;
            --font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-dark-canvas);
            color: var(--text-dark-main);
            font-family: var(--font-family);
            overflow-x: hidden;
            line-height: 1.6;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ================= NAVIGATION (Dark Translucent Cockpit) ================= */
        .site-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: rgba(9, 14, 11, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--card-dark-border);
            z-index: 1000;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary-accent);
            box-shadow: 0 0 16px var(--primary-accent);
            animation: pulseDot 2s infinite ease-in-out;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); box-shadow: 0 0 12px var(--primary-accent); }
            50% { transform: scale(1.2); box-shadow: 0 0 24px var(--primary-accent); }
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-link {
            color: var(--text-dark-muted);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: color 0.25s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff;
        }

        .nav-cta-group {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-signin {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: 12px;
            border: 1px solid var(--card-dark-border-strong);
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.2s ease;
        }

        .btn-signin:hover {
            border-color: rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-signup {
            color: #000000;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 800;
            padding: 10px 22px;
            border-radius: 12px;
            background: var(--primary-accent);
            box-shadow: 0 4px 18px var(--primary-accent-glow);
            transition: all 0.25s ease;
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px var(--primary-accent-glow);
            filter: brightness(1.08);
            color: #000000;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .nav-links {
                display: none;
            }
            .mobile-toggle {
                display: block;
            }
        }

        /* ================= HERO SECTION (Dark Institutional Canvas) ================= */
        .about-hero {
            position: relative;
            padding: 170px 0 70px;
            text-align: center;
            overflow: hidden;
            background-color: var(--bg-dark-canvas);
        }

        #heroCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            opacity: 0.55;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 176, 116, 0.12);
            border: 1px solid rgba(0, 176, 116, 0.35);
            color: var(--primary-accent);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .hero-title {
            font-size: 3.4rem;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -1.5px;
            margin-bottom: 22px;
            color: #ffffff;
        }

        .hero-title span {
            color: var(--primary-accent);
            text-shadow: 0 0 35px var(--primary-accent-glow);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.3rem;
            }
        }

        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-dark-muted);
            line-height: 1.7;
            max-width: 760px;
            margin: 0 auto 36px;
        }

        /* ================= STATS BAR (Floating Luminous Platinum Island) ================= */
        .stats-bar {
            position: relative;
            z-index: 10;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 28px;
            padding: 34px 28px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 90px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        @media (max-width: 768px) {
            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.4rem;
            font-weight: 900;
            color: var(--primary-accent);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 0.82rem;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* =========================================================================
           BREATH OF FRESH AIR: MISSION & STORY (Luminous Polar Titanium Section)
           ========================================================================= */
        .story-section {
            padding: 90px 0 110px;
            background: linear-gradient(180deg, 
                #090e0b 0%, 
                #121b16 5%, 
                #edf2f7 16%, 
                #f8fafc 50%, 
                #edf2f7 84%, 
                #121b16 95%, 
                #090e0b 100%
            );
            color: var(--text-light-main);
            position: relative;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .story-grid {
                grid-template-columns: 1fr;
            }
        }

        .section-tag {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--primary-accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            display: block;
        }

        .section-heading {
            font-size: 2.4rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 22px;
            letter-spacing: -0.8px;
            color: var(--text-light-main);
        }

        .story-text {
            color: var(--text-light-muted);
            font-size: 1rem;
            margin-bottom: 18px;
            line-height: 1.8;
        }

        .story-highlight-box {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-left: 4px solid var(--primary-accent);
            padding: 22px 24px;
            border-radius: 0 18px 18px 0;
            margin: 24px 0;
            font-style: italic;
            color: #1e293b;
            box-shadow: var(--card-light-shadow);
        }

        .story-visual-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 28px;
            padding: 38px 32px;
            position: relative;
            box-shadow: var(--card-light-shadow);
        }

        .visual-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 24px;
        }

        .visual-item:last-child {
            margin-bottom: 0;
        }

        .visual-icon-box {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 14px;
            background: rgba(0, 176, 116, 0.12);
            color: var(--primary-accent);
            border: 1px solid rgba(0, 176, 116, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(0, 176, 116, 0.1);
        }

        .visual-title {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text-light-main);
            margin-bottom: 4px;
        }

        .visual-desc {
            font-size: 0.88rem;
            color: var(--text-light-muted);
            line-height: 1.5;
        }

        /* ================= 4 CORE PILLARS (Dark Security Vault) ================= */
        .pillars-section {
            padding: 90px 0 100px;
            background-color: var(--bg-dark-canvas);
            color: #ffffff;
        }

        .pillars-section h2 {
            color: #ffffff !important;
        }

        .pillars-section p {
            color: var(--text-dark-muted) !important;
        }

        .pillars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 48px;
        }

        .pillar-card {
            background: var(--bg-dark-card);
            border: 1px solid var(--card-dark-border);
            border-radius: 24px;
            padding: 34px 28px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: var(--card-dark-shadow);
        }

        .pillar-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary-accent);
            background: var(--bg-dark-card-hover);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.6), 0 0 20px var(--primary-accent-glow);
        }

        .pillar-icon {
            font-size: 2.2rem;
            color: var(--primary-accent);
            margin-bottom: 20px;
            display: inline-block;
        }

        .pillar-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .pillar-text {
            color: var(--text-dark-muted);
            font-size: 0.92rem;
            line-height: 1.7;
        }

        /* ================= CTA BANNER (Rich Dark Forest Gradient) ================= */
        .cta-banner {
            background: linear-gradient(135deg, #141f17 0%, #0a0e0c 100%);
            border: 1px solid var(--card-dark-border);
            border-radius: 32px;
            padding: 56px 40px;
            text-align: center;
            margin: 60px 0 100px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--card-dark-shadow);
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 176, 116, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .cta-banner h2 {
            font-size: 2.4rem;
            font-weight: 900;
            margin-bottom: 16px;
            position: relative;
            z-index: 2;
            color: #ffffff;
        }

        .cta-banner p {
            color: var(--text-dark-muted);
            font-size: 1.1rem;
            max-width: 680px;
            margin: 0 auto 30px;
            position: relative;
            z-index: 2;
        }

        .cta-btn-group {
            display: flex;
            justify-content: center;
            gap: 16px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        /* ================= FOOTER (Deep Obsidian Space) ================= */
        .site-footer {
            background: #060a08;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 70px 0 30px;
            color: var(--text-dark-muted);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -0.3px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 12px;
        }

        .footer-col a {
            color: var(--text-dark-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .footer-col a:hover {
            color: var(--primary-accent);
        }

        .footer-desc {
            color: var(--text-dark-muted);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-top: 14px;
            max-width: 320px;
        }

        .footer-bottom {
            padding-top: 28px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-dark-dim);
            flex-wrap: wrap;
            gap: 14px;
        }

        .footer-bottom a {
            color: var(--text-dark-muted);
            text-decoration: none;
        }

        .footer-bottom a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- ================= NAVIGATION ================= -->
    <header class="site-nav">
        <div class="container nav-inner">
            <a href="index.php" class="brand-logo">
                <?php if (!empty($logo) && file_exists($logo)) { ?>
                    <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-height: 44px; width: auto; object-fit: contain;">
                <?php } else { ?>
                    <span class="brand-dot"></span>
                    <span class="brand-title"><?php echo htmlspecialchars($site_name); ?></span>
                <?php } ?>
            </a>

            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="about.php" class="nav-link active">About Us</a>
                <a href="index.php#motion" class="nav-link">Network Architecture</a>
                <a href="index.php#plans" class="nav-link">Portfolio Plans</a>
                <a href="index.php#calculator" class="nav-link">Profit Calculator</a>
                <a href="index.php#faq" class="nav-link">FAQ</a>
            </nav>

            <div class="nav-cta-group">
                <?php 
                $wa_raw_about = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '15552345678');
                $clean_wa_about = preg_replace('/[^0-9]/', '', $wa_raw_about);
                if (empty($clean_wa_about)) $clean_wa_about = '15552345678';
                $wa_url_about = "https://wa.me/" . $clean_wa_about . "?text=" . urlencode("Hello Admin, I have an inquiry about " . $site_name);
                ?>
                <a href="<?php echo htmlspecialchars($wa_url_about); ?>" target="_blank" style="display:inline-flex; align-items:center; gap:8px; background:rgba(37,211,102,0.15); color:#25d366; border:1px solid rgba(37,211,102,0.4); padding:8px 16px; border-radius:12px; font-size:0.85rem; font-weight:700; text-decoration:none;" title="Chat with Admin">
                    <i class="fa-brands fa-whatsapp"></i> Chat with Admin
                </a>
                <a href="signin.php" class="btn-signin">Sign In</a>
                <a href="register.php" class="btn-signup">Open Account</a>
                <button class="mobile-toggle" onclick="toggleMobileNav()" title="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Nav Dropdown -->
    <div id="mobileNavMenu" style="display:none; position:fixed; top:80px; left:0; right:0; background:rgba(255,255,255,0.98); box-shadow:0 10px 30px rgba(0,0,0,0.08); border-bottom:1px solid var(--card-border); padding:24px; z-index:999; flex-direction:column; gap:16px;">
        <a href="index.php" class="nav-link">Home</a>
        <a href="about.php" class="nav-link active" style="color:var(--primary-accent);">About Us</a>
        <a href="index.php#plans" class="nav-link">Portfolio Plans</a>
        <a href="index.php#calculator" class="nav-link">Profit Calculator</a>
        <a href="signin.php" class="nav-link">Sign In</a>
        <a href="register.php" class="nav-link" style="color:var(--primary-accent); font-weight:bold;">Create Account</a>
    </div>

    <!-- ================= HERO ================= -->
    <section class="about-hero">
        <canvas id="heroCanvas"></canvas>
        <div class="container hero-content">
            <div class="badge-pill">
                <i class="fa-solid fa-building-columns"></i> Corporate Governance & Institutional Custody
            </div>
            <h1 class="hero-title">
                Pioneering Global Wealth Through <span>Algorithmic Crypto</span> Intelligence
            </h1>
            <p class="hero-desc">
                Founded with a mission to bridge institutional financial discipline and exponential digital asset markets, <?php echo htmlspecialchars($site_name); ?> manages client portfolios with multi-venue high-frequency execution and military-grade cold storage.
            </p>
            <div style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
                <a href="register.php" class="btn-signup" style="padding:14px 32px; font-size:1rem;">
                    Register Now <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
                <a href="index.php#plans" class="btn-signin" style="padding:14px 28px; font-size:1rem;">
                    View Our Strategies
                </a>
            </div>
        </div>
    </section>

    <!-- ================= STATS BAR ================= -->
    <div class="container">
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number">$480M+</div>
                <div class="stat-label">Quarterly Volume</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">48,000+</div>
                <div class="stat-label">Active Platform Clients</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">99.98%</div>
                <div class="stat-label">Custody Uptime</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">120+</div>
                <div class="stat-label">Supported Nations</div>
            </div>
        </div>
    </div>

    <!-- ================= MISSION & STORY ================= -->
    <section class="story-section">
        <div class="container">
            <div class="story-grid">
                <div>
                    <span class="section-tag">Our History & Philosophy</span>
                    <h2 class="section-heading">Disciplined Portfolio Management in Modern Markets</h2>
                    <p class="story-text">
                        <?php echo htmlspecialchars($site_name); ?> is an independent, privately-managed digital asset wealth advisory firm headquartered at <?php echo htmlspecialchars($address); ?>, with international trading operations in Zurich, London, and Singapore.
                    </p>
                    <p class="story-text">
                        Our quantitative desk combines active statistical arbitrage, cross-exchange liquidity provision, and automated market making to yield consistent returns across both bull and bear market cycles.
                    </p>
                    <div class="story-highlight-box">
                        "We believe risk management is just as important as growing capital. Every client dollar is segregated, backed by proof-of-reserves, and protected by multi-signature offline hardware vaults."
                    </div>
                    <p class="story-text">
                        Whether you are an individual participant starting with our Starter Strategy or an institutional partner participating in our Deluxe Strategy, your portfolio benefits from 24/7 algorithmic oversight and instant withdrawal capabilities.
                    </p>
                </div>

                <div class="story-visual-card">
                    <div class="visual-item">
                        <div class="visual-icon-box">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="visual-title">FIPS 140-2 Level 3 Cold Vaults</div>
                            <div class="visual-desc">98% of all digital reserves are kept in air-gapped, geographically distributed multi-signature vaults with Swiss bank-grade protocols.</div>
                        </div>
                    </div>

                    <div class="visual-item">
                        <div class="visual-icon-box">
                            <i class="fa-solid fa-microchip"></i>
                        </div>
                        <div>
                            <div class="visual-title">Sub-Millisecond Execution</div>
                            <div class="visual-desc">Direct low-latency optical connections to global Tier-1 liquidity venues ensuring zero slippage and instantaneous order settlement.</div>
                        </div>
                    </div>

                    <div class="visual-item">
                        <div class="visual-icon-box">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="visual-title">Compound Daily Yields</div>
                            <div class="visual-desc">Automated algorithmic profit distribution directly to user account balances with complete real-time withdrawal rights.</div>
                        </div>
                    </div>

                    <div class="visual-item">
                        <div class="visual-icon-box">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <div class="visual-title">24/7 Dedicated Account Desk</div>
                            <div class="visual-desc">Direct access to portfolio managers, instant live chat assistance, and VIP advisory services at any time.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 4 CORE PILLARS ================= -->
    <section class="pillars-section">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto;">
                <span class="section-tag">Institutional Edge</span>
                <h2 class="section-heading">Built on Four Uncompromising Pillars</h2>
                <p style="color: var(--text-muted); font-size: 1.05rem;">
                    How <?php echo htmlspecialchars($site_name); ?> continuously delivers outsized performance while safeguarding member principal.
                </p>
            </div>

            <div class="pillars-grid">
                <div class="pillar-card">
                    <div class="pillar-icon"><i class="fa-solid fa-lock"></i></div>
                    <h3 class="pillar-title">100% Reserve Solvency</h3>
                    <p class="pillar-text">
                        Every satoshi and dollar deposited is strictly held 1:1 on our balance sheets. We never engage in re-hypothecation or unsecured lending, ensuring your capital is always available.
                    </p>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon"><i class="fa-solid fa-sliders"></i></div>
                    <h3 class="pillar-title">Dynamic Hedging</h3>
                    <p class="pillar-text">
                        Our automated risk mitigation engine dynamically hedges spot portfolios using decentralized perpetual futures during unexpected market flash crashes to protect your principal.
                    </p>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon"><i class="fa-solid fa-coins"></i></div>
                    <h3 class="pillar-title">Instant Admin-Approved Payouts</h3>
                    <p class="pillar-text">
                        Withdraw your verified earnings in Bitcoin, Ethereum, Tether USDT, or direct wire settlement with swift admin authorization and zero hidden penalty fees.
                    </p>
                </div>

                <div class="pillar-card">
                    <div class="pillar-icon"><i class="fa-solid fa-fingerprint"></i></div>
                    <h3 class="pillar-title">Multi-Layer Security</h3>
                    <p class="pillar-text">
                        From personal withdrawal PIN protection and mandatory account verification to cryptographic session tokens, your assets remain fortified against unauthorized access.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= CTA BANNER ================= -->
    <div class="container">
        <div class="cta-banner">
            <h2>Experience Advanced Technology with <?php echo htmlspecialchars($site_name); ?></h2>
            <p>Join over 48,000 verified clients utilizing institutional crypto execution. Create your account in under two minutes.</p>
            <div class="cta-btn-group">
                <a href="register.php" class="btn-signup" style="padding: 15px 36px; font-size: 1.05rem;">
                    Get Started <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
                <a href="signin.php" class="btn-signin" style="padding: 15px 32px; font-size: 1.05rem;">
                    Sign In
                </a>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Branding -->
                <div>
                    <a href="index.php" class="brand-logo">
                        <?php if (!empty($logo) && file_exists($logo)) { ?>
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-height: 40px; width: auto; object-fit: contain;">
                        <?php } else { ?>
                            <span class="brand-dot"></span>
                            <span class="brand-title"><?php echo htmlspecialchars($site_name); ?></span>
                        <?php } ?>
                    </a>
                    <p class="footer-desc">
                        <?php echo htmlspecialchars($site_name); ?> provides institutional-level digital asset trading, high-frequency execution, and secure portfolio management tailored for private and corporate clients worldwide.
                    </p>
                </div>

                <!-- Navigation -->
                <div class="footer-col">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="index.php#plans">Portfolio Plans</a></li>
                        <li><a href="index.php#calculator">Profit Calculator</a></li>
                        <li><a href="index.php#faq">Frequently Asked</a></li>
                    </ul>
                </div>

                <!-- Account -->
                <div class="footer-col">
                    <h4>Client Portal</h4>
                    <ul>
                        <li><a href="signin.php">Sign In</a></li>
                        <li><a href="register.php">Create Account</a></li>
                        <li><a href="dashboard/customer.php">Member Dashboard</a></li>
                        <li><a href="dashboard/deposit.php">Deposit Gateway</a></li>
                        <li><a href="dashboard/plans.php">Active Plans</a></li>
                    </ul>
                </div>

                <!-- Contact Details -->
                <div class="footer-col">
                    <h4>Official Contact</h4>
                    <ul>
                        <li style="display:flex; align-items:center; gap:10px; color:var(--text-muted); font-size:0.88rem;">
                            <i class="fa-solid fa-envelope" style="color:var(--primary-accent);"></i>
                            <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a>
                        </li>
                        <li style="display:flex; align-items:flex-start; gap:10px; color:var(--text-muted); font-size:0.88rem; margin-top:10px;">
                            <i class="fa-solid fa-location-dot" style="color:var(--primary-accent); margin-top:4px;"></i>
                            <span><?php echo htmlspecialchars($address); ?></span>
                        </li>
                        <?php if (!empty($phone)) { ?>
                        <li style="display:flex; align-items:center; gap:10px; color:var(--text-muted); font-size:0.88rem; margin-top:10px;">
                            <i class="fa-solid fa-phone" style="color:var(--primary-accent);"></i>
                            <a href="tel:<?php echo htmlspecialchars($phone); ?>"><?php echo htmlspecialchars($phone); ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. All rights reserved. Institutional Digital Asset Management.
                </div>
                <div style="display:flex; gap:20px;">
                    <a href="index.php#faq">Security Standards</a>
                    <a href="index.php#faq">Terms of Service</a>
                    <a href="index.php#faq">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Direct Admin WhatsApp Floating Action -->
    <a href="<?php echo htmlspecialchars($wa_url_about); ?>" target="_blank" style="position: fixed; bottom: 25px; right: 25px; width: 60px; height: 60px; border-radius: 50%; background: #25d366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.9rem; box-shadow: 0 8px 25px rgba(37, 211, 102, 0.45); z-index: 9999; text-decoration: none;" title="Chat with Admin on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- 3D Geometric Motion Background Script -->
    <script>
        function toggleMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
        }

        (function() {
            const canvas = document.getElementById('heroCanvas');
            const ctx = canvas.getContext('2d');
            let width, height;
            let nodes = [];
            const nodeCount = 50;
            const accentColor = '<?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : "#ccff00"; ?>';

            function resize() {
                width = canvas.width = canvas.parentElement.offsetWidth;
                height = canvas.height = canvas.parentElement.offsetHeight;
            }

            function init() {
                nodes = [];
                for (let i = 0; i < nodeCount; i++) {
                    nodes.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        vx: (Math.random() - 0.5) * 0.9,
                        vy: (Math.random() - 0.5) * 0.9,
                        radius: Math.random() * 2.2 + 1,
                        alpha: Math.random() * 0.5 + 0.3
                    });
                }
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < nodes.length; i++) {
                    const n = nodes[i];
                    n.x += n.vx;
                    n.y += n.vy;

                    if (n.x < 0) n.x = width;
                    if (n.x > width) n.x = 0;
                    if (n.y < 0) n.y = height;
                    if (n.y > height) n.y = 0;

                    ctx.beginPath();
                    ctx.arc(n.x, n.y, n.radius, 0, Math.PI * 2);
                    ctx.fillStyle = accentColor;
                    ctx.globalAlpha = n.alpha;
                    ctx.fill();

                    for (let j = i + 1; j < nodes.length; j++) {
                        const n2 = nodes[j];
                        const dx = n.x - n2.x;
                        const dy = n.y - n2.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < 130) {
                            ctx.beginPath();
                            ctx.moveTo(n.x, n.y);
                            ctx.lineTo(n2.x, n2.y);
                            ctx.strokeStyle = accentColor;
                            ctx.globalAlpha = (1 - dist / 130) * 0.22;
                            ctx.stroke();
                        }
                    }
                }

                ctx.globalAlpha = 1.0;
                requestAnimationFrame(animate);
            }

            window.addEventListener('resize', () => {
                resize();
                init();
            });

            resize();
            init();
            animate();
        })();
    </script>
    <!-- Progressive Web App Controller -->
    <script src="pwa.js"></script>
</body>
</html>
