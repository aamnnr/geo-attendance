@php
    use App\Enums\JenisIzin;
    use App\Enums\StatusIzin;

    $hariIni = today()->toDateString();
@endphp

<x-layouts.siswa title="Pengajuan Izin" subtitle="Ajukan izin sakit atau keperluan lain, lalu pantau statusnya di sini.">
    <x-slot:actions>
        <button type="button" class="btn btn-primary" @click="$dispatch('izin-create')">
            <x-icon name="plus" size="16" stroke="2.4" /> Ajukan Izin
        </button>
    </x-slot:actions>

    <x-table-card :paginator="$izins">
        <x-slot:header>
            <h2 class="text-lg font-semibold text-ink-900">{{ $izins->total() }} <span class="font-medium">Pengajuan</span></h2>
        </x-slot:header>

        @if ($izins->isEmpty())
            <x-empty-state icon="file-text" title="Belum ada pengajuan" description="Jika berhalangan hadir, ajukan izin agar tidak tercatat Alpa.">
                <button type="button" class="btn btn-primary" @click="$dispatch('izin-create')"><x-icon name="plus" size="16" /> Ajukan Izin</button>
            </x-empty-state>
        @else
            <ul class="divide-y divide-ink-100">
                @foreach ($izins as $izin)
                    <li class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-start sm:gap-5">
                        <span @class([
                            'flex size-11 shrink-0 items-center justify-center rounded-xl',
                            'bg-amber-50 text-amber-600' => $izin->status === StatusIzin::Pending,
                            'bg-emerald-50 text-emerald-600' => $izin->status === StatusIzin::Disetujui,
                            'bg-red-50 text-red-600' => $izin->status === StatusIzin::Ditolak,
                        ])>
                            <x-icon :name="match ($izin->status) { StatusIzin::Pending => 'clock', StatusIzin::Disetujui => 'check-circle', StatusIzin::Ditolak => 'x-circle' }" size="20" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-medium text-ink-900">{{ $izin->jenis->label() }}</p>
                                <x-badge :status="$izin->status" />
                            </div>
                            <p class="mt-0.5 text-sm text-ink-500">{{ $izin->periode }} · {{ $izin->durasi }} hari</p>
                            <p class="mt-2 text-sm text-ink-700">{{ $izin->keterangan }}</p>

                            @if ($izin->catatan_admin)
                                <p class="mt-2 rounded-lg bg-ink-50 px-3 py-2 text-sm text-ink-600">
                                    <span class="font-medium text-ink-800">Catatan admin:</span> {{ $izin->catatan_admin }}
                                </p>
                            @endif

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-ink-400">
                                <span>Diajukan {{ $izin->created_at->translatedFormat('j M Y, H:i') }}</span>
                                @if ($izin->admin)
                                    <span>Diproses oleh {{ $izin->admin->admin_nama }}</span>
                                @endif
                                @if ($izin->bukti_url)
                                    <a href="{{ $izin->bukti_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-medium text-ink-600 hover:text-ink-900">
                                        <x-icon name="paperclip" size="13" /> Lampiran
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($izin->isPending())
                            <x-delete-button :action="route('siswa.izin.destroy', $izin)" label="Batalkan" title="Batalkan pengajuan?"
                                             message="Pengajuan izin {{ $izin->periode }} akan dibatalkan." />
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </x-table-card>

    <div id="izin-form" x-data="crudForm({
            storeUrl: @js(route('siswa.izin.store')),
            updateUrl: '',
            defaults: { jenis: 'sakit', izin_dari: @js($hariIni), izin_sampai: @js($hariIni), keterangan: '' },
            old: @js(session()->getOldInput()),
            hasErrors: @js($errors->any()),
         })" @izin-create.window="create()">
        <x-modal title="Ajukan Izin" description="Izin akan ditinjau oleh admin sekolah." max-width="lg">
            <form method="POST" :action="action" enctype="multipart/form-data" class="space-y-4">
                <x-form.crud-meta form="izin-form" />
                <x-form.select name="jenis" label="Jenis izin" x-model="form.jenis" :options="JenisIzin::options()" required />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form.input type="date" name="izin_dari" label="Dari tanggal" x-model="form.izin_dari" :min="$hariIni" required />
                    <x-form.input type="date" name="izin_sampai" label="Sampai tanggal" x-model="form.izin_sampai" ::min="form.izin_dari" required />
                </div>
                <x-form.textarea name="keterangan" label="Alasan" x-model="form.keterangan" placeholder="Jelaskan alasan izin secara singkat" maxlength="255" required />
                <x-form.input type="file" name="bukti" label="Lampiran (opsional)" accept=".jpg,.jpeg,.png,.pdf"
                              hint="Surat dokter / surat orang tua. JPG, PNG, atau PDF, maks 2 MB."
                              input-class="file:mr-3 file:rounded-md file:border-0 file:bg-ink-100 file:px-3 file:py-1 file:text-sm" />
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn btn-secondary" @click="close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-layouts.siswa>
