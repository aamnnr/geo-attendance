@php
    $gaya = [
        'alpa_otomatis' => ['icon' => 'clock', 'class' => 'bg-red-100 text-red-600', 'label' => 'Alpa otomatis'],
        'izin_disetujui' => ['icon' => 'check-circle', 'class' => 'bg-emerald-100 text-emerald-600', 'label' => 'Izin disetujui'],
        'izin_ditolak' => ['icon' => 'x-circle', 'class' => 'bg-amber-100 text-amber-700', 'label' => 'Izin ditolak'],
        'pengaturan' => ['icon' => 'settings', 'class' => 'bg-sky-100 text-sky-600', 'label' => 'Pengaturan'],
    ];
    $default = ['icon' => 'history', 'class' => 'bg-ink-100 text-ink-600', 'label' => null];
@endphp

<x-layouts.admin title="Log Aktivitas" subtitle="Jejak proses otomatis dan tindakan admin.">
    <x-table-card :paginator="$logs">
        @if ($logs->isEmpty())
            <x-empty-state icon="history" title="Belum ada aktivitas" description="Aktivitas seperti persetujuan izin dan penandaan Alpa otomatis akan tercatat di sini." />
        @else
            <ol class="relative px-5 py-4">
                @foreach ($logs->groupBy(fn ($log) => $log->created_at->toDateString()) as $tanggal => $grup)
                    <li class="pt-2 pb-3">
                        <p class="mb-3 text-xs font-semibold tracking-wide text-ink-400 uppercase">
                            {{ \Illuminate\Support\Carbon::parse($tanggal)->isToday() ? 'Hari ini' : \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, j F Y') }}
                        </p>
                        <ol class="space-y-1 border-l border-ink-200 pl-6">
                            @foreach ($grup as $log)
                                @php $g = $gaya[$log->tipe] ?? $default; @endphp
                                <li class="relative rounded-xl px-3 py-2.5 transition hover:bg-ink-50">
                                    <span class="absolute top-3 -left-[2.4rem] flex size-7 items-center justify-center rounded-full ring-4 ring-white {{ $g['class'] }}">
                                        <x-icon :name="$g['icon']" size="14" />
                                    </span>
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                        <div>
                                            <p class="text-sm text-ink-800">{{ $log->pesan }}</p>
                                            <p class="mt-0.5 text-xs text-ink-400">{{ $g['label'] ?? str($log->tipe)->replace('_', ' ')->ucfirst() }}</p>
                                        </div>
                                        <time class="shrink-0 text-xs text-ink-400" datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->translatedFormat('j M Y H:i') }}">
                                            {{ $log->created_at->diffForHumans() }}
                                        </time>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </li>
                @endforeach
            </ol>
        @endif
    </x-table-card>
</x-layouts.admin>
