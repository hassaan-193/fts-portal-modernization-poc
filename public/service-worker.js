const CACHE_NAME = 'attendance-pwa-v1';
const RUNTIME_CACHE = 'attendance-runtime-v1';
const OFFLINE_URL = '/pwa/offline';

const PRECACHE_URLS = [
    '/pwa/login',
    '/pwa/attendance',
    '/pwa/confirmation',
    '/pwa/offline',
    '/manifest.json',
];

self.addEventListener('install', (event) => {
    console.log('[ServiceWorker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console. log('[ServiceWorker] Caching app shell');
                return cache.addAll(PRECACHE_URLS).catch(() => {
                    console.warn('[ServiceWorker] Some files failed to cache');
                });
            })
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    console.log('[ServiceWorker] Activating...');
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                            console.log('[ServiceWorker] Deleting cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;
    if (url.origin !== location.origin) return;

    // API - Network first
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok && response.status === 200) {
                        const responseToCache = response.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                        return response;
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(request).then((cached) => {
                        if (cached) {
                            console.log('[SW] Cache:', url.pathname);
                            return cached;
                        }
                        return new Response(
                            JSON.stringify({ success: false, message: 'Offline' }),
                            { status: 503, headers: { 'Content-Type': 'application/json' } }
                        );
                    });
                })
        );
        return;
    }

    // Static assets - Cache first
    if (url.pathname.match(/\.(js|css|png|jpg|jpeg|svg|gif|webp|woff|woff2)$/)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request)
                    .then((response) => {
                        if (response.ok && response.status === 200) {
                            const responseToCache = response.clone();
                            caches.open(RUNTIME_CACHE).then((cache) => {
                                cache.put(request, responseToCache);
                            });
                            return response;
                        }
                        return response;
                    })
                    .catch(() => new Response('Offline', { status: 503 }));
            })
        );
        return;
    }

    // HTML pages - Network first
    event.respondWith(
        fetch(request)
            .then((response) => {
                if (response.ok && response.status === 200) {
                    const responseToCache = response.clone();
                    caches.open(RUNTIME_CACHE).then((cache) => {
                        cache.put(request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(request).then((cached) => {
                    if (cached) return cached;
                    return caches.match(OFFLINE_URL);
                });
            })
    );
});