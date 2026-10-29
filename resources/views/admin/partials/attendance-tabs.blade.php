{{-- Tab bergaris bawah ala "Timesheet / Time-off request / Time-off policy" di referensi. --}}
<a href="{{ route('admin.rekap.index') }}" class="tab-link" @if (request()->routeIs('admin.rekap.*')) aria-current="page" @endif>
    <x-icon name="clipboard" size="16" /> Rekap Presensi
</a>
<a href="{{ route('admin.izin.index') }}" class="tab-link" @if (request()->routeIs('admin.izin.*')) aria-current="page" @endif>
    <x-icon name="file-text" size="16" /> Pengajuan Izin
</a>
<a href="{{ route('admin.libur.index') }}" class="tab-link" @if (request()->routeIs('admin.libur.*')) aria-current="page" @endif>
    <x-icon name="calendar-off" size="16" /> Hari Libur
</a>
