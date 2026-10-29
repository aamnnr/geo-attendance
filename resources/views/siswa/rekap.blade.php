@php
    use App\Enums\StatusPresensi;

    $statusAktif = request('status');
    $prev = $bulan->copy()->subMonth()->format('Y-m');
    $next = $bulan->copy()->addMonth()->format('Y-m');
@endphp

<x-layouts.siswa title="Riwayat Presensi" subtitle="Catatan kehadiranmu setiap bulan.">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-1">
            <a href="{{ route('siswa.rekap', ['bulan' => $prev]) }}" class="btn-icon" aria-label="Bulan sebelumnya"><x-icon name="chevron-left" /></a>
            <form method="GET" class="relative" x-data>
                <input type="month" name="bulan" value="{{ $bulan->format('Y-m') }}" max="{{ now()->format('Y-m') }}" class="form-input w-auto py-2 font-medium" @change="$el.form.submit()" aria-label="Pilih bulan">
            </form>
            @if ($bulan->lt(now()->startOfMonth()))
                <a href="{{ route('siswa.rekap', ['bulan' => $next]) }}" class="btn-icon" aria-label="Bulan berikutnya"><x-icon name="chevron-right" /></a>
            @endif
        </div>
        <p class="text-sm text-ink-500">{{ $bulan->translatedFormat('F Y') }} · {{ array_sum($ringkasan) }} hari tercatat</p>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach (StatusPresensi::cases() as $status)
            <a href="{{ route('siswa.rekap', ['bulan' => $bulan->format('Y-m'), 'status' => $statusAktif === $status->value ? null : $status->value]) }}"
               @class([
                   'card flex items-center justify-between p-4 transition hover:-translate-y-0.5 hover:shadow-pop',
                   'ring-2 ring-brand-500' => $statusAktif === $status->value,
               ])>
                <x-badge :status="$status" />
                <span class="text-xl font-semibold text-ink-900 tabular-nums">{{ $ringkasan[$status->value] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <x-table-card :paginator="$rekaps">
        <x-slot:header>
            <h2 class="text-lg font-semibold text-ink-900">{{ $rekaps->total() }} <span class="font-medium">Catatan</span></h2>
            <nav class="segmented overflow-x-auto" aria-label="Filter status">
                <a href="{{ route('siswa.rekap', ['bulan' => $bulan->format('Y-m')]) }}" class="segmented-item" @if (! $statusAktif) aria-current="page" @endif>Semua</a>
                @foreach (StatusPresensi::cases() as $status)
                    <a href="{{ route('siswa.rekap', ['bulan' => $bulan->format('Y-m'), 'status' => $status->value]) }}" class="segmented-item"
                       @if ($statusAktif === $status->value) aria-current="page" @endif>{{ $status->label() }}</a>
                @endforeach
            </nav>
        </x-slot:header>

        @if ($rekaps->isEmpty())
            <x-empty-state icon="clipboard" title="Belum ada catatan" description="Tidak ada data presensi untuk periode ini." />
        @else
            {{-- Mobile: kartu --}}
            <ul class="divide-y divide-ink-100 sm:hidden">
                @foreach ($rekaps as $rekap)
                    <li class="flex items-center gap-4 px-5 py-4">
                        <div class="flex size-11 shrink-0 flex-col items-center justify-center rounded-xl bg-ink-50 leading-none">
                            <span class="text-base font-semibold text-ink-900">{{ $rekap->tanggal->format('j') }}</span>
                            <span class="text-[10px] text-ink-500 uppercase">{{ $rekap->tanggal->translatedFormat('M') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-ink-900">{{ $rekap->tanggal->translatedFormat('l') }}</p>
                            <p class="text-xs text-ink-500 tabular-nums">{{ $rekap->jamMasuk() ?? '--:--' }} – {{ $rekap->jamKeluar() ?? '--:--' }}</p>
                            @if ($rekap->keterangan)
                                <p class="mt-0.5 truncate text-xs text-ink-400">{{ $rekap->keterangan }}</p>
                            @endif
                        </div>
                        <x-badge :status="$rekap->status" />
                    </li>
                @endforeach
            </ul>

            {{-- Desktop: tabel --}}
            <table class="table hidden sm:table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekaps as $rekap)
                        <tr>
                            <td class="font-medium whitespace-nowrap text-ink-900">{{ $rekap->tanggal->translatedFormat('l, j M Y') }}</td>
                            <td class="tabular-nums">{{ $rekap->jamMasuk() ?? '—' }}</td>
                            <td class="tabular-nums">{{ $rekap->jamKeluar() ?? '—' }}</td>
                            <td><x-badge :status="$rekap->status" /></td>
                            <td class="text-ink-500">{{ $rekap->keterangan ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-table-card>
</x-layouts.siswa>
