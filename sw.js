// Avix Treims Progressive Web App Service Worker
const CACHE_NAME = 'avixtreims-pwa-v1.0.2';
const PRECACHE_ASSETS = [
    './',
    'index.php',
    'manifest.json',
    'offline.html',
    'pwa.css',
    'pwa.js',
    'images/logo.png',
    'images/logo.svg',
    'images/icons/icon-192.png',
    'images/icons/icon-512.png',
    'images/icons/apple-touch-icon.png',
    'images/tech-hero-mobile.jpg'
];

// 1. Install Event: Pre-cache core shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[PWA SW] Pre-cache non-fatal warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Clean up outdated caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Network-first for dynamic content, cache-first for static assets
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Only handle GET requests (never cache POST forms or financial actions)
    if (request.method !== 'GET') {
        return;
    }

    // Skip chrome-extension or non-http schemes
    if (!request.url.startsWith('http')) {
        return;
    }

    // Strategy A: HTML Page Navigations (Network-first with offline fallback)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    const offlinePage = await caches.match('offline.html');
                    return offlinePage || new Response('Offline', { status: 503, statusText: 'Offline' });
                })
        );
        return;
    }

    // Strategy B: Static Assets (Images, Icons, Fonts, CSS, JS) - Cache-first with network refresh
    const isStaticAsset = request.destination === 'image' ||
                          request.destination === 'style' ||
                          request.destination === 'script' ||
                          request.destination === 'font' ||
                          request.url.match(/\.(png|jpg|jpeg|svg|gif|webp|woff2|woff|ttf|css|js)(\?.*)?$/i);

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Refresh cache in background
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                        }
                    }).catch(() => {});
                    return cachedResponse;
                }

                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Strategy C: Default Network with cache fallback
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
