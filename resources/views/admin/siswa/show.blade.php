@php
    use App\Enums\StatusPresensi;

    $total = array_sum($ringkasan);
    $hadir = $ringkasan['hadir'] + $ringkasan['terlambat'];
    $persen = $total ? round($hadir / $total * 100) : 0;
@endphp

<x-layouts.admin :title="$siswa->siswa_nama" :subtitle="'NIS '.$siswa->siswa_nis.' · '.$siswa->kelas->kelas_nama">
    <x-slot:actions>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost"><x-icon name="arrow-left" size="16" /> Kembali</a>
        <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-secondary"><x-icon name="pencil" size="16" /> Ubah</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <section class="card p-6 text-center">
                <x-avatar :initials="$siswa->inisial" :seed="$siswa->siswa_nama" size="xl" class="mx-auto" />
                <h2 class="mt-4 text-lg font-semibold text-ink-900">{{ $siswa->siswa_nama }}</h2>
                <p class="text-sm text-ink-500">{{ $siswa->kelas->kelas_nama }}</p>

                <dl class="mt-6 space-y-3 border-t border-ink-100 pt-5 text-left text-sm">
                    <div class="flex items-center gap-3"><x-icon name="user" size="16" class="text-ink-400" /><dt class="sr-only">NIS</dt><dd class="tabular-nums">{{ $siswa->siswa_nis }}</dd></div>
                    <div class="flex items-center gap-3"><x-icon name="mail" size="16" class="text-ink-400" /><dt class="sr-only">Email</dt><dd class="truncate">{{ $siswa->siswa_email ?? '—' }}</dd></div>
                    <div class="flex items-center gap-3"><x-icon name="phone" size="16" class="text-ink-400" /><dt class="sr-only">HP</dt><dd>{{ $siswa->siswa_hp ?? '—' }}</dd></div>
                    <div class="flex items-start gap-3"><x-icon name="home" size="16" class="mt-0.5 text-ink-400" /><dt class="sr-only">Alamat</dt><dd>{{ $siswa->siswa_alamat ?? '—' }}</dd></div>
                </dl>
            </section>

            <section class="card p-6">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-sm text-ink-500">Tingkat kehadiran</p>
                        <p class="text-3xl font-semibold tracking-tight text-ink-900">{{ $persen }}%</p>
                    </div>
                    <p class="text-xs text-ink-400">{{ $total }} hari tercatat</p>
                </div>
                <div class="mt-4 flex h-2.5 overflow-hidden rounded-full bg-ink-100">
                    @foreach (StatusPresensi::cases() as $status)
                        @if ($total && $ringkasan[$status->value])
                            @php $warna = ['green' => 'bg-emerald-500', 'amber' => 'bg-brand-500', 'blue' => 'bg-sky-400', 'violet' => 'bg-violet-400', 'red' => 'bg-red-400'][$status->color()]; @endphp
                            <div class="{{ $warna }}" style="width: {{ $ringkasan[$status->value] / $total * 100 }}%" title="{{ $status->label() }}"></div>
                        @endif
                    @endforeach
                </div>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @foreach (StatusPresensi::cases() as $status)
                        <li class="flex items-center justify-between">
                            <x-badge :status="$status" />
                            <span class="font-medium text-ink-900 tabular-nums">{{ $ringkasan[$status->value] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <x-table-card :paginator="$rekaps">
                <x-slot:header>
                    <h2 class="font-semibold text-ink-900">Riwayat presensi</h2>
                </x-slot:header>
                @if ($rekaps->isEmpty())
                    <x-empty-state icon="clipboard" title="Belum ada rekap" description="Riwayat presensi siswa akan muncul di sini." />
                @else
                    <table class="table">
                        <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Jarak</th><th>Status</th><th>Keterangan</th></tr></thead>
                        <tbody>
                            @foreach ($rekaps as $rekap)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $rekap->tanggal->translatedFormat('D, j M Y') }}</td>
                                    <td class="tabular-nums">{{ $rekap->jamMasuk() ?? '—' }}</td>
                                    <td class="tabular-nums">{{ $rekap->jamKeluar() ?? '—' }}</td>
                                    <td class="tabular-nums">{{ $rekap->jarak !== null ? $rekap->jarak.' m' : '—' }}</td>
                                    <td><x-badge :status="$rekap->status" /></td>
                                    <td class="max-w-56 truncate text-ink-500">{{ $rekap->keterangan ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-table-card>

            <section class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-ink-200 px-5 py-4">
                    <h2 class="font-semibold text-ink-900">Izin terbaru</h2>
                    <a href="{{ route('admin.izin.index', ['status' => 'semua', 'q' => $siswa->siswa_nis]) }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">Lihat semua</a>
                </div>
                @forelse ($izins as $izin)
                    <div class="flex items-center gap-4 border-b border-ink-100 px-5 py-3.5 last:border-b-0">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-ink-100 text-ink-600"><x-icon name="file-text" size="18" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-ink-900">{{ $izin->jenis->label() }} · {{ $izin->periode }}</p>
                            <p class="truncate text-xs text-ink-500">{{ $izin->keterangan }}</p>
                        </div>
                        <x-badge :status="$izin->status" />
                    </div>
                @empty
                    <x-empty-state icon="file-text" title="Belum pernah mengajukan izin" />
                @endforelse
            </section>
        </div>
    </div>
</x-layouts.admin>
