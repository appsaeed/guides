
self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
      // const cache = await caches.open(CACHE_NAME);
      // Setting {cache: 'reload'} in the new request will ensure that the response
      // isn't fulfilled from the HTTP cache; i.e., it will be from the network.
      // await cache.add(new Request(OFFLINE_URL, {cache: 'reload'}));
    })());
  });
  
  self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
      // Enable navigation preload if it's supported.
      // See https://developers.google.com/web/updates/2017/02/navigation-preload
      if ('navigationPreload' in self.registration) {
        await self.registration.navigationPreload.enable();
      }
    })());
  
    // Tell the active service worker to take control of the page immediately.
    self.clients.claim();
  });
  
  self.addEventListener('fetch', (event) => {
    // We only want to call event.respondWith() if this is a navigation request
    // for an HTML page.
    if (event.request.mode === 'navigate') {
      event.respondWith((async () => {
        try {
          // First, try to use the navigation preload response if it's supported.
          const preloadResponse = await event.preloadResponse;
          if (preloadResponse) {
            return preloadResponse;
          }
  
          const networkResponse = await fetch(event.request);
          return networkResponse;
        } catch (error) {
          // catch is only triggered if an exception is thrown, which is likely
          // due to a network error.
          // If fetch() returns a valid HTTP response with a response code in
          // the 4xx or 5xx range, the catch() will NOT be called.
          console.log('Fetch failed; returning offline page instead.', error);
        }
      })());
    }
  });
  
  
  self.addEventListener('message', event => {
    if (event.data && event.data.type === 'data') {
      const data = event.data.payload;
      // Handle the received data
      console.log('Data received in service worker:', data);
      
      clients.matchAll().then(clients => {
        clients.forEach(client => {
          client.postMessage({ response: 'Data received successfully' });
        });
      });
  
    }
  });