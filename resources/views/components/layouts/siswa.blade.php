@props(['title', 'subtitle' => null])

@php
    $siswa = auth('siswa')->user();

    $nav = [
        ['route' => 'siswa.dashboard', 'icon' => 'home', 'label' => 'Beranda'],
        ['route' => 'siswa.presensi', 'active' => 'siswa.presensi*', 'icon' => 'map-pin', 'label' => 'Presensi'],
        ['route' => 'siswa.rekap', 'icon' => 'clipboard', 'label' => 'Riwayat Presensi', 'short' => 'Riwayat'],
        ['route' => 'siswa.izin', 'active' => 'siswa.izin*', 'icon' => 'file-text', 'label' => 'Pengajuan Izin', 'short' => 'Izin'],
        ['route' => 'siswa.jadwal', 'icon' => 'calendar-clock', 'label' => 'Jadwal Pelajaran', 'short' => 'Jadwal'],
        ['route' => 'siswa.profile', 'active' => 'siswa.profile*', 'icon' => 'user', 'label' => 'Profil'],
    ];
@endphp

<x-layouts.app
    :title="$title"
    :subtitle="$subtitle"
    :nav="$nav"
    :user="['nama' => $siswa->siswa_nama, 'peran' => 'NIS '.$siswa->siswa_nis, 'inisial' => $siswa->inisial]"
    logout-route="siswa.logout"
    profile-route="siswa.profile"
    bottom-nav
>
    <x-slot:actions>{{ $actions ?? '' }}</x-slot:actions>
    <x-slot:tabs>{{ $tabs ?? '' }}</x-slot:tabs>
    {{ $slot }}
</x-layouts.app>
