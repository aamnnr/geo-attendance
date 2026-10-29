<x-layouts.admin title="Pengaturan" subtitle="Lokasi sekolah, radius, dan aturan waktu presensi.">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-6 lg:grid-cols-3"
          x-data="locationPicker({
              lat: @js((float) old('school_lat', $settings['school_lat'])),
              lng: @js((float) old('school_lng', $settings['school_lng'])),
              radius: @js((int) old('school_radius', $settings['school_radius'])),
          })">
        @csrf
        @method('PUT')

        <section class="card overflow-hidden lg:col-span-2">
            <div class="flex flex-col gap-3 border-b border-ink-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-ink-900">Lokasi sekolah</h2>
                    <p class="text-sm text-ink-500">Klik peta atau geser penanda untuk memindahkan titik sekolah.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" @click="recenter()"><x-icon name="locate" size="14" /> Pusatkan</button>
                    <button type="button" class="btn btn-dark btn-sm" @click="useMyLocation()" :disabled="locating">
                        <x-icon name="navigation" size="14" /> <span x-text="locating ? 'Mencari…' : 'Lokasi saya'">Lokasi saya</span>
                    </button>
                </div>
            </div>

            <div x-ref="map" class="h-80 w-full bg-ink-100 sm:h-[26rem]"></div>

            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <x-form.input name="school_lat" label="Latitude" type="number" step="0.0000001" x-model.number="lat" :value="$settings['school_lat']" required />
                <x-form.input name="school_lng" label="Longitude" type="number" step="0.0000001" x-model.number="lng" :value="$settings['school_lng']" required />
            </div>
        </section>

        <div class="space-y-6">
            <section class="card space-y-4 p-6">
                <h2 class="font-semibold text-ink-900">Identitas</h2>
                <x-form.input name="school_name" label="Nama sekolah" :value="$settings['school_name']" required />
            </section>

            <section class="card space-y-4 p-6">
                <div>
                    <h2 class="font-semibold text-ink-900">Radius presensi</h2>
                    <p class="text-sm text-ink-500">Siswa hanya bisa presensi di dalam lingkaran ini.</p>
                </div>
                <div>
                    <div class="mb-2 flex items-baseline justify-between">
                        <label for="school_radius" class="form-label mb-0">Radius</label>
                        <span class="text-2xl font-semibold tracking-tight text-ink-900 tabular-nums"><span x-text="radius">{{ $settings['school_radius'] }}</span> <span class="text-sm font-medium text-ink-500">m</span></span>
                    </div>
                    <input type="range" min="10" max="1000" step="5" x-model.number="radius" class="w-full accent-brand-500" aria-label="Radius (slider)">
                    <div class="mt-1 flex justify-between text-xs text-ink-400"><span>10 m</span><span>1 km</span></div>
                </div>
                <x-form.input name="school_radius" type="number" min="10" max="5000" x-model.number="radius" :value="$settings['school_radius']" hint="Bisa diisi manual hingga 5000 m." required />
            </section>

            <section class="card space-y-4 p-6">
                <h2 class="font-semibold text-ink-900">Aturan waktu</h2>
                <x-form.input name="toleransi_terlambat" label="Toleransi terlambat (menit)" type="number" min="0" max="120"
                              :value="$settings['toleransi_terlambat']" hint="Dihitung dari jam pelajaran pertama." required />
                <x-form.input name="batas_presensi_masuk" label="Batas presensi masuk" type="time"
                              :value="$settings['batas_presensi_masuk']" hint="Setelah jam ini presensi masuk ditutup." required />
            </section>

            <button type="submit" class="btn btn-primary w-full py-3"><x-icon name="check" size="16" stroke="2.4" /> Simpan Pengaturan</button>
        </div>
    </form>
</x-layouts.admin>
