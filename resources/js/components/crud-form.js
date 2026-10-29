/**
 * Form tambah/ubah di dalam modal, dipakai halaman Kelas, Jadwal, Rekap, Libur, Izin.
 *
 *   <div x-data="crudForm({
 *       storeUrl: '...', updateUrl: '.../__ID__',
 *       defaults: { kelas_nama: '' },
 *       old: @js(session()->getOldInput()), hasErrors: @js($errors->any()),
 *   })">
 *
 * - create()        buka modal kosong
 * - edit(id, item)  buka modal berisi data item
 * Jika validasi server gagal, modal otomatis dibuka kembali dengan input lama.
 */
export function crudForm({ storeUrl, updateUrl, defaults = {}, old = {}, hasErrors = false }) {
    return {
        open: false,
        editingId: null,
        form: { ...defaults },

        init() {
            if (hasErrors && old && old._form === this.$root.id) {
                this.editingId = old._editing || null;
                this.form = { ...defaults, ...pick(old, Object.keys(defaults)) };
                this.open = true;
            }
        },

        get isEditing() {
            return this.editingId !== null && this.editingId !== '';
        },

        get action() {
            return this.isEditing ? updateUrl.replace('__ID__', this.editingId) : storeUrl;
        },

        create(preset = {}) {
            this.editingId = null;
            this.form = { ...defaults, ...preset };
            this.open = true;
        },

        edit(id, item) {
            this.editingId = id;
            this.form = { ...defaults, ...pick(item, Object.keys(defaults)) };
            this.open = true;
        },

        close() {
            this.open = false;
        },
    };
}

function pick(source, keys) {
    return Object.fromEntries(keys.filter((k) => source[k] !== undefined && source[k] !== null).map((k) => [k, source[k]]));
}
