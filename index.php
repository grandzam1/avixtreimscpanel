<?php 
include("front-config.php"); 

// Prepare admin WhatsApp direct link
$wa_raw = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '+1 (555) 234-5678');
$clean_wa = preg_replace('/[^0-9]/', '', $wa_raw);
if (empty($clean_wa)) {
    $clean_wa = '15552345678';
}
$wa_url = "https://wa.me/" . $clean_wa . "?text=" . urlencode("Hello Admin, I would like to get started with " . $site_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <link rel="shortcut icon" href="images/bitcoin31.png" type="image/x-icon">
    
    <!-- Progressive Web App (PWA) & Native Mobile App Meta -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#090e0b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($site_name); ?>">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="192x192" href="images/icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="images/icons/icon-512.png">
    <link rel="stylesheet" href="pwa.css">
    
    <!-- Dynamic High-Impact Google SEO & Meta Engine -->
    <?php echo render_site_seo(); ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- High-Performance Modern Motion Styling with Refined Slate & Platinum Theme -->
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
            --text-dark-light: #e2e8f0;
            --text-dark-muted: #94a3b8;
            --text-dark-dim: #64748b;

            /* Luminous Frosted Platinum & Titanium (Breath of Fresh Air) */
            --bg-light-canvas: #f8fafc;
            --bg-light-surface: #f1f5f9;
            --bg-light-card: #ffffff;
            --bg-light-card-hover: #ffffff;
            --card-light-border: rgba(226, 232, 240, 0.9);
            --card-light-border-strong: rgba(203, 213, 225, 0.95);
            --card-light-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.08), 0 0 1px rgba(15, 23, 42, 0.1);
            --card-light-shadow-hover: 0 24px 50px -12px rgba(15, 23, 42, 0.14), 0 0 0 1px var(--primary-accent);
            --text-light-main: #090d16;
            --text-light-sub: #1e293b;
            --text-light-muted: #475569;
            --text-light-dim: #64748b;

            --color-green: #059669;
            --color-red: #dc2626;
            --font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;        }

        body {
            background-color: var(--bg-dark-canvas);
            color: var(--text-dark-main);
            font-family: var(--font-family);
            overflow-x: hidden;
            line-height: 1.6;        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;        }

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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;        }

        .brand-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary-accent);
            box-shadow: 0 0 16px var(--primary-accent);
            animation: pulseDot 2s infinite ease-in-out;        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); box-shadow: 0 0 12px var(--primary-accent); }
            50% { transform: scale(1.2); box-shadow: 0 0 24px var(--primary-accent); }        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #ffffff;        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 26px;        }

        .nav-link {
            color: var(--text-dark-muted);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: color 0.25s ease;        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff;        }

        .nav-cta-group {
            display: flex;
            align-items: center;
            gap: 12px;        }

        .btn-wa-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 211, 102, 0.15);
            color: #25d366;
            border: 1px solid rgba(37, 211, 102, 0.4);
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;        }

        .btn-wa-header:hover {
            background: #25d366;
            color: #ffffff;
            box-shadow: 0 0 16px rgba(37, 211, 102, 0.4);        }

        .btn-signin {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: 12px;
            border: 1px solid var(--card-dark-border-strong);
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.2s ease;        }

        .btn-signin:hover {
            border-color: rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;        }

        .btn-signup {
            color: #000000;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 800;
            padding: 10px 22px;
            border-radius: 12px;
            background: var(--primary-accent);
            box-shadow: 0 4px 18px var(--primary-accent-glow);
            transition: all 0.25s ease;        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px var(--primary-accent-glow);
            filter: brightness(1.08);
            color: #000000;        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;        }

        @media (max-width: 990px) {
            .nav-links, .btn-wa-header {
                display: none;        }
            .mobile-toggle {
                display: block;        }        }

        /* ================= CINEMATIC HERO SECTION (RESPONSIVE DESKTOP & MOBILE) ================= */
        .hero-section {
            position: relative;
            min-height: 82vh;
            padding: 130px 0 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
            text-align: center;
            background-color: var(--bg-dark-canvas);        }

        .hero-video-bg-wrap {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
            background: #090e0b url('images/tech-hero.jpg') center 55% / cover no-repeat;
        }

        /* Dual-Source Hardware Accelerated H.264 MP4 Video Elements */
        .hero-video-element {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            filter: contrast(1.06) brightness(1.02);
            transition: opacity 0.3s ease;
        }

        .hero-video-desktop {
            display: block;
            object-fit: cover;
            object-position: center 54%;
        }

        .hero-video-mobile {
            display: none;
            object-fit: cover;
            object-position: center center;
        }

        /* Subtle dark gradient overlay: readable text without dimming or fading the car */
        .hero-video-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(9, 14, 11, 0.45) 0%, rgba(9, 14, 11, 0.08) 22%, transparent 35%, transparent 75%, rgba(9, 14, 11, 0.88) 100%);
            z-index: 2;
            pointer-events: none;
        }

        .hero-container {
            position: relative;
            z-index: 3;
            max-width: 900px;
            margin: 0 auto;
            padding-top: 15px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(19, 27, 22, 0.85);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #ffffff;
            padding: 7px 20px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .live-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-accent);
            box-shadow: 0 0 10px var(--primary-accent);
            animation: pulseDot 1.8s infinite;
        }

        .hero-h1 {
            font-size: 3.6rem;
            font-weight: 900;
            line-height: 1.14;
            letter-spacing: -1.6px;
            margin-bottom: 14px;
            color: #ffffff;
            text-shadow: 0 4px 28px rgba(0, 0, 0, 0.95);
        }

        .hero-h1 span.glow-accent {
            color: var(--primary-accent);
            text-shadow: 0 0 28px var(--primary-accent-glow);
        }

        .hero-h1 span.accent-crimson {
            color: #e82127;
            text-shadow: 0 0 28px rgba(232, 33, 39, 0.5);
        }

        .hero-mini-sub {
            font-size: 1.12rem;
            color: #f1f5f9;
            font-weight: 600;
            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.9);
            margin-bottom: 24px;
            letter-spacing: 0.3px;        }

        .hero-mobile-spacer {
            display: none;        }

        .hero-cta-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;        }

        .btn-hero-lg {
            padding: 14px 34px;
            font-size: 1.05rem;
            font-weight: 800;        }

        .btn-hero-ghost {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 14px 26px;
            border-radius: 12px;
            border: 1px solid var(--card-dark-border-strong);
            background: rgba(255, 255, 255, 0.06);
            transition: all 0.25s ease;        }

        .btn-hero-ghost:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
            color: #ffffff;        }

        .btn-wa-hero {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #25d366;
            color: #ffffff;
            border: 1px solid rgba(37, 211, 102, 0.6);
            padding: 14px 26px;
            border-radius: 12px;
            font-size: 0.98rem;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 18px rgba(37, 211, 102, 0.3);        }

        .btn-wa-hero:hover {
            background: #1eb956;
            box-shadow: 0 0 24px rgba(37, 211, 102, 0.5);
            transform: translateY(-2px);
            color: #ffffff;        }

        /* Telemetry HUD Strip (Moved below hero so video is 100% visible) */
        .hero-telemetry-strip {
            position: relative;
            z-index: 10;
            margin-top: -36px;
            margin-bottom: 30px;        }

        .hero-telemetry-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin: 0 auto;
            max-width: 980px;        }

        .telemetry-pill-card {
            background: rgba(19, 27, 22, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-dark-border);
            border-radius: 18px;
            padding: 16px 12px;
            text-align: center;
            transition: all 0.25s ease;
            box-shadow: var(--card-dark-shadow);        }

        .telemetry-pill-card:hover {
            background: #1a251e;
            border-color: var(--primary-accent);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5), 0 0 15px var(--primary-accent-glow);        }

        .pill-metric-val {
            font-size: 1.55rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.1;
            margin-bottom: 4px;        }

        .pill-metric-label {
            font-size: 0.72rem;
            color: var(--text-dark-dim);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;        }

        .pill-sub-badge {
            font-size: 0.68rem;
            color: var(--primary-accent);
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;        }

        /* Live Market Ticker */
        .live-market-bar-wrapper {
            position: relative;
            z-index: 10;
            margin-top: -24px;
            margin-bottom: 50px;        }

        .market-ticker-container {
            display: flex;
            align-items: center;
            background: rgba(14, 20, 16, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-dark-border);
            border-radius: 18px;
            padding: 12px 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);        }

        .market-ticker-header {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #1a251e;
            border: 1px solid var(--card-dark-border-strong);
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #ffffff;
            flex-shrink: 0;
            margin-right: 20px;        }

        .live-indicator-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary-accent);
            box-shadow: 0 0 8px var(--primary-accent);
            animation: pulseDot 1.5s infinite;        }

        .market-ticker-marquee-window {
            flex: 1;
            overflow: hidden;
            position: relative;
            mask-image: linear-gradient(90deg, transparent 0%, #000 30px, #000 calc(100% - 30px), transparent 100%);
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, #000 30px, #000 calc(100% - 30px), transparent 100%);        }

        .market-ticker-stream {
            display: flex;
            align-items: center;
            gap: 40px;
            animation: scrollTickerStream 35s linear infinite;
            white-space: nowrap;        }

        @keyframes scrollTickerStream {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }        }

        .stream-coin-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 700;        }

        .stream-coin-item .coin-symbol {
            color: var(--text-dark-muted);
            font-size: 0.84rem;        }

        .stream-coin-item .coin-val {
            color: #ffffff;
            font-weight: 800;        }

        /* ================= DEDICATED MOBILE LAYOUT (BELOW 768px) ================= */
        @media (max-width: 767px) {
            .hero-section {
                min-height: 88vh;
                padding: calc(92px + env(safe-area-inset-top, 0px)) 16px 36px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                text-align: center;
            }

            .hero-video-desktop {
                display: none !important;
            }

            .hero-video-mobile {
                display: block !important;
                object-fit: cover;
                object-position: center center;
            }

            .hero-video-bg-wrap {
                background: #090e0b url('images/tech-hero-mobile.jpg') center center / cover no-repeat;
            }

            .hero-video-scrim {
                background: linear-gradient(180deg, 
                    rgba(9, 14, 11, 0.60) 0%, 
                    rgba(9, 14, 11, 0.10) 20%, 
                    transparent 28%, 
                    transparent 72%, 
                    rgba(9, 14, 11, 0.92) 100%
                );
            }

            .hero-container {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                height: 100%;
                padding: 0;
            }

            .hero-tag {
                padding: 5px 14px;
                font-size: 0.72rem;
                margin-bottom: 10px;
                letter-spacing: 0.4px;
            }

            .hero-h1 {
                font-size: 2.15rem;
                line-height: 1.18;
                letter-spacing: -0.8px;
                margin-bottom: 8px;
                text-shadow: 0 3px 16px rgba(0, 0, 0, 0.95);
            }

            .hero-mini-sub {
                font-size: 0.92rem;
                line-height: 1.45;
                margin-bottom: 0;
            }

            .hero-mobile-spacer {
                display: block;
                min-height: 140px;
                flex: 1;
                pointer-events: none;
            }

            .hero-cta-actions {
                flex-direction: column;
                gap: 12px;
                width: 100%;
                margin-top: auto;
            }

            .hero-cta-actions a {
                width: 100%;
                padding: 15px 20px;
                font-size: 1rem;
                font-weight: 800;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 52px;
            }

            .hero-telemetry-strip {
                margin-top: -15px;
                margin-bottom: 24px;
            }

            .hero-telemetry-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .telemetry-pill-card {
                padding: 12px 10px;
                border-radius: 14px;
            }

            .pill-metric-val {
                font-size: 1.25rem;
            }

            .market-ticker-header {
                padding: 5px 10px;
                font-size: 0.7rem;
                margin-right: 10px;
            }

            .stream-coin-item {
                font-size: 0.8rem;
                gap: 6px;
            }

            /* Responsive Single Straight Line Highway */
            .network-single-line-highway-wrap {
                padding: 30px 0 40px;
            }

            .single-line-track-road {
                height: 75px;
            }

            .single-car-unit .real-car-photo {
                height: 48px;
            }

            .single-car-unit {
                bottom: 10px;
            }

            .single-track-line-base {
                bottom: 8px;
            }

            .single-track-dashed-line {
                bottom: 14px;
            }

            .highway-wrapper {
                padding: 24px 14px;
                border-radius: 22px;
            }
        }

        /* ================= REDUCED MOTION PREFERENCES & STATIC FALLBACK ================= */
        @media (prefers-reduced-motion: reduce) {
            .hero-video-element {
                display: none !important;
            }
            .hero-video-bg-wrap {
                background: #090e0b url('images/tech-hero.jpg') center 55% / cover no-repeat !important;
            }
            @media (max-width: 767px) {
                .hero-video-bg-wrap {
                    background: #090e0b url('images/tech-hero-mobile.jpg') center center / cover no-repeat !important;
                }
            }
            .hero-video-scrim {
                background: rgba(9, 14, 11, 0.5) !important;
            }
            .market-ticker-stream {
                animation: none !important;
            }
        }

        /* ================= DEDICATED DESKTOP CINEMATIC ENHANCEMENTS (1024px AND ABOVE) ================= */
        @media (min-width: 1024px) {
            .hero-section {
                min-height: 86vh;
                max-height: 960px;
                padding: 140px 0 80px;
            }

            .hero-video-desktop {
                display: block !important;
                object-position: center 54%;
            }

            .hero-video-mobile {
                display: none !important;
            }

            .hero-video-bg-wrap {
                background: #090e0b url('images/tech-hero.jpg') center 55% / cover no-repeat;
            }

            .hero-h1 {
                font-size: 3.8rem;
                line-height: 1.12;
                letter-spacing: -1.8px;
            }

            .hero-mini-sub {
                font-size: 1.15rem;
                margin-bottom: 28px;
            }

            .hero-cta-actions {
                display: flex;
                flex-direction: row;
                justify-content: center;
                gap: 18px;
            }

            .hero-mobile-spacer {
                display: none !important;
            }
        }

        .stream-coin-item .coin-change.up {
            color: #2ecc71;
            font-size: 0.82rem;
            font-weight: 700;        }



        /* ================= AUTONOMOUS NETWORK TELEMETRY (DARK CINEMATIC RUNWAY) ================= */
        .network-runway-section {
            position: relative;
            padding: 60px 0 100px;
            background: #090e0b;
            overflow: hidden;        }

        .runway-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 36px;        }

        .runway-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(232, 33, 39, 0.12);
            border: 1px solid #e82127;
            color: #e82127;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 14px;        }

        .runway-title {
            font-size: 2.6rem;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 12px;
            color: #ffffff;        }

        .runway-title span {
            color: var(--primary-accent);        }

        .runway-desc {
            color: var(--text-dark-muted);
            font-size: 1.05rem;
            line-height: 1.6;        }

        .highway-wrapper {
            position: relative;
            background: linear-gradient(180deg, #141c17 0%, #0b110d 100%);
            border: 1px solid var(--card-dark-border);
            border-radius: 32px;
            padding: 40px 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.7), 0 0 40px var(--primary-accent-glow);
            overflow: hidden;        }

        .cyber-road {
            position: relative;
            width: 100%;
            height: 380px;
            background: #060907;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.9);        }

        /* ================= SINGLE STRAIGHT LINE FLEET CONVOY ================= */
        .network-single-line-highway-wrap {
            position: relative;
            padding: 42px 0 54px; /* Generous breathing space between Live Markets and Autonomous Graphics */
            background: transparent;
            overflow: hidden;
        }

        .single-line-track-road {
            position: relative;
            width: 100%;
            height: 95px;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
        }

        /* The Sleek Glowing Highway Line */
        .single-track-line-base {
            position: absolute;
            bottom: 12px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(0, 176, 116, 0.3) 10%, 
                rgba(204, 255, 0, 0.65) 30%, 
                rgba(255, 255, 255, 0.8) 50%, 
                rgba(232, 33, 39, 0.65) 70%, 
                rgba(0, 229, 255, 0.3) 90%, 
                transparent 100%
            );
            box-shadow: 0 0 14px rgba(204, 255, 0, 0.45), 0 0 25px rgba(0, 176, 116, 0.25);
            z-index: 1;
        }

        .single-track-dashed-line {
            position: absolute;
            bottom: 18px;
            left: 0;
            width: 200%;
            height: 2px;
            background: repeating-linear-gradient(90deg, 
                rgba(204, 255, 0, 0.45) 0px, 
                rgba(204, 255, 0, 0.45) 35px, 
                transparent 35px, 
                transparent 75px
            );
            animation: roadDash 3s linear infinite;
            z-index: 1;
        }

        @keyframes roadDash {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .single-track-neon-glow {
            position: absolute;
            bottom: 6px;
            left: 10%;
            right: 10%;
            height: 14px;
            background: radial-gradient(ellipse at center, rgba(0, 176, 116, 0.2) 0%, transparent 75%);
            filter: blur(6px);
            pointer-events: none;
            z-index: 1;
        }

        /* All 3 Cars Moving on ONE Straight Line */
        .single-car-unit {
            position: absolute;
            bottom: 14px;
            display: inline-block;
            z-index: 2;
            will-change: left;
            pointer-events: none;
        }

        .single-car-unit .real-car-photo {
            height: 62px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 10px 16px rgba(0, 0, 0, 0.95));
            user-select: none;
            -webkit-user-drag: none;
        }

        /* Convoy Staggered Left-to-Right Loop on a Single Straight Line */
        .convoy-truck {
            left: -240px;
            animation: singleLineCruise 14s linear infinite;
            animation-delay: 0s;
        }

        .convoy-models {
            left: -240px;
            animation: singleLineCruise 14s linear infinite;
            animation-delay: -4.66s;
        }

        .convoy-roadster {
            left: -240px;
            animation: singleLineCruise 14s linear infinite;
            animation-delay: -9.33s;
        }

        @keyframes singleLineCruise {
            0% {
                left: -240px;
            }
            100% {
                left: calc(100% + 50px);
            }
        }

        /* Headlight Flares */
        .single-car-unit .car-headlight-flare.cybertruck-flare {
            position: absolute;
            top: 25px;
            right: -55px;
            width: 85px;
            height: 24px;
            background: radial-gradient(ellipse at left, rgba(255, 255, 255, 0.95) 0%, rgba(0, 229, 255, 0.5) 35%, transparent 75%);
            pointer-events: none;
            filter: blur(3px);
        }

        .single-car-unit .car-headlight-flare.right {
            position: absolute;
            top: 20px;
            right: -55px;
            width: 80px;
            height: 28px;
            background: radial-gradient(ellipse at left, rgba(204, 255, 0, 0.45) 0%, rgba(204, 255, 0, 0.12) 40%, transparent 75%);
            pointer-events: none;
            filter: blur(3px);
        }

        .single-car-unit .car-headlight-flare.roadster-flare {
            background: radial-gradient(ellipse at left, rgba(232, 33, 39, 0.85) 0%, rgba(232, 33, 39, 0.2) 40%, transparent 75%) !important;
        }

        /* Ground Contact Shadow */
        .single-car-unit .real-car-shadow {
            position: absolute;
            bottom: -5px;
            left: 8px;
            right: 8px;
            height: 10px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 50%;
            filter: blur(4px);
            pointer-events: none;
        }

        .highway-hud-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 28px;        }

        .hud-stat-box {
            background: #141c17;
            border: 1px solid var(--card-dark-border);
            border-radius: 18px;
            padding: 16px 20px;
            box-shadow: var(--card-dark-shadow);        }

        .hud-label {
            font-size: 0.75rem;
            color: var(--text-dark-muted);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;        }

        .hud-val {
            font-size: 1.25rem;
            font-weight: 900;
            color: #ffffff;        }

        .hud-val.accent {
            color: var(--primary-accent);        }

        .hud-val.red {
            color: #ff5252;        }

        .highway-controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 20px;        }

        .btn-hud-action {
            background: #1a251e;
            border: 1px solid var(--card-dark-border);
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;        }

        .btn-hud-action:hover, .btn-hud-action.active {
            border-color: var(--primary-accent);
            color: #000;
            background: var(--primary-accent);        }

        /* ================= INSTITUTIONAL INFRASTRUCTURE & EXECUTION (Dark High-Tech) ================= */
        .network-ecosystem-section {
            padding: 60px 0 90px;
            background: #0d1310;
            border-top: 1px solid var(--card-dark-border);        }

        .ecosystem-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;        }

        @media (max-width: 900px) {
            .ecosystem-grid {
                grid-template-columns: 1fr;        }        }

        .eco-video-card {
            background: #141c17;
            border: 1px solid var(--card-dark-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--card-dark-shadow);
            margin-bottom: 24px;        }

        .eco-video-frame {
            width: 100%;
            height: 240px;
            border: none;
            display: block;        }

        .eco-video-caption {
            padding: 16px 20px;
            font-size: 0.88rem;
            color: #ffffff;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0e1411;
            border-top: 1px solid var(--card-dark-border);        }

        /* ================= 4 CORE PORTFOLIO STRATEGIES (DARK LUXURY) ================= */
        .plans-section {
            padding: 90px 0 110px;
            background-color: var(--bg-dark-canvas);
            color: #ffffff;
            position: relative;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(204, 255, 0, 0.05) 0%, transparent 45%),
                radial-gradient(circle at 80% 70%, rgba(0, 229, 255, 0.04) 0%, transparent 50%);
            border-top: 1px solid var(--card-dark-border);
            border-bottom: 1px solid var(--card-dark-border);
        }

        .plans-section h2 {
            color: #ffffff !important;
        }

        .plans-section p {
            color: var(--text-dark-muted) !important;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 24px;
            margin-top: 48px;
        }

        .plan-card {
            background: var(--bg-dark-card);
            border: 1px solid var(--card-dark-border);
            border-radius: 26px;
            padding: 34px 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: var(--card-dark-shadow);
        }

        .plan-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-accent);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7), 0 0 25px var(--primary-accent-glow);
        }

        .plan-card h3 {
            color: #ffffff !important;
        }

        .plan-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-dark-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 14px;
            text-transform: uppercase;
        }

        .plan-badge.featured {
            background: var(--primary-accent);
            color: #000000;
            border-color: var(--primary-accent);
        }

        .plan-roi {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--primary-accent);
            line-height: 1.1;
            margin-bottom: 6px;
        }

        .plan-roi span {
            font-size: 1rem;
            color: var(--text-dark-muted);
            font-weight: 600;
        }

        .plan-specs {
            list-style: none;
            margin: 24px 0 28px;
            padding-top: 18px;
            border-top: 1px solid var(--card-dark-border);
        }

        .plan-specs li {
            font-size: 0.88rem;
            color: var(--text-dark-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-specs li strong {
            color: #ffffff;
        }

        .plan-specs li i {
            color: var(--primary-accent);
        }

        .btn-plan-select {
            display: block;
            text-align: center;
            padding: 13px;
            border-radius: 14px;
            background: #18221c;
            border: 1px solid var(--card-dark-border);
            color: #ffffff;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.95rem;
            transition: all 0.25s;
        }

        .btn-plan-select:hover {
            background: var(--primary-accent);
            color: #000000;
            border-color: var(--primary-accent);
            box-shadow: 0 6px 20px var(--primary-accent-glow);
        }

        /* 3D Profit Calculator Inside Dark Section */
        .calculator-card {
            background: linear-gradient(135deg, #141c17 0%, #0d1410 100%);
            border: 1px solid var(--card-dark-border);
            border-radius: 32px;
            padding: 44px 36px;
            margin: 60px 0 30px;
            box-shadow: var(--card-dark-shadow);
            color: #ffffff;
        }

        .calculator-card h3 {
            color: #ffffff !important;
        }

        /* ================= SERVICES SECTION (Dark Banking Vault) ================= */
        .services-section {
            padding: 90px 0;
            background-color: var(--bg-dark-canvas);
            color: #ffffff;
            border-bottom: 1px solid var(--card-dark-border);        }

        .services-section h2 {
            color: #ffffff;        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 40px;        }

        .service-card {
            background: var(--bg-dark-card);
            border: 1px solid var(--card-dark-border);
            border-radius: 22px;
            padding: 30px 24px;
            transition: all 0.25s;
            box-shadow: var(--card-dark-shadow);        }

        .service-card:hover {
            border-color: var(--primary-accent);
            transform: translateY(-5px);
            background: var(--bg-dark-card-hover);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.6), 0 0 15px var(--primary-accent-glow);        }

        .service-card h3 {
            color: #ffffff !important;        }

        .service-card p {
            color: var(--text-dark-muted) !important;        }

        .service-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: rgba(0, 176, 116, 0.15);
            color: var(--primary-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;        }

        /* =========================================================================
           BREATH OF FRESH AIR: LEADERSHIP & GLOBAL INVESTOR REVIEWS
           (Luminous Polar Titanium Atmosphere with High-Contrast Pods)
           ========================================================================= */
        .testimonials-section {
            padding: 100px 0 110px;
            background: linear-gradient(180deg, 
                #090e0b 0%, 
                #121b16 5%, 
                #edf2f7 16%, 
                #f8fafc 50%, 
                #edf2f7 84%, 
                #121b16 95%, 
                #090e0b 100%
            );
            color: #090d16;
            position: relative;
        }

        .testimonials-section h2 {
            color: #090d16 !important;
        }

        .testi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .testi-card {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(203, 213, 225, 0.95);
            border-radius: 24px;
            padding: 30px 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 18px 45px -10px rgba(15, 23, 42, 0.12), 0 0 1px rgba(15, 23, 42, 0.2);
            transition: all 0.25s ease;
        }

        .testi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 50px -10px rgba(15, 23, 42, 0.18);
            border-color: var(--primary-accent);
        }

        .testi-quote {
            font-style: italic;
            color: #334155;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .testi-author {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .testi-author strong {
            color: #090d16 !important;
        }

        /* Solid Dark Contrast Badge Pill: Ensures brand & initials pop with 100% contrast */
        .testi-avatar-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #090e0b;
            border: 2px solid var(--primary-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--primary-accent);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }

        /* ================= FAQ SECTION (Dark Obsidian Accordion) ================= */
        .faq-section {
            padding: 90px 0;
            background-color: var(--bg-dark-canvas);
            color: #ffffff;        }

        .faq-section h2 {
            color: #ffffff !important;        }

        .faq-item {
            background: #131b16;
            border: 1px solid var(--card-dark-border);
            border-radius: 18px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
            transition: all 0.2s ease;        }

        .faq-item:hover {
            border-color: rgba(255, 255, 255, 0.18);        }

        .faq-question {
            padding: 20px 24px;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #ffffff;        }

        .faq-answer {
            padding: 0 24px 22px;
            color: var(--text-dark-muted);
            font-size: 0.94rem;
            line-height: 1.7;
            display: none;        }

        .faq-item.active {
            border-color: var(--primary-accent);        }

        .faq-item.active .faq-answer {
            display: block;        }

        .faq-item.active .faq-toggle-icon {
            transform: rotate(180deg);
            color: var(--primary-accent);        }

        /* ================= FOOTER (Deep Obsidian Space Finish) ================= */
        .site-footer {
            background: #060a08;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 70px 0 30px;
            color: var(--text-dark-muted);        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 50px;        }

        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;        }        }

        @media (max-width: 600px) {
            .footer-grid {
                grid-template-columns: 1fr;        }        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 20px;        }

        .footer-col ul {
            list-style: none;        }

        .footer-col li {
            margin-bottom: 12px;        }

        .footer-col a {
            color: var(--text-dark-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;        }

        .footer-col a:hover {
            color: var(--primary-accent);        }

        .footer-bottom {
            padding-top: 28px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-dark-dim);
            flex-wrap: wrap;
            gap: 14px;        }

        .footer-bottom a {
            color: var(--text-dark-muted);
            text-decoration: none;        }

        .footer-bottom a:hover {
            color: #ffffff;        }

        /* Floating Admin WhatsApp Button */
        .floating-admin-wa {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #25d366;
            color: #ffffff;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.45);
            z-index: 9999;
            text-decoration: none;
            transition: all 0.3s;
            animation: pulseWa 2.5s infinite;        }

        .floating-admin-wa:hover {
            transform: scale(1.1);
            filter: brightness(1.1);
            color: #ffffff;        }

        @keyframes pulseWa {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.6); }
            50% { box-shadow: 0 0 0 16px rgba(37, 211, 102, 0); }        }
    </style>
</head>
<body>

    <!-- ================= TOP NAVIGATION ================= -->
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
                <a href="index.php" class="nav-link active">Home</a>
                <a href="#hero-showcase" class="nav-link"><i class="fa-solid fa-microchip me-1" style="color:#00e5ff;"></i> Architecture</a>
                <a href="#network-telemetry" class="nav-link">Network Telemetry</a>
                <a href="#plans" class="nav-link">Portfolio Plans</a>
                <a href="#calculator" class="nav-link">Calculator</a>
                <a href="about.php" class="nav-link">About Us</a>
                <a href="#faq" class="nav-link">FAQ</a>
            </nav>

            <div class="nav-cta-group">
                <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" class="btn-wa-header" title="Chat Directly with Admin">
                    <i class="fa-brands fa-whatsapp font-size-16"></i> Chat with Admin
                </a>
                <a href="signin.php" class="btn-signin">Sign In</a>
                <a href="register.php" class="btn-signup">Open Account</a>
                <button class="mobile-toggle" onclick="toggleMobileNav()" title="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Dropdown -->
    <div id="mobileNavMenu" style="display:none; position:fixed; top:80px; left:0; right:0; background:#121922; border-bottom:1px solid var(--card-border); padding:24px; z-index:999; flex-direction:column; gap:16px;">
        <a href="index.php" class="nav-link active">Home</a>
        <a href="#hero-showcase" class="nav-link" onclick="toggleMobileNav()"><i class="fa-solid fa-microchip me-1" style="color:#00e5ff;"></i> Architecture</a>
        <a href="#network-telemetry" class="nav-link" onclick="toggleMobileNav()">Network Telemetry</a>
        <a href="#plans" class="nav-link" onclick="toggleMobileNav()">Portfolio Plans</a>
        <a href="#calculator" class="nav-link" onclick="toggleMobileNav()">Calculator</a>
        <a href="about.php" class="nav-link">About Us</a>
        <a href="#faq" class="nav-link" onclick="toggleMobileNav()">FAQ</a>
        <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" class="nav-link" style="color:#25d366; font-weight:bold;">
            <i class="fa-brands fa-whatsapp"></i> Chat Directly with Admin
        </a>
        <a href="signin.php" class="nav-link">Sign In</a>
        <a href="register.php" class="nav-link" style="color:var(--primary-accent); font-weight:bold;">Create Account</a>
    </div>

    <!-- ================= CINEMATIC HERO SECTION (RESPONSIVE DESKTOP & MOBILE) ================= -->
    <section class="hero-section" id="hero-showcase">
        <!-- Full-bleed background video of the Autonomous Technology Showcase -->
        <div class="hero-video-bg-wrap">
            <!-- Dedicated Desktop Video (16:9 1080p Cinematic Landscape, Hardware-Accelerated H.264 MP4) -->
            <video id="heroCinematicVideoDesktop" class="hero-video-element hero-video-desktop" autoplay muted loop playsinline preload="auto" poster="images/tech-hero.jpg" aria-label="Autonomous Technology Desktop Showcase">
                <source src="videos/network_hero_desktop.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <!-- Dedicated Mobile Video (9:16 1080x1920 Vertical Portrait, Independently Optimized H.264 MP4) -->
            <video id="heroCinematicVideoMobile" class="hero-video-element hero-video-mobile" autoplay muted loop playsinline preload="auto" poster="images/tech-hero-mobile.jpg" aria-label="Autonomous Technology Mobile Showcase">
                <source src="videos/network_hero_mobile.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <div class="hero-video-scrim"></div>
        </div>

        <div class="container hero-container">
            <div class="hero-header-block">
                <!-- Clean, compact header on top -->
                <div class="hero-tag">
                    <span class="live-pulse-dot"></span>
                    <span>Autonomous Technology Terminal</span>
                </div>

                <h1 class="hero-h1">
                    Next-Generation <span class="accent-crimson">Autonomous Architecture</span>
                </h1>

                <div class="hero-mini-sub">
                    Distributed Computing &bull; Multi-Asset Cryptographic Custody
                </div>
            </div>

            <!-- Mobile Middle Spacer: Guarantees central car flight is 100% visible on portrait screens -->
            <div class="hero-mobile-spacer"></div>

            <!-- CTA Actions (Horizontally aligned on desktop, vertically stacked on mobile) -->
            <div class="hero-cta-actions">
                <a href="register.php" class="btn-signup btn-hero-lg">
                    Register <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
                <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" class="btn-wa-hero">
                    <i class="fa-brands fa-whatsapp font-size-18"></i> Chat with Admin
                </a>
            </div>
        </div>
    </section>

    <!-- ================= REAL-WORLD TELEMETRY HUD (MOVED BELOW HERO SO VIDEO IS 100% VISIBLE) ================= -->
    <div class="hero-telemetry-strip">
        <div class="container">
            <div class="hero-telemetry-grid">
                <div class="telemetry-pill-card">
                    <div class="pill-metric-val">0.02ms</div>
                    <div class="pill-metric-label">Execution Latency</div>
                    <div class="pill-sub-badge"><i class="fa-solid fa-bolt"></i> Low-Latency Engine</div>
                </div>
                <div class="telemetry-pill-card">
                    <div class="pill-metric-val" style="color:var(--accent-cyan);">Multi-Asset</div>
                    <div class="pill-metric-label">Distributed Custody</div>
                    <div class="pill-sub-badge"><i class="fa-solid fa-shield-halved"></i> Institutional Vault</div>
                </div>
                <div class="telemetry-pill-card">
                    <div class="pill-metric-val" style="color:#ffffff;">99.98%</div>
                    <div class="pill-metric-label">Infrastructure Uptime</div>
                    <div class="pill-sub-badge"><i class="fa-solid fa-server"></i> Enterprise Redundancy</div>
                </div>
                <div class="telemetry-pill-card">
                    <div class="pill-metric-val" style="color:var(--primary-accent);">24/7</div>
                    <div class="pill-metric-label">Automated Execution</div>
                    <div class="pill-sub-badge"><i class="fa-solid fa-chart-line"></i> Real-Time Telemetry</div>
                </div>
            </div>
        </div>
    </div>


    <!-- ================= DEDICATED CLEAN NON-CLASHING LIVE MARKET TICKER ================= -->
    <div class="live-market-bar-wrapper">
        <div class="container">
            <div class="market-ticker-container">
                <div class="market-ticker-header">
                    <span class="live-indicator-dot"></span>
                    <span>Live Markets</span>
                </div>
                <div class="market-ticker-marquee-window">
                    <div class="market-ticker-stream">
                        <!-- Primary Stream Items -->
                        <div class="stream-coin-item"><span class="coin-symbol">BTC / USDT</span> <strong class="coin-val tPriceBTC">$68,450.00</strong> <span class="coin-change up tChangeBTC">+1.5%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">ETH / USDT</span> <strong class="coin-val tPriceETH">$3,849.93</strong> <span class="coin-change up tChangeETH">+2.5%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">SOL / USDT</span> <strong class="coin-val tPriceSOL">$194.50</strong> <span class="coin-change up tChangeSOL">+9.8%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol" style="color:var(--primary-accent);">AVAX / USDT</span> <strong class="coin-val tPriceAVAX">$38.60</strong> <span class="coin-change up">+4.3%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol" style="color:#00e5ff;">LINK / USDT</span> <strong class="coin-val tPriceLINK">$18.40</strong> <span class="coin-change up">+1.8%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol" style="color:#f59e0b;">XRP / USDT</span> <strong class="coin-val tPriceXRP">$0.62</strong> <span class="coin-change up">+5.8%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">DOT / USDT</span> <strong class="coin-val tPriceDOT">$8.30</strong> <span class="coin-change up tChangeDOT">+2.9%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">BNB / USDT</span> <strong class="coin-val tPriceBNB">$648.10</strong> <span class="coin-change up tChangeBNB">+1.9%</span></div>
                        
                        <!-- Loop duplicate items for seamless continuous marquee -->
                        <div class="stream-coin-item"><span class="coin-symbol">BTC / USDT</span> <strong class="coin-val tPriceBTC">$68,450.00</strong> <span class="coin-change up tChangeBTC">+1.5%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">ETH / USDT</span> <strong class="coin-val tPriceETH">$3,849.93</strong> <span class="coin-change up tChangeETH">+2.5%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">SOL / USDT</span> <strong class="coin-val tPriceSOL">$194.50</strong> <span class="coin-change up tChangeSOL">+9.8%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol" style="color:var(--primary-accent);">AVAX / USDT</span> <strong class="coin-val tPriceAVAX">$38.60</strong> <span class="coin-change up">+4.3%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol" style="color:#00e5ff;">LINK / USDT</span> <strong class="coin-val tPriceLINK">$18.40</strong> <span class="coin-change up">+1.8%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol" style="color:#f59e0b;">XRP / USDT</span> <strong class="coin-val tPriceXRP">$0.62</strong> <span class="coin-change up">+5.8%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">DOT / USDT</span> <strong class="coin-val tPriceDOT">$8.30</strong> <span class="coin-change up tChangeDOT">+2.9%</span></div>
                        <div class="stream-coin-item"><span class="coin-symbol">BNB / USDT</span> <strong class="coin-val tPriceBNB">$648.10</strong> <span class="coin-change up tChangeBNB">+1.9%</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SINGLE STRAIGHT LINE FLEET CONVOY ================= -->
    <div class="network-single-line-highway-wrap">
        <div class="container">
            <div class="single-line-track-road">
                <!-- Glowing Laser Road Track Line -->
                <div class="single-track-line-base"></div>
                <div class="single-track-dashed-line"></div>
                <div class="single-track-neon-glow"></div>

                <!-- CAR 1: Autonomous Transport (Facing Right, Leading Convoy on Single Straight Line) -->
                <div class="single-car-unit convoy-truck" id="cybertruckUnit">
                    <div class="car-headlight-flare cybertruck-flare"></div>
                    <img src="images/mobility-unit-1.svg" alt="Autonomous Network Node" class="real-car-photo">
                    <div class="real-car-shadow"></div>
                </div>

                <!-- CAR 2: High-Performance Electric Unit (Facing Right, Mid Convoy on Single Straight Line) -->
                <div class="single-car-unit convoy-models" id="modelsUnit">
                    <div class="car-headlight-flare right"></div>
                    <img src="images/mobility-unit-2.png" alt="High-Performance Compute Node" class="real-car-photo">
                    <div class="real-car-shadow"></div>
                </div>

                <!-- CAR 3: Next-Gen Mobility Unit (Facing Right, Hyperspeed Convoy on Single Straight Line) -->
                <div class="single-car-unit convoy-roadster" id="roadsterUnit">
                    <div class="car-headlight-flare right roadster-flare"></div>
                    <img src="images/mobility-unit-3.png" alt="Next-Gen Routing Node" class="real-car-photo">
                    <div class="real-car-shadow"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================
         AUTONOMOUS NETWORK TELEMETRY & EXECUTION
         ================================================================ -->
    <section class="network-runway-section" id="network-telemetry">
        <div class="container">
            <div class="runway-header">
                <div class="runway-badge">
                    <i class="fa-solid fa-gauge-high"></i> Autonomous Network Telemetry &bull; Real-Time Execution
                </div>
                <h2 class="runway-title">
                    Autonomous Telemetry In Motion: <span>Continuous Network Routing</span>
                </h2>
                <p class="runway-desc">
                    Monitor automated liquidity execution across our real-time telemetry network. As quantitative algorithmic orders execute 24/7, performance data updates continuously.
                </p>
            </div>

            <!-- The Fleet Control Panel -->
            <div class="highway-wrapper">
                <!-- Live HUD Telemetry Metrics -->
                <div class="highway-hud-bar">
                    <div class="hud-stat-box">
                        <div class="hud-label"><i class="fa-solid fa-network-wired"></i> Active Nodes</div>
                        <div class="hud-val">Primary &bull; Secondary &bull; Edge</div>
                    </div>
                    <div class="hud-stat-box">
                        <div class="hud-label"><i class="fa-solid fa-gauge"></i> Execution Throughput</div>
                        <div class="hud-val accent" id="hudSpeed">0.02ms &bull; 100k TPS</div>
                    </div>
                    <div class="hud-stat-box">
                        <div class="hud-label"><i class="fa-solid fa-shield-halved"></i> Custodial Volume</div>
                        <div class="hud-val red">$480M+ &bull; Multichain</div>
                    </div>
                    <div class="hud-stat-box">
                        <div class="hud-label"><i class="fa-solid fa-robot"></i> Algorithmic Status</div>
                        <div class="hud-val accent" id="hudStatus">Active Routing</div>
                    </div>
                </div>

                <!-- Interactive Driver Motion Controls -->
                <div class="highway-controls-row">
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="btn-hud-action" id="btnAccelerate" onclick="boostFleetSpeed()">
                            <i class="fa-solid fa-bolt"></i> Boost Telemetry Speed
                        </button>
                        <button type="button" class="btn-hud-action" id="btnReverse" onclick="toggleMotionDirection()">
                            <i class="fa-solid fa-arrows-left-right"></i> Reverse Flow
                        </button>
                        <button type="button" class="btn-hud-action active" id="btnAutopilot" onclick="toggleAutopilotMode()">
                            <i class="fa-solid fa-microchip"></i> Automated Routing: ON
                        </button>
                    </div>
                    <div>
                        <a href="#plans" class="btn-signup" style="padding:10px 22px; font-size:0.88rem;">
                            <i class="fa-solid fa-chart-line me-1"></i> Explore Platform Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= INSTITUTIONAL INFRASTRUCTURE & ARCHITECTURE ================= -->
    <section class="network-ecosystem-section">
        <div class="container">
            <div class="ecosystem-grid">
                <div>
                    <span style="font-size:0.85rem; font-weight:800; color:var(--primary-accent); text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:8px;">
                        <i class="fa-solid fa-microchip"></i> Enterprise Architecture
                    </span>
                    <h2 style="font-size:2.5rem; font-weight:900; line-height:1.2; margin-bottom:20px;">
                        Why <?php echo htmlspecialchars($site_name); ?> Pioneers <span style="color:var(--primary-accent);">Next-Gen</span> Technology
                    </h2>
                    <p style="color:var(--text-muted); font-size:1.02rem; margin-bottom:18px; line-height:1.8;">
                        <?php echo htmlspecialchars($site_name); ?> operates an enterprise-grade digital asset platform engineered for institutional liquidity, automated risk management, and distributed cryptographic infrastructure.
                    </p>
                    <p style="color:var(--text-muted); font-size:1.02rem; margin-bottom:24px; line-height:1.8;">
                        Our proprietary algorithmic execution engines route capital through deep multichain liquidity pools and sustainable green-compute validation nodes. This enables resilient portfolio performance backed by 24/7 automated monitoring and institutional risk buffers.
                    </p>

                    <div style="background:#141c17; border:1px solid var(--card-dark-border); border-left:4px solid var(--primary-accent); border-radius:0 18px 18px 0; padding:20px 24px; margin-bottom:28px; box-shadow:var(--card-dark-shadow);">
                        <div style="font-weight:700; color:#ffffff; margin-bottom:6px;">Multi-Sector Portfolio Allocation</div>
                        <div style="color:var(--text-muted); font-size:0.9rem;">
                            50% Quantitative Digital Arbitrage &bull; 30% Sustainable Compute Infrastructure &bull; 20% Macro Liquidity Provision
                        </div>
                    </div>

                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                        <a href="register.php" class="btn-signup">Get Started</a>
                        <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" class="btn-wa-header" style="padding:10px 20px;">
                            <i class="fa-brands fa-whatsapp font-size-16"></i> Consult on WhatsApp
                        </a>
                    </div>
                </div>

                <!-- High-Tech Architecture Overview Cards -->
                <div style="display:flex; flex-direction:column; gap:20px;">
                    <!-- Card 1: Quantitative Execution Engine -->
                    <div style="background:#141c17; border:1px solid var(--card-dark-border); border-radius:24px; padding:24px; box-shadow:var(--card-dark-shadow);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:44px; height:44px; border-radius:12px; background:rgba(0,176,116,0.12); color:var(--primary-accent); display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                                    <i class="fa-solid fa-server"></i>
                                </div>
                                <div>
                                    <h4 style="font-size:1.05rem; font-weight:800; color:#ffffff; margin:0;">Quantitative Algorithmic Engine</h4>
                                    <span style="font-size:0.78rem; color:var(--text-muted);">Low-Latency Order Routing Architecture</span>
                                </div>
                            </div>
                            <span style="font-size:0.75rem; background:rgba(0,176,116,0.15); color:var(--primary-accent); border:1px solid rgba(0,176,116,0.3); padding:3px 10px; border-radius:20px; font-weight:800;">
                                <i class="fa-solid fa-circle-check"></i> Active 24/7
                            </span>
                        </div>
                        <p style="color:var(--text-muted); font-size:0.88rem; line-height:1.6; margin-bottom:14px;">
                            Multi-venue algorithmic liquidity execution with microsecond latency, continuous cross-exchange spread analysis, and automated risk throttling.
                        </p>
                        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; background:#0e1411; padding:12px; border-radius:14px; border:1px solid rgba(255,255,255,0.05); text-align:center;">
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase;">Throughput</div>
                                <div style="font-size:0.95rem; font-weight:800; color:#ffffff;">100k TPS</div>
                            </div>
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase;">Latency</div>
                                <div style="font-size:0.95rem; font-weight:800; color:var(--primary-accent);">0.02ms</div>
                            </div>
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase;">Network Nodes</div>
                                <div style="font-size:0.95rem; font-weight:800; color:#0284c7;">Global Mesh</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Cryptographic MPC Custody -->
                    <div style="background:#141c17; border:1px solid var(--card-dark-border); border-radius:24px; padding:24px; box-shadow:var(--card-dark-shadow);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:44px; height:44px; border-radius:12px; background:rgba(2,132,199,0.12); color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <h4 style="font-size:1.05rem; font-weight:800; color:#ffffff; margin:0;">Institutional Cryptographic Custody</h4>
                                    <span style="font-size:0.78rem; color:var(--text-muted);">Multi-Signature Cold Storage Security</span>
                                </div>
                            </div>
                            <span style="font-size:0.75rem; background:rgba(2,132,199,0.15); color:#0284c7; border:1px solid rgba(2,132,199,0.3); padding:3px 10px; border-radius:20px; font-weight:800;">
                                <i class="fa-solid fa-lock"></i> HSM Secured
                            </span>
                        </div>
                        <p style="color:var(--text-muted); font-size:0.88rem; line-height:1.6; margin-bottom:14px;">
                            Institutional cold-storage architecture with multi-party computation (MPC) private keys, 2FA PIN clearance, and strict proof-of-reserve validation.
                        </p>
                        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; background:#0e1411; padding:12px; border-radius:14px; border:1px solid rgba(255,255,255,0.05); text-align:center;">
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase;">Encryption</div>
                                <div style="font-size:0.95rem; font-weight:800; color:#ffffff;">256-Bit SSL</div>
                            </div>
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase;">Storage</div>
                                <div style="font-size:0.95rem; font-weight:800; color:var(--primary-accent);">Air-Gapped</div>
                            </div>
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase;">Uptime</div>
                                <div style="font-size:0.95rem; font-weight:800; color:#0284c7;">99.98%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 4 CORE PORTFOLIO PLANS ================= -->
    <section class="plans-section" id="plans">
        <div class="container">
            <div style="text-align:center; max-width:760px; margin:0 auto;">
                <span style="color:var(--primary-accent); font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:10px;">
                    Institutional Strategies
                </span>
                <h2 style="font-size:2.6rem; font-weight:900; letter-spacing:-1px; margin-bottom:14px;">
                    Verified Portfolio Packages
                </h2>
                <p style="color:var(--text-muted); font-size:1.05rem;">
                    Transparent yield structures tailored for everyday and institutional capital with automated daily accounting and zero hidden management fees.
                </p>
            </div>

            <div class="plans-grid">
                <?php
                $plans_query = mysqli_query($link, "SELECT * FROM plans ORDER BY min_deposit ASC");
                if ($plans_query && mysqli_num_rows($plans_query) > 0) {
                    while ($p = mysqli_fetch_assoc($plans_query)) {
                        $is_feat = ($p['is_featured'] == 1);
                        $badge = htmlspecialchars($p['badge'] ?? 'STANDARD');
                        $name = htmlspecialchars($p['name']);
                        $roi = htmlspecialchars($p['roi']);
                        $duration = htmlspecialchars($p['duration']);
                        $min_dep = number_format($p['min_deposit']);
                        $max_dep = ($p['max_deposit'] >= 1000000) ? 'Unlimited' : '$' . number_format($p['max_deposit']);
                        $desc = htmlspecialchars($p['description'] ?? '');
                        $icon = !empty($p['icon']) ? htmlspecialchars($p['icon']) : 'fa-solid fa-chart-line';
                        
                        $card_style = $is_feat ? 'style="border-color: rgba(204,255,0,0.45); box-shadow: 0 10px 30px rgba(204,255,0,0.08);"' : '';
                        $btn_style = $is_feat ? 'style="background:var(--primary-accent); color:#000;"' : '';
                        $badge_class = $is_feat ? 'plan-badge featured' : 'plan-badge';
                        $badge_icon = $is_feat ? '<i class="fa-solid fa-star me-1"></i> ' : '<i class="' . $icon . ' me-1"></i> ';
                        
                        // Parse features
                        $raw_feats = explode("\n", $p['features'] ?? '');
                        $parsed_feats = [];
                        foreach ($raw_feats as $rf) {
                            $rf = trim($rf);
                            if (!empty($rf)) {
                                $parsed_feats[] = $rf;        }        }
                ?>
                <div class="plan-card" <?php echo $card_style; ?>>
                    <div>
                        <span class="<?php echo $badge_class; ?>"><?php echo $badge_icon . $badge; ?></span>
                        <h3 style="font-size:1.35rem; font-weight:800; margin-bottom:8px;"><?php echo $name; ?></h3>
                        <div class="plan-roi"><?php echo $roi; ?>% <span>/ Weekly</span></div>
                        <p style="color:var(--text-muted); font-size:0.82rem; min-height: 40px; margin-bottom:16px;"><?php echo $desc; ?></p>
                        <ul class="plan-specs">
                            <li><i class="fa-solid fa-clock"></i> Duration: <strong><?php echo $duration; ?></strong></li>
                            <li><i class="fa-solid fa-check"></i> Min Deposit: <strong>$<?php echo $min_dep; ?></strong></li>
                            <li><i class="fa-solid fa-check"></i> Max Deposit: <strong><?php echo $max_dep; ?></strong></li>
                            <?php 
                            $count_f = 0;
                            foreach ($parsed_feats as $feat) { 
                                if (stripos($feat, 'minimum deposit') !== false || stripos($feat, 'duration') !== false) continue;
                                if ($count_f++ >= 3) break;
                            ?>
                            <li><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($feat); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <a href="register.php" class="btn-plan-select" <?php echo $btn_style; ?>>Choose Plan</a>
                </div>
                <?php        }        }
                ?>
            </div>

            <!-- ================= 3D PROFIT CALCULATOR ================= -->
            <div class="calculator-card" id="calculator">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:28px;">
                    <div>
                        <h3 style="font-size:1.8rem; font-weight:900; margin-bottom:4px;">Interactive Profit Calculator</h3>
                        <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">Select your capital amount to simulate estimated weekly and monthly returns.</p>
                    </div>
                    <span style="background:rgba(204,255,0,0.15); color:var(--primary-accent); padding:6px 16px; border-radius:20px; font-size:0.85rem; font-weight:800;">
                        <i class="fa-solid fa-calculator me-1"></i> Real-Time Projections
                    </span>
                </div>

                <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:36px; align-items:center;">
                    <div>
                        <label style="display:block; font-size:0.88rem; color:var(--text-muted); margin-bottom:12px; font-weight:600;">
                            Allocation Capital: <span id="calcAmtDisplay" style="color:var(--text-main); font-size:1.3rem; font-weight:900; margin-left:8px;">$10,000</span>
                        </label>
                        <input type="range" id="calcSlider" min="1000" max="100000" step="500" value="10000" style="width:100%; accent-color:var(--primary-accent); height:8px; cursor:pointer;" oninput="updateCalculator()">

                        <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted); margin-top:8px;">
                            <span>$1,000 (Min)</span>
                            <span>$25,000</span>
                            <span>$50,000</span>
                            <span>$100,000+</span>
                        </div>
                    </div>

                    <div style="background:#0e1511; border:1px solid var(--card-dark-border); border-radius:20px; padding:22px; text-align:center;">
                        <div style="font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px;">Estimated 7-Day Net Yield</div>
                        <div id="calcReturn7" style="font-size:2rem; font-weight:900; color:var(--primary-accent); line-height:1.2; margin-bottom:10px;">+$1,500.00</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">
                            Estimated Monthly Return: <strong id="calcReturn30" style="color:var(--text-main);">+$6,000.00</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FULL SERVICES GRID ================= -->
    <section class="services-section">
        <div class="container">
            <div style="text-align:center; max-width:700px; margin:0 auto;">
                <span style="color:var(--primary-accent); font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:8px;">
                    Multi-Market Capabilities
                </span>
                <h2 style="font-size:2.4rem; font-weight:900; letter-spacing:-0.8px;">
                    Comprehensive Financial Services
                </h2>
            </div>

            <div class="services-grid">
                <!-- Service 1: Cryptocurrencies -->
                <div class="service-card">
                    <div class="service-icon-wrap"><i class="fa-brands fa-bitcoin"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Cryptocurrencies</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        Algorithmic execution across Bitcoin, Ethereum, Solana, and digital assets with instant cold-storage reconciliation and zero counterfeit risk.
                    </p>
                </div>

                <!-- Service 2: Sustainable Compute Infrastructure -->
                <div class="service-card">
                    <div class="service-icon-wrap" style="color:var(--primary-accent); background:rgba(0,176,116,0.1);"><i class="fa-solid fa-server"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Sustainable Compute Infrastructure</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        Distributed clean-energy compute clusters powering high-throughput blockchain validation, zero-carbon network nodes, and predictive algorithmic infrastructure.
                    </p>
                </div>

                <!-- Service 3: Escrow Services -->
                <div class="service-card">
                    <div class="service-icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Institutional Escrow</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        Third-party custodian verification for high-value cross-border transactions, ensuring funds are released strictly upon verified delivery.
                    </p>
                </div>

                <!-- Service 4: Crypto-Backed Loans -->
                <div class="service-card">
                    <div class="service-icon-wrap"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Collateralized Loans</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        Borrow against your portfolio without selling your crypto holdings. Instant liquidity with flexible repayment terms and low APR.
                    </p>
                </div>

                <!-- Service 5: Forex -->
                <div class="service-card">
                    <div class="service-icon-wrap"><i class="fa-solid fa-arrow-trend-up"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Foreign Exchange (FX)</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        Predictive currency pair execution across EUR/USD, GBP/USD, and JPY pairs with 100x leverage risk parameters.
                    </p>
                </div>

                <!-- Service 6: Non-Farm Payroll (NFP) -->
                <div class="service-card">
                    <div class="service-icon-wrap"><i class="fa-solid fa-newspaper"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Non-Farm Payroll (NFP)</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        High-frequency straddle execution during US economic data releases, capturing major macroeconomic volatility bursts safely.
                    </p>
                </div>

                <!-- Service 7: Algorithmic Liquidity Syndicate -->
                <div class="service-card" style="border-color: rgba(0, 229, 255, 0.35); background: radial-gradient(circle at top right, rgba(0,229,255,0.08) 0%, #131b16 65%);">
                    <div class="service-icon-wrap" style="color:#00e5ff; background:rgba(0, 229, 255, 0.12);"><i class="fa-solid fa-network-wired"></i></div>
                    <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Algorithmic Liquidity Syndicate</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7;">
                        Direct participation in cross-venue market-making liquidity pools, automated smart routing corridors, and high-frequency quantitative execution protocols.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= LEADERSHIP & CLIENT TESTIMONIALS ================= -->
    <section class="testimonials-section">
        <div class="container">
            <div style="text-align:center; max-width:700px; margin:0 auto;">
                <span style="color:var(--primary-accent); font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:8px;">
                    Verified Track Record
                </span>
                <h2 style="font-size:2.4rem; font-weight:900; letter-spacing:-0.8px;">
                    Executive Governance & Investor Reviews
                </h2>
            </div>

            <!-- Leadership Profiles from Original Script -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px; margin-top:40px;">
                <div style="background:#ffffff; border:1px solid var(--card-light-border); border-radius:20px; padding:24px; text-align:center; box-shadow:var(--card-light-shadow);">
                    <div style="width:64px; height:64px; border-radius:50%; background:#f1f5f9; border:2px solid var(--primary-accent); color:var(--primary-accent); display:flex; align-items:center; justify-content:center; font-size:1.6rem; margin:0 auto 14px; font-weight:900;">
                        GS
                    </div>
                    <h4 style="font-size:1.15rem; font-weight:800; margin-bottom:4px; color:var(--text-light-main);">George Soros</h4>
                    <span style="font-size:0.8rem; color:var(--primary-accent); font-weight:700; text-transform:uppercase; display:block; margin-bottom:10px;">Founder & Senior Advisor</span>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">
                        Renowned international financier and philanthropist guiding long-term macroeconomic asset preservation and cross-border currency positioning.
                    </p>
                </div>

                <div style="background:#ffffff; border:1px solid var(--card-light-border); border-radius:20px; padding:24px; text-align:center; box-shadow:var(--card-light-shadow);">
                    <div style="width:64px; height:64px; border-radius:50%; background:#f1f5f9; border:2px solid var(--primary-accent); color:var(--primary-accent); display:flex; align-items:center; justify-content:center; font-size:1.6rem; margin:0 auto 14px; font-weight:900;">
                        SL
                    </div>
                    <h4 style="font-size:1.15rem; font-weight:800; margin-bottom:4px; color:var(--text-light-main);">Sarah Olivia Louis</h4>
                    <span style="font-size:0.8rem; color:var(--primary-accent); font-weight:700; text-transform:uppercase; display:block; margin-bottom:10px;">Chief Executive Officer</span>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">
                        Over 15 years institutional wealth management experience between London and New York, overseeing quantitative yield strategies and institutional digital asset portfolio integration.
                    </p>
                </div>
            </div>

            <!-- Client Testimonials from Original Script -->
            <div class="testi-grid">
                <div class="testi-card">
                    <p class="testi-quote">
                        "This is really good profit for a small investment capital. I am so glad I invested with this company. Thanks so much to Sarah Olivia Louis and the account managers for being truthful and guiding me all through my investment."
                    </p>
                    <div class="testi-author">
                        <div class="testi-avatar-circle">NT</div>
                        <div>
                            <strong style="display:block; font-size:0.95rem;"><?php echo "Nkuli Tshirumbula"; ?></strong>
                            <span style="font-size:0.78rem; color:var(--text-muted);">South Africa &bull; Verified Investor</span>
                        </div>
                    </div>
                </div>

                <div class="testi-card">
                    <p class="testi-quote">
                        "I was short of words right now. Don't actually know how I'm gonna appreciate the great work this awesome company did for me by trading my money. The returns came out huge and I made a successful withdrawal directly to my bank."
                    </p>
                    <div class="testi-author">
                        <div class="testi-avatar-circle">JG</div>
                        <div>
                            <strong style="display:block; font-size:0.95rem;"><?php echo "Joy Gray"; ?></strong>
                            <span style="font-size:0.78rem; color:var(--text-muted);">Canada &bull; Verified Investor</span>
                        </div>
                    </div>
                </div>

                <div class="testi-card">
                    <p class="testi-quote">
                        "Hey, I'm John Tubbs from Texas. Thankful to have located a truthful and trustworthy digital asset platform that made it possible to participate in algorithmic market-making and quantitative portfolio management."
                    </p>
                    <div class="testi-author">
                        <div class="testi-avatar-circle">JT</div>
                        <div>
                            <strong style="display:block; font-size:0.95rem;"><?php echo "John Tubbs"; ?></strong>
                            <span style="font-size:0.78rem; color:var(--text-muted);">Texas, USA &bull; Verified Investor</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FAQ ACCORDION ================= -->
    <section class="faq-section" id="faq">
        <div class="container" style="max-width:850px;">
            <div style="text-align:center; margin-bottom:40px;">
                <span style="color:var(--primary-accent); font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:8px;">
                    Questions Answered
                </span>
                <h2 style="font-size:2.4rem; font-weight:900;">Frequently Asked Questions</h2>
            </div>

            <div class="faq-item active">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How long does it take for my deposit to be credited?</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    As soon as your blockchain transfer is verified on-chain and reviewed by our administrative desk, it is typically credited within 1 to 2 hours directly to your active balance.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How long does the withdrawal process take?</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Withdrawal requests are processed promptly following automated PIN verification and admin compliance clearance, typically completed within 2 to 24 hours depending on the chosen cryptocurrency network.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How does the algorithmic quantitative engine generate returns?</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Capital allocated to our Premium and Deluxe plans participates in automated market-making and cross-venue algorithmic arbitrage, capitalizing on price discrepancies across top-tier digital asset liquidity pools with strict risk controls.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What security and custody measures safeguard client accounts?</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    All client digital assets are secured using multi-party computation (MPC) cold storage, multi-signature transaction authorization, and 256-bit SSL encryption. Account access and withdrawals require mandatory two-factor PIN verification and rigorous compliance clearance.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Do you offer referral bonuses?</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    Yes! Every registered member receives a unique referral link. You earn a dynamic referral bonus (currently <?php echo htmlspecialchars($ref_bonus ?? 10); ?>%) credited directly to your account balance for every direct deposit made by your referrals.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I contact the administrator directly on WhatsApp?</span>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </div>
                <div class="faq-answer">
                    You can click the green floating WhatsApp widget at the bottom right corner of any page or click the "Chat with Admin" button in the header. It connects you directly with the official platform administrator for personal assistance.
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="index.php" class="brand-logo">
                        <?php if (!empty($logo) && file_exists($logo)) { ?>
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-height: 40px; width: auto; object-fit: contain;">
                        <?php } else { ?>
                            <span class="brand-dot"></span>
                            <span class="brand-title"><?php echo htmlspecialchars($site_name); ?></span>
                        <?php } ?>
                    </a>
                    <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.7; margin-top:14px; max-width:320px;">
                        <?php echo htmlspecialchars($site_name); ?> is an institutional digital asset management and quantitative technology platform headquartered at <?php echo htmlspecialchars($address); ?>.
                    </p>
                    <div style="margin-top:16px;">
                        <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" class="btn-wa-header" style="font-size:0.8rem; padding:6px 14px;">
                            <i class="fa-brands fa-whatsapp"></i> Direct Admin WhatsApp
                        </a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Ecosystem</h4>
                    <ul>
                        <li><a href="#network-telemetry">Network Architecture</a></li>
                        <li><a href="#plans">Portfolio Plans</a></li>
                        <li><a href="#calculator">Profit Calculator</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Member Access</h4>
                    <ul>
                        <li><a href="signin.php">Sign In</a></li>
                        <li><a href="register.php">Create Account</a></li>
                        <li><a href="dashboard/customer.php">User Dashboard</a></li>
                        <li><a href="dashboard/deposit.php">Deposit Gateway</a></li>
                        <li><a href="dashboard/withdraw.php">Withdraw Funds</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact Support</h4>
                    <ul>
                        <li style="display:flex; align-items:center; gap:8px; font-size:0.88rem; color:var(--text-muted);">
                            <i class="fa-solid fa-envelope" style="color:var(--primary-accent);"></i>
                            <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a>
                        </li>
                        <li style="display:flex; align-items:center; gap:8px; font-size:0.88rem; color:var(--text-muted); margin-top:10px;">
                            <i class="fa-brands fa-whatsapp" style="color:#25d366;"></i>
                            <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank">Admin: <?php echo htmlspecialchars($whatsapp ?? $phone); ?></a>
                        </li>
                        <li style="display:flex; align-items:flex-start; gap:8px; font-size:0.88rem; color:var(--text-muted); margin-top:10px;">
                            <i class="fa-solid fa-location-dot" style="color:var(--primary-accent); margin-top:4px;"></i>
                            <span><?php echo htmlspecialchars($address); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. All rights reserved. Powered by Institutional Digital Asset & Cryptographic Network Infrastructure.
                </div>
                <div style="display:flex; gap:20px;">
                    <a href="#faq">Terms of Service</a>
                    <a href="#faq">Privacy Policy</a>
                    <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" style="color:#25d366;"><i class="fa-brands fa-whatsapp"></i> WhatsApp Support</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Direct Admin WhatsApp Floating Button -->
    <a href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" class="floating-admin-wa" title="Chat Directly with Admin on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Interactive Scripts -->
    <script>
        function toggleMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';        }

        function toggleFaq(el) {
            const item = el.parentElement;
            item.classList.toggle('active');        }

        // Calculator Logic
        function updateCalculator() {
            const amt = parseFloat(document.getElementById('calcSlider').value);
            document.getElementById('calcAmtDisplay').innerText = '$' + amt.toLocaleString();

            let roiWeekly = 0.10;
            if (amt >= 50000) {
                roiWeekly = 0.30;        } else if (amt >= 20000) {
                roiWeekly = 0.25;        } else if (amt >= 5000) {
                roiWeekly = 0.15;        }

            const yield7 = amt * roiWeekly;
            const yield30 = yield7 * 4;

            document.getElementById('calcReturn7').innerText = '+$' + yield7.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('calcReturn30').innerText = '+$' + yield30.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});        }

        // =========================================================
        // AUTONOMOUS NETWORK TELEMETRY INTERACTIVE CONTROLS
        // =========================================================
        let isBoosted = false;
        let isReversed = false;
        let autopilotActive = true;

        function boostFleetSpeed() {
            const btn = document.getElementById('btnAccelerate');
            const truck = document.getElementById('cybertruckUnit');
            const models = document.getElementById('modelsUnit');
            const roadster = document.getElementById('roadsterUnit');
            const laneTop = document.getElementById('laneDividerTop');
            const laneBot = document.getElementById('laneDividerBottom');
            const hudSpeed = document.getElementById('hudSpeed');

            if (!isBoosted) {
                isBoosted = true;
                truck.style.animationDuration = '7.0s';
                models.style.animationDuration = '7.0s';
                roadster.style.animationDuration = '7.0s';
                models.style.animationDelay = '-2.33s';
                roadster.style.animationDelay = '-4.66s';
                hudSpeed.innerText = 'Max Velocity • 100k TPS';
                hudSpeed.style.color = '#ff3333';
                btn.classList.add('active');
                btn.innerHTML = '<i class="fa-solid fa-gauge-high"></i> Throughput Boost: ON';
            } else {
                isBoosted = false;
                truck.style.animationDuration = '14.0s';
                models.style.animationDuration = '14.0s';
                roadster.style.animationDuration = '14.0s';
                models.style.animationDelay = '-4.66s';
                roadster.style.animationDelay = '-9.33s';
                hudSpeed.innerText = '0.02ms • 100k TPS';
                hudSpeed.style.color = 'var(--primary-accent)';
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Boost Telemetry Speed';
            }
        }

        function toggleMotionDirection() {
            const truck = document.getElementById('cybertruckUnit');
            const models = document.getElementById('modelsUnit');
            const roadster = document.getElementById('roadsterUnit');
            isReversed = !isReversed;
            const btn = document.getElementById('btnReverse');

            const dir = isReversed ? 'reverse' : 'normal';
            if (truck) truck.style.animationDirection = dir;
            if (models) models.style.animationDirection = dir;
            if (roadster) roadster.style.animationDirection = dir;

            if (isReversed) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }

        function toggleAutopilotMode() {
            const btn = document.getElementById('btnAutopilot');
            const hudStatus = document.getElementById('hudStatus');
            const truck = document.getElementById('cybertruckUnit');
            const models = document.getElementById('modelsUnit');
            const roadster = document.getElementById('roadsterUnit');
            autopilotActive = !autopilotActive;

            const playState = autopilotActive ? 'running' : 'paused';
            if (truck) truck.style.animationPlayState = playState;
            if (models) models.style.animationPlayState = playState;
            if (roadster) roadster.style.animationPlayState = playState;

            if (autopilotActive) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fa-solid fa-microchip"></i> Autopilot AI: ON';
                hudStatus.innerText = 'Active Arbitrage';
                hudStatus.style.color = 'var(--primary-accent)';        } else {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fa-solid fa-hand"></i> Manual Drive: ON';
                hudStatus.innerText = 'Manual Oversight';
                hudStatus.style.color = '#ff9900';        }        }

        // =========================================================
        // 3D MOTION BACKGROUND CANVAS
        // =========================================================
        (function() {
            const canvas = document.getElementById('heroCanvas');
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];
            const count = 48;
            const accent = '<?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : "#ccff00"; ?>';

            function resize() {
                width = canvas.width = canvas.parentElement.offsetWidth;
                height = canvas.height = canvas.parentElement.offsetHeight;        }

            function init() {
                particles = [];
                for (let i = 0; i < count; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        vx: (Math.random() - 0.5) * 0.7,
                        vy: (Math.random() - 0.5) * 0.7,
                        r: Math.random() * 2 + 1,
                        alpha: Math.random() * 0.5 + 0.2        });        }        }

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

                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const dx = p.x - p2.x;
                        const dy = p.y - p2.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < 120) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = accent;
                            ctx.globalAlpha = (1 - dist / 120) * 0.2;
                            ctx.stroke();        }        }        }

                ctx.globalAlpha = 1.0;
                requestAnimationFrame(draw);        }

            window.addEventListener('resize', () => {
                resize();
                init();        });

            resize();
            init();
            draw();
            updateCalculator();        })();

        // Connected Real-Time Live Ticker for Top Digital Assets
        const tickerPrices = {
            'BTC': { price: 68450.00, change: 1.50 },
            'ETH': { price: 3849.93, change: 2.48 },
            'SOL': { price: 194.50, change: 9.87 },
            'AVAX': { price: 38.60, change: 4.35 },
            'LINK': { price: 18.40, change: 1.82 },
            'XRP': { price: 0.62, change: 5.80 },
            'DOT': { price: 8.30, change: 2.88 },
            'BNB': { price: 648.10, change: 1.90 }
        };

        function updateTickerDOM(sym, price, changePct) {
            const priceStr = '$' + price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const isUp = changePct >= 0;
            const changeStr = (isUp ? '+' : '') + changePct.toFixed(1) + '%';
            
            document.querySelectorAll(`.tPrice${sym}`).forEach(el => el.innerText = priceStr);
            document.querySelectorAll(`.tChange${sym}`).forEach(el => {
                el.innerText = changeStr;
                el.className = `coin-change ${isUp ? 'up' : 'down'} tChange${sym}`;
            });
        }

        async function fetchLiveTicker() {
            try {
                const res = await fetch('https://api.binance.com/api/v3/ticker/24hr?symbols=["BTCUSDT","ETHUSDT","SOLUSDT","AVAXUSDT","LINKUSDT","XRPUSDT","DOTUSDT","BNBUSDT"]');
                if (res.ok) {
                    const data = await res.json();
                    data.forEach(item => {
                        let sym = null;
                        if (item.symbol === 'BTCUSDT') sym = 'BTC';
                        else if (item.symbol === 'ETHUSDT') sym = 'ETH';
                        else if (item.symbol === 'SOLUSDT') sym = 'SOL';
                        else if (item.symbol === 'AVAXUSDT') sym = 'AVAX';
                        else if (item.symbol === 'LINKUSDT') sym = 'LINK';
                        else if (item.symbol === 'XRPUSDT') sym = 'XRP';
                        else if (item.symbol === 'DOTUSDT') sym = 'DOT';
                        else if (item.symbol === 'BNBUSDT') sym = 'BNB';

                        if (sym && tickerPrices[sym]) {
                            tickerPrices[sym].price = parseFloat(item.lastPrice);
                            tickerPrices[sym].change = parseFloat(item.priceChangePercent);
                            updateTickerDOM(sym, tickerPrices[sym].price, tickerPrices[sym].change);
                        }
                    });
                }
            } catch (e) {
                // Natural fallback micro-jitter
                ['BTC', 'ETH', 'SOL', 'AVAX', 'LINK', 'XRP', 'DOT', 'BNB'].forEach(sym => {
                    const d = (Math.random() - 0.49) * (tickerPrices[sym].price * 0.001);
                    tickerPrices[sym].price = parseFloat((tickerPrices[sym].price + d).toFixed(2));
                    updateTickerDOM(sym, tickerPrices[sym].price, tickerPrices[sym].change);
                });
            }
        }

        fetchLiveTicker();
        setInterval(fetchLiveTicker, 4000);
    </script>
    <!-- Dedicated Cinematic Hero Video Loop Controller (Supports Desktop & Mobile H.264 Videos) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const vDesktop = document.getElementById('heroCinematicVideoDesktop');
            const vMobile = document.getElementById('heroCinematicVideoMobile');

            function setup5SecShowcase(video) {
                if (!video) return;
                video.addEventListener('timeupdate', function() {
                    // Strictly enforce 5.0-second showcase loop:
                    // Only plays when the car went from the bottom till when it flew down.
                    // Cuts off before any human faces appear!
                    if (video.currentTime >= 5.0) {
                        video.currentTime = 0.05;
                        video.play().catch(function() {});
                    }
                });
                video.play().catch(function() {});
            }

            setup5SecShowcase(vDesktop);
            setup5SecShowcase(vMobile);

            // Responsive Playback Optimization: Pause hidden video to eliminate background GPU/CPU overhead
            function syncHeroVideos() {
                const isMobile = window.innerWidth < 768;
                if (isMobile) {
                    if (vDesktop && !vDesktop.paused) vDesktop.pause();
                    if (vMobile && vMobile.paused) vMobile.play().catch(function() {});
                } else {
                    if (vMobile && !vMobile.paused) vMobile.pause();
                    if (vDesktop && vDesktop.paused) vDesktop.play().catch(function() {});
                }
            }

            syncHeroVideos();
            window.addEventListener('resize', syncHeroVideos);
            window.addEventListener('orientationchange', syncHeroVideos);
        });
    </script>
    <!-- Progressive Web App Controller -->
    <script src="pwa.js"></script>
</body>
</html>
