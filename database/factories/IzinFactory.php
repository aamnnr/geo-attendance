<?php

namespace Database\Factories;

use App\Enums\JenisIzin;
use App\Enums\StatusIzin;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Izin>
 */
class IzinFactory extends Factory
{
    public function definition(): array
    {
        $dari = fake()->dateTimeBetween('-10 days', '+5 days');

        return [
            'siswa_id' => Siswa::factory(),
            'jenis' => fake()->randomElement(JenisIzin::cases()),
            'keterangan' => fake()->randomElement([
                'Demam dan flu, sudah periksa ke dokter',
                'Menghadiri pernikahan saudara di luar kota',
                'Mengikuti lomba LKS tingkat kabupaten',
                'Sakit gigi, perlu ke dokter gigi',
                'Mengurus pembuatan KTP',
            ]),
            'izin_dari' => $dari,
            'izin_sampai' => (clone $dari)->modify('+'.fake()->numberBetween(0, 2).' days'),
            'status' => StatusIzin::Pending,
        ];
    }
}
