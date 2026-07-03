self.addEventListener('install', function(e) {
  self.skipWaiting();
});

self.addEventListener('activate', function(e) {
  e.waitUntil(clients.claim());
});

self.addEventListener('push', function(e) {
  var data = {};
  if (e.data) {
    try { data = e.data.json(); } catch(ex) { data = { title: e.data.text() }; }
  }

  var title = data.title || 'Lembrete de Agendamento';
  var body = data.body || 'Você tem um agendamento chegando!';
  var icon = data.icon || '/images/logo.jpg';
  var tag = data.tag || 'lembrete-push';
  var url = data.url || '/';

  e.waitUntil(
    self.registration.showNotification(title, {
      body: body,
      icon: icon,
      tag: tag,
      badge: '/images/logo.jpg',
      vibrate: [200, 100, 200, 100, 200],
      requireInteraction: true,
      data: { url: url }
    })
  );
});

self.addEventListener('notificationclick', function(e) {
  e.notification.close();
  var url = e.notification.data && e.notification.data.url ? e.notification.data.url : '/';
  e.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
      for (var i = 0; i < clientList.length; i++) {
        var client = clientList[i];
        if (client.url.indexOf(self.location.origin) !== -1 && 'focus' in client) {
          return client.focus().then(function(c) { c.navigate(url); });
        }
      }
      if (clients.openWindow) return clients.openWindow(url);
    })
  );
});
