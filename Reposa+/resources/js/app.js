import './bootstrap';
import * as bootstrap from 'bootstrap';
import { showToast, initInteractions } from './interactions';

window.bootstrap = bootstrap;
window.showToast = showToast;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initInteractions);
} else {
    initInteractions();
}
