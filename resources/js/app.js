import './bootstrap';
import '@hotwired/turbo';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';

Alpine.plugin(persist);

// Add custom event listener for Turbo and Alpine
document.addEventListener('turbo:before-render', () => {
    let permanents = document.querySelectorAll('[data-turbo-permanent]');
    let undos = Array.from(permanents).map(el => {
        let clone = el.cloneNode(true);
        return () => {
            el.replaceWith(clone);
        };
    });
    
    document.addEventListener('turbo:render', function handler() {
        undos.forEach(undo => undo());
        document.removeEventListener('turbo:render', handler);
    });
});

// Preserve Sidebar Scroll Position
let sidebarScrollPosition = 0;

document.addEventListener('turbo:before-visit', () => {
    const sidebar = document.querySelector('aside');
    if (sidebar) {
        sidebarScrollPosition = sidebar.scrollTop;
    }
});

document.addEventListener('turbo:render', () => {
    const sidebar = document.querySelector('aside');
    if (sidebar) {
        sidebar.scrollTop = sidebarScrollPosition;
    }
});

window.Alpine = Alpine;
Alpine.start();
