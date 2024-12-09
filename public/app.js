if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(function(registration) {
            console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch(function(error) {
            console.error('Service Worker registration failed:', error);
        });
} 
requestNotificationPermission();
function sendPushNotification(message) {
    if (Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(function(registration) {
            registration.showNotification('New Notification', {
                body: message,
                icon: 'path/to/icon.png', // Optional
                badge: 'path/to/badge.png' // Optional
            });
        });
    } else {
        console.log('Notification permission not granted.');
    }
}

// Example usage
sendPushNotification('Hello, this is a test notification!');