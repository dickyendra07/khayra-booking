const KHAYRA_CACHE = 'khayra-erm-static-v1';

const STATIC_ASSETS = [
  '/manifest.webmanifest',
  '/images/khayra-logo.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(KHAYRA_CACHE)
      .then(cache => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys
          .filter(key => key !== KHAYRA_CACHE)
          .map(key => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  const request = event.request;

  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);

  if (url.origin !== location.origin) {
    return;
  }

  // Do not cache dynamic authenticated pages such as admin, therapist, patient, cashier, billing, reports.
  if (request.mode === 'navigate') {
    event.respondWith(fetch(request));
    return;
  }

  // Cache static assets only.
  if (
    url.pathname.startsWith('/images/') ||
    url.pathname.endsWith('.css') ||
    url.pathname.endsWith('.js') ||
    url.pathname.endsWith('.png') ||
    url.pathname.endsWith('.jpg') ||
    url.pathname.endsWith('.jpeg') ||
    url.pathname.endsWith('.svg') ||
    url.pathname.endsWith('.webp') ||
    url.pathname === '/manifest.webmanifest'
  ) {
    event.respondWith(
      caches.match(request).then(cached => {
        return cached || fetch(request).then(response => {
          const clone = response.clone();
          caches.open(KHAYRA_CACHE).then(cache => cache.put(request, clone));
          return response;
        });
      })
    );
  }
});
