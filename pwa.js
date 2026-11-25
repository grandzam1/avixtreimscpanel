// Avix Treims Progressive Web App & Native Mobile App Controller
(function() {
    'use strict';

    // 1. Determine Environment & Paths
    const isDashboard = window.location.pathname.includes('/dashboard');
    const assetPrefix = isDashboard ? '../' : '';
    const swPath = isDashboard ? '../sw.js' : 'sw.js';
    const swScope = isDashboard ? '../' : './';

    // 2. Detect Native Standalone Mobile App Mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                         window.navigator.standalone === true ||
                         document.referrer.includes('android-app://') ||
                         window.location.search.includes('source=pwa');

    function applyStandaloneMode() {
        document.documentElement.classList.add('pwa-standalone-active');
        if (document.body) {
            document.body.classList.add('pwa-standalone-active');
        }
        // Hide all install UI elements in standalone mode
        hideAllPrompts();
    }

    if (isStandalone) {
        applyStandaloneMode();
        console.log('[PWA] Running in native standalone mobile app mode');
    }

    if (window.matchMedia) {
        window.matchMedia('(display-mode: standalone)').addEventListener('change', function(e) {
            if (e.matches) {
                applyStandaloneMode();
            }
        });
    }

    // 3. Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register(swPath, { scope: swScope })
                .then(function(reg) {
                    console.log('[PWA] Service Worker registered, scope:', reg.scope);
                })
                .catch(function(err) {
                    console.warn('[PWA] Service Worker registration skipped:', err);
                });
        });
    }

    // 4. Device & Browser Detection
    const isIOS = (/iPad|iPhone|iPod/.test(navigator.userAgent) || 
                  (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)) && 
                  !window.MSStream;
    const isAndroid = /Android/i.test(navigator.userAgent);
    const isMobilePhone = isIOS || isAndroid || (window.innerWidth <= 768);

    let deferredPrompt = null;

    // Helper to hide all install banners & cards
    function hideAllPrompts() {
        const banner = document.getElementById('pwaInstallPrompt');
        if (banner) banner.style.display = 'none';

        const card = document.getElementById('pwaDashboardCard');
        if (card) card.style.display = 'none';

        const navBtns = document.querySelectorAll('.pwa-install-nav-item, .pwa-mobile-header-btn');
        navBtns.forEach(btn => btn.style.display = 'none');
    }

    // Dismiss dashboard install card
    function dismissInstallCard() {
        const card = document.getElementById('pwaDashboardCard');
        if (card) {
            card.style.display = 'none';
        }
        sessionStorage.setItem('pwa_card_dismissed', 'true');
    }

    // 5. Build Modal Interfaces - Completely suppressed per user instruction ("Install mobile app should not show there at all")
    function buildPwaInstallUi() {
        hideAllPrompts();
        return;
    }

    // 6. Primary Action: Trigger App Installation
    function triggerAppInstall() {
        const banner = document.getElementById('pwaInstallPrompt');
        const iosModal = document.getElementById('pwaIosModal');
        const androidModal = document.getElementById('pwaAndroidModal');

        // Case 1: Android / Chromium with native prompt captured
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] User accepted installation prompt');
                    hideAllPrompts();
                }
                deferredPrompt = null;
                if (banner) banner.style.display = 'none';
            });
            return;
        }

        // Case 2: iPhone / iPad (iOS Safari)
        if (isIOS) {
            if (iosModal) {
                iosModal.style.display = 'flex';
            }
            return;
        }

        // Case 3: Android without deferredPrompt (prompt already used or in-app browser)
        if (isAndroid) {
            if (androidModal) {
                androidModal.style.display = 'flex';
            }
            return;
        }

        // Case 4: Desktop Chrome / Edge / Firefox
        alert("To install Avix Treims on your device:\n\n1. Look for the Install (+) icon in your browser's address bar.\n2. Or click the browser menu (⋮) and select 'Install Avix Treims'.");
    }

    // 7. Event Listeners for PWA Lifecycle
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        console.log('[PWA] beforeinstallprompt captured and ready');

        if (isStandalone) return;

        // Show dashboard install card and banner
        const dashCard = document.getElementById('pwaDashboardCard');
        if (dashCard && !sessionStorage.getItem('pwa_card_dismissed')) {
            dashCard.style.display = 'flex';
        }

        const banner = document.getElementById('pwaInstallPrompt');
        if (banner && !sessionStorage.getItem('pwa_banner_dismissed')) {
            setTimeout(() => { banner.style.display = 'flex'; }, 1500);
        }
    });

    window.addEventListener('appinstalled', function() {
        console.log('[PWA] App successfully installed');
        applyStandaloneMode();
        deferredPrompt = null;
    });

    // 8. Expose Global Handlers for Onclick Attributes
    window.triggerAppInstall = triggerAppInstall;
    window.dismissInstallCard = dismissInstallCard;
    window.AvixTreimsPWA = {
        isStandalone: isStandalone,
        isIOS: isIOS,
        isAndroid: isAndroid,
        install: triggerAppInstall
    };

    // Initialize UI on DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildPwaInstallUi);
    } else {
        buildPwaInstallUi();
    }
})();
