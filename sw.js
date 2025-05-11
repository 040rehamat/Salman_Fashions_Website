self.addEventListener('install', e => {
    e.waitUntil(
      caches.open('v1').then(cache => {
        return cache.addAll([
          'index.php',
          'style.css',
          'script.js',
          'images/icon-192.png',
          'images/icon-512.png'
        ]);
      })
    );
  });
  
  self.addEventListener('fetch', e => {
    e.respondWith(
      caches.match(e.request).then(response => {
        return response || fetch(e.request);
      })
    );
  });
  