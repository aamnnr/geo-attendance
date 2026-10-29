@props(['title', 'subtitle' => null])

@php
    $admin = auth('admin')->user();

    $nav = [
        ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.siswa.index', 'active' => 'admin.siswa.*', 'icon' => 'users', 'label' => 'Data Siswa'],
        ['route' => 'admin.kelas.index', 'active' => 'admin.kelas.*', 'icon' => 'school', 'label' => 'Kelas'],
        ['route' => 'admin.jadwal.index', 'active' => 'admin.jadwal.*', 'icon' => 'calendar-clock', 'label' => 'Jadwal'],
        ['route' => 'admin.rekap.index', 'active' => 'admin.rekap.*', 'icon' => 'clipboard', 'label' => 'Rekap Presensi'],
        ['route' => 'admin.izin.index', 'active' => 'admin.izin.*', 'icon' => 'file-text', 'label' => 'Pengajuan Izin', 'badge' => $izinPendingCount ?: null],
        ['route' => 'admin.libur.index', 'active' => 'admin.libur.*', 'icon' => 'calendar-off', 'label' => 'Hari Libur'],
        ['route' => 'admin.log', 'icon' => 'history', 'label' => 'Log Aktivitas'],
        ['route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'Pengaturan'],
    ];
@endphp

<x-layouts.app
    :title="$title"
    :subtitle="$subtitle"
    :nav="$nav"
    :user="['nama' => $admin->admin_nama, 'peran' => 'Administrator', 'inisial' => str($admin->admin_nama)->substr(0, 2)->upper()]"
    logout-route="admin.logout"
>
    <x-slot:actions>{{ $actions ?? '' }}</x-slot:actions>
    <x-slot:tabs>{{ $tabs ?? '' }}</x-slot:tabs>
    {{ $slot }}
</x-layouts.app>
