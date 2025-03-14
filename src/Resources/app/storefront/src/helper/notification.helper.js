export default class NotificationHelper {
    static init() {
        document.addEventListener('showNotification', (event) => {
            const { type, message } = event.detail;
            NotificationHelper.createNotification(type, message);
        });
    }

    static createNotification(type, message) {
        const element = document.createElement('div');
        element.className = `alert alert-${type} shr-notification`;
        element.setAttribute('role', 'alert');
        element.textContent = message;

        document.body.appendChild(element);

        // Trigger animation
        setTimeout(() => element.classList.add('show'), 10);

        // Remove notification after 3 seconds
        setTimeout(() => {
            element.classList.remove('show');
            setTimeout(() => element.remove(), 300);
        }, 3000);
    }
}