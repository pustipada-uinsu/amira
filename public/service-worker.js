self.addEventListener('install', event => {
  event.waitUntil(
    caches.open('laravel-pwa-v1').then(cache => {
      return cache.addAll([
        '/',
      ]);
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match('/'))
  );
});
