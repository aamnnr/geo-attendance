@php
    use App\Enums\JenisIzin;
    use App\Enums\StatusIzin;

    $tabs = [
        StatusIzin::Pending->value => 'Menunggu',
        StatusIzin::Disetujui->value => 'Disetujui',
        StatusIzin::Ditolak->value => 'Ditolak',
        'semua' => 'Semua',
    ];
    $aktif = $statusAktif?->value ?? 'semua';
@endphp

<x-layouts.admin title="Kehadiran & Izin" subtitle="Tinjau pengajuan izin siswa dan catat izin baru.">
    <x-slot:tabs>@include('admin.partials.attendance-tabs')</x-slot:tabs>

    <div x-data="{ detail: null, tolak: null }">
        <x-table-card :paginator="$izins">
            <x-slot:header>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
                    <h2 class="text-xl font-semibold tracking-tight text-ink-900">
                        {{ $izins->total() }} <span class="font-medium">Pengajuan</span>
                    </h2>
                    <nav class="segmented overflow-x-auto" aria-label="Filter status">
                        @foreach ($tabs as $value => $label)
                            <a href="{{ route('admin.izin.index', ['status' => $value, 'q' => request('q')]) }}" class="segmented-item"
                               @if ($aktif === $value) aria-current="page" @endif>
                                {{ $label }}
                                @if ($value !== 'semua')
                                    <span class="segmented-count">{{ $jumlah[$value] ?? 0 }}</span>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <form method="GET" class="relative">
                        <input type="hidden" name="status" value="{{ $aktif }}">
                        <x-icon name="search" size="16" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-ink-400" />
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS…" class="form-input py-2 pl-9 sm:w-56">
                    </form>
                    <button type="button" class="btn btn-primary" @click="$dispatch('izin-create')">
                        <x-icon name="plus" size="16" stroke="2.4" /> Catat Izin
                    </button>
                </div>
            </x-slot:header>

            @if ($izins->isEmpty())
                <x-empty-state icon="file-text" title="Tidak ada pengajuan"
                               description="{{ $aktif === 'pending' ? 'Semua pengajuan izin sudah diproses. Kerja bagus!' : 'Belum ada pengajuan izin pada kategori ini.' }}" />
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Durasi</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th class="text-center">Alasan</th>
                            <th>Persetujuan</th>
                            <th class="w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($izins as $izin)
                            @php
                                $detail = [
                                    'nama' => $izin->siswa->siswa_nama,
                                    'kelas' => $izin->siswa->kelas->kelas_nama,
                                    'jenis' => $izin->jenis->label(),
                                    'periode' => $izin->periode,
                                    'durasi' => $izin->durasi,
                                    'keterangan' => $izin->keterangan,
                                    'bukti' => $izin->bukti_url,
                                    'status' => $izin->status->label(),
                                    'catatan' => $izin->catatan_admin,
                                    'diproses' => $izin->admin ? $izin->admin->admin_nama.' · '.$izin->diproses_pada?->translatedFormat('j M Y H:i') : null,
                                    'diajukan' => $izin->created_at->translatedFormat('j M Y H:i'),
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.siswa.show', $izin->siswa) }}" class="flex items-center gap-3">
                                        <x-avatar :initials="$izin->siswa->inisial" :seed="$izin->siswa->siswa_nama" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-ink-900">{{ $izin->siswa->siswa_nama }}</p>
                                            <p class="truncate text-xs text-ink-400">{{ $izin->siswa->kelas->kelas_nama }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td class="whitespace-nowrap">{{ $izin->durasi }} hari</td>
                                <td class="whitespace-nowrap">{{ $izin->periode }}</td>
                                <td class="whitespace-nowrap">{{ $izin->jenis->label() }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn-icon size-8" @click="detail = @js($detail)" title="Lihat alasan">
                                        <x-icon name="file-text" size="17" />
                                    </button>
                                </td>
                                <td>
                                    @if ($izin->isPending())
                                        <div class="flex items-center gap-1">
                                            <form method="POST" action="{{ route('admin.izin.approve', $izin) }}">
                                                @csrf
                                                <button type="submit" class="btn-icon size-8 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700" title="Setujui" aria-label="Setujui izin {{ $izin->siswa->siswa_nama }}">
                                                    <x-icon name="check-circle" size="20" />
                                                </button>
                                            </form>
                                            <button type="button" class="btn-icon size-8 text-red-500 hover:bg-red-50 hover:text-red-600" title="Tolak"
                                                    aria-label="Tolak izin {{ $izin->siswa->siswa_nama }}"
                                                    @click="tolak = { url: @js(route('admin.izin.reject', $izin)), nama: @js($izin->siswa->siswa_nama) }">
                                                <x-icon name="x-circle" size="20" />
                                            </button>
                                        </div>
                                    @elseif ($izin->status === StatusIzin::Disetujui)
                                        <span class="inline-flex items-center gap-1.5 font-medium text-ink-800"><x-icon name="check" size="15" class="text-emerald-600" stroke="2.4" /> Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 font-medium text-ink-800"><x-icon name="x" size="15" class="text-red-500" stroke="2.4" /> Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button type="button" class="btn-icon size-8" @click="detail = @js($detail)" aria-label="Detail"><x-icon name="more" size="18" /></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-table-card>

        {{-- Detail pengajuan --}}
        <x-modal show="detail" title="Detail Pengajuan Izin" max-width="md">
            <template x-if="detail">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-ink-500">Siswa</dt>
                        <dd class="font-medium text-ink-900"><span x-text="detail.nama"></span> · <span class="text-ink-500" x-text="detail.kelas"></span></dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><dt class="text-ink-500">Jenis</dt><dd class="font-medium text-ink-900" x-text="detail.jenis"></dd></div>
                        <div><dt class="text-ink-500">Status</dt><dd class="font-medium text-ink-900" x-text="detail.status"></dd></div>
                        <div><dt class="text-ink-500">Tanggal</dt><dd class="font-medium text-ink-900" x-text="detail.periode"></dd></div>
                        <div><dt class="text-ink-500">Durasi</dt><dd class="font-medium text-ink-900" x-text="detail.durasi + ' hari'"></dd></div>
                    </div>
                    <div>
                        <dt class="text-ink-500">Alasan</dt>
                        <dd class="mt-1 rounded-lg bg-ink-50 p-3 text-ink-800" x-text="detail.keterangan"></dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Bukti</dt>
                        <dd class="mt-1">
                            <a x-show="detail.bukti" :href="detail.bukti" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><x-icon name="paperclip" size="14" /> Lihat lampiran</a>
                            <span x-show="!detail.bukti" class="text-ink-400">Tidak ada lampiran</span>
                        </dd>
                    </div>
                    <template x-if="detail.catatan">
                        <div><dt class="text-ink-500">Catatan admin</dt><dd class="text-ink-800" x-text="detail.catatan"></dd></div>
                    </template>
                    <p class="border-t border-ink-100 pt-3 text-xs text-ink-400">
                        Diajukan <span x-text="detail.diajukan"></span><template x-if="detail.diproses"><span> · Diproses <span x-text="detail.diproses"></span></span></template>
                    </p>
                </dl>
            </template>
        </x-modal>

        {{-- Tolak dengan catatan --}}
        <x-modal show="tolak" title="Tolak pengajuan izin" max-width="md">
            <form method="POST" :action="tolak?.url" class="space-y-4">
                @csrf
                <p class="text-sm text-ink-600">Pengajuan izin <strong class="text-ink-900" x-text="tolak?.nama"></strong> akan ditolak. Siswa dapat melihat catatan Anda.</p>
                <x-form.textarea name="catatan_admin" id="tolak_catatan" label="Catatan (opsional)" placeholder="Mis. mohon lampirkan surat dokter" rows="3" />
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-secondary" @click="tolak = null">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Izin</button>
                </div>
            </form>
        </x-modal>
    </div>

    {{-- Catat izin oleh admin (langsung disetujui) --}}
    <div id="izin-form" x-data="crudForm({
            storeUrl: @js(route('admin.izin.store')),
            updateUrl: '',
            defaults: { siswa_id: '', jenis: 'sakit', izin_dari: @js(today()->toDateString()), izin_sampai: @js(today()->toDateString()), keterangan: '', catatan_admin: '' },
            old: @js(session()->getOldInput()),
            hasErrors: @js($errors->any()),
         })" @izin-create.window="create()">
        <x-modal title="Catat Izin Siswa" description="Izin yang dicatat admin langsung berstatus disetujui." max-width="lg">
            <form method="POST" :action="action" enctype="multipart/form-data" class="space-y-4">
                <x-form.crud-meta form="izin-form" />
                <x-form.select name="siswa_id" label="Siswa" x-model="form.siswa_id" placeholder="Pilih siswa…" required
                               :options="$siswaList->mapWithKeys(fn ($s) => [$s->siswa_id => $s->siswa_nama.' — '.$s->kelas->kelas_nama])" />
                <x-form.select name="jenis" label="Jenis izin" x-model="form.jenis" :options="JenisIzin::options()" required />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form.input type="date" name="izin_dari" label="Dari tanggal" x-model="form.izin_dari" required />
                    <x-form.input type="date" name="izin_sampai" label="Sampai tanggal" x-model="form.izin_sampai" ::min="form.izin_dari" required />
                </div>
                <x-form.textarea name="keterangan" label="Alasan" x-model="form.keterangan" required />
                <x-form.input type="file" name="bukti" label="Lampiran (opsional)" accept=".jpg,.jpeg,.png,.pdf" hint="JPG, PNG, atau PDF. Maks 2 MB." input-class="file:mr-3 file:rounded-md file:border-0 file:bg-ink-100 file:px-3 file:py-1 file:text-sm" />
                <x-form.input name="catatan_admin" label="Catatan admin (opsional)" x-model="form.catatan_admin" />
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn btn-secondary" @click="close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Izin</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-layouts.admin>
