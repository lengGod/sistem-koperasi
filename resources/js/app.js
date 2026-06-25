import './bootstrap';
import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('appShell', () => ({
    sidebarOpen: false,
    profileOpen: false,
    confirmOpen: false,
    confirmTitle: 'Konfirmasi aksi',
    confirmMessage: 'Lanjutkan tindakan ini?',
    confirmButton: 'Lanjutkan',
    confirmTone: 'danger',
    confirmForm: null,
    openConfirm(detail) {
        this.confirmTitle = detail.title ?? 'Konfirmasi aksi';
        this.confirmMessage = detail.message ?? 'Lanjutkan tindakan ini?';
        this.confirmButton = detail.button ?? 'Lanjutkan';
        this.confirmTone = detail.tone ?? 'danger';
        this.confirmForm = detail.form ?? null;
        this.confirmOpen = true;
    },
    closeConfirm() {
        this.confirmOpen = false;
        this.confirmForm = null;
    },
    submitConfirm() {
        if (!this.confirmForm) {
            this.closeConfirm();
            return;
        }

        const form = this.confirmForm;
        this.closeConfirm();
        form.dataset.confirmBypass = 'true';
        form.requestSubmit();
    },
}));

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (!form.matches('[data-confirm]')) {
        return;
    }

    if (form.dataset.confirmBypass === 'true') {
        delete form.dataset.confirmBypass;
        return;
    }

    event.preventDefault();

    const detail = {
        title: form.dataset.confirmTitle || 'Konfirmasi aksi',
        message: form.dataset.confirmMessage || form.dataset.confirm || 'Lanjutkan tindakan ini?',
        button: form.dataset.confirmButton || 'Lanjutkan',
        tone: form.dataset.confirmTone || 'danger',
        form,
    };

    window.dispatchEvent(new CustomEvent('open-confirm-modal', { detail }));
});

Alpine.start();

Turbo.start();
import './elements/turbo-echo-stream-tag';
import './libs';

// ── Toast notification ───────────────────────────────────────────────────────
// Inject the keyframe once into the document head so it's always available.
(function injectToastStyle() {
    if (document.getElementById('toast-keyframes')) return;
    const style = document.createElement('style');
    style.id = 'toast-keyframes';
    style.textContent = `
        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateY(1rem); }
            to   { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
}());

function initToast() {
    const toast = document.getElementById('app-toast');
    if (!toast) return;

    // Apply entrance animation
    toast.style.animation = 'toastSlideIn 0.3s ease forwards';

    // Close button
    const closeBtn = document.getElementById('app-toast-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => dismissToast(toast));
    }

    // Auto-dismiss after 4 seconds
    setTimeout(() => dismissToast(toast), 4000);
}

function dismissToast(toast) {
    if (!toast || !toast.isConnected) return;
    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(1rem)';
    setTimeout(() => toast.remove(), 300);
}

// Run on initial load and after every Turbo navigation
document.addEventListener('DOMContentLoaded', initToast);
document.addEventListener('turbo:load', initToast);
