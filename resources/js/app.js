import './bootstrap';
import Alpine from 'alpinejs';
import Toastify from 'toastify-js';
import ThemeManager from './theme';

// Console modules
import { VncClient, SshClient, ConsoleManager } from './console/index.js';

// Import xterm.js CSS
import '@xterm/xterm/css/xterm.css';

// Initialize theme manager immediately to prevent flash
ThemeManager.init();

// Make console modules available globally
window.VncClient = VncClient;
window.SshClient = SshClient;
window.ConsoleManager = ConsoleManager;

window.Alpine = Alpine;
Alpine.start();

// Helper function to create toast with progress bar
function createToast(message, title, duration, className) {
    const toast = Toastify({
        text: `<div class="toast-content">
            <div class="toast-header">
                <span class="toast-title">${title}</span>
                <button class="toast-close" onclick="this.closest('.toastify').remove()">✕</button>
            </div>
            <div class="toast-body">${message}</div>
            <div class="toast-progress">
                <div class="toast-progress-bar" style="animation-duration: ${duration}ms"></div>
            </div>
        </div>`,
        duration: duration,
        gravity: "top",
        position: "right",
        escapeMarkup: false,
        className: className,
        stopOnFocus: true,
    });
    toast.showToast();
    return toast;
}

// Global toast function
window.toast = {
    success: (message, title = 'Success') => createToast(message, title, 3000, 'toast-success'),
    error: (message, title = 'Error') => createToast(message, title, 8000, 'toast-error'),
    warning: (message, title = 'Warning') => createToast(message, title, 6000, 'toast-warning'),
    info: (message, title = 'Info') => createToast(message, title, 5000, 'toast-info')
};
