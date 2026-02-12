import './bootstrap';
import Alpine from 'alpinejs';

// Initialize Alpine.js globally
window.Alpine = Alpine;

// Register Alpine directives and magic properties
Alpine.start();

// Global notification/toast function
window.showNotification = function(message, type = 'info', duration = 3000) {
    // This will be used by Blade views with Alpine x-show
    const event = new CustomEvent('showNotification', {
        detail: { message, type, duration }
    });
    document.dispatchEvent(event);
};

// Global form submission handler with loading state
document.addEventListener('submit', function(e) {
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
});

// CSRF token for AJAX requests
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

export { Alpine };
