/**
 * Laravel application bootstrap file
 * Load common dependencies and configure global settings
 */

import axios from 'axios';
window.axios = axios;

// Configure axios defaults
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token setup for Laravel
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Initialize any global JavaScript libraries here
// For example: Alpine.js, Chart.js, etc.

// Initialize Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Initialize theme system after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.setDarkClass = () => {
        const isDark = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

        // Use requestAnimationFrame for safer DOM manipulation to prevent childNodes errors
        requestAnimationFrame(() => {
            if (document.documentElement && document.documentElement.classList) {
                try {
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (error) {
                    // Fallback for any DOM manipulation errors
                    console.warn('Theme switching error:', error);
                    // Try again with a small delay
                    setTimeout(() => {
                        try {
                            if (isDark) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        } catch (retryError) {
                            console.error('Theme switching retry failed:', retryError);
                        }
                    }, 10);
                }
            }
        });
    };

    // Set initial theme
    window.setDarkClass();

    // Listen for system theme changes with better browser support
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    if (mediaQuery.addEventListener) {
        mediaQuery.addEventListener('change', window.setDarkClass);
    } else {
        // Fallback for older browsers
        mediaQuery.addListener(window.setDarkClass);
    }
});

// Alpine.start(); // Removed: Will be called in app.js

// Initialize Livewire
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js';
// Livewire.start(); // Removed: Will be called in app.js

// Export Alpine and Livewire if they need to be explicitly imported in app.js
// window.Livewire = Livewire; // Livewire is often globally available after import
