import './bootstrap';

// Import Alpine and Livewire if not globally available from bootstrap.js
// (Assuming Alpine and Livewire are set on `window` in bootstrap.js)
// import Alpine from 'alpinejs'; // Already in bootstrap.js
// import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js'; // Already in bootstrap.js

// Add any custom JavaScript functionality here
// For example: Alpine.js components, custom event listeners, etc.

// Livewire navigate events (if using Livewire 3 navigate feature)
document.addEventListener('livewire:navigated', () => {
    // Re-initialize any JavaScript that needs to run after navigation
    console.log('Livewire navigated');
    // If Alpine components need re-initialization after Livewire navigation:
    // Alpine.discoverUninitializedComponents((el) => Alpine.initializeComponent(el));
});

// Custom form validation helpers
window.showAlert = function(message, type = 'info') {
    // Custom alert system
    console.log(`${type.toUpperCase()}: ${message}`);
};

// Start Alpine and Livewire here to ensure they are initialized once
if (window.Alpine) {
    window.Alpine.start();
}

if (window.Livewire) {
    window.Livewire.start();
}
