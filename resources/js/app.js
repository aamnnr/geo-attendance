import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { crudForm } from './components/crud-form';
import { presensiMap } from './components/presensi-map';
import { locationPicker } from './components/location-picker';

window.Alpine = Alpine;
Alpine.plugin(focus);

/**
 * Notifikasi toast global. Dipanggil dari Blade (flash session) maupun JS:
 *   Alpine.store('toast').push('Tersimpan', 'success')
 */
Alpine.store('toast', {
    items: [],
    push(message, type = 'success', timeout = 4500) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => this.dismiss(id), timeout);
    },
    dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

/**
 * Dialog konfirmasi global. Form dengan atribut data-confirm akan
 * menampilkan dialog sebelum dikirim (lihat <x-confirm-dialog>).
 */
Alpine.store('confirm', {
    open: false,
    title: '',
    message: '',
    action: 'Hapus',
    form: null,
    ask(form) {
        this.form = form;
        this.title = form.dataset.confirmTitle || 'Yakin?';
        this.message = form.dataset.confirm;
        this.action = form.dataset.confirmAction || 'Hapus';
        this.open = true;
    },
    accept() {
        const form = this.form;
        this.open = false;
        this.form = null;
        form?.submit();
    },
});

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form instanceof HTMLFormElement && form.dataset.confirm !== undefined && Alpine.store('confirm').form !== form) {
        event.preventDefault();
        Alpine.store('confirm').ask(form);
    }
});

// Cegah double submit: tombol submit dinonaktifkan setelah form dikirim.
document.addEventListener('submit', (event) => {
    if (event.defaultPrevented) return;
    event.target.querySelectorAll?.('[type="submit"]').forEach((btn) => {
        btn.dataset.loading = 'true';
        setTimeout(() => (btn.disabled = true));
    });
});

Alpine.data('crudForm', crudForm);
Alpine.data('presensiMap', presensiMap);
Alpine.data('locationPicker', locationPicker);

Alpine.start();
