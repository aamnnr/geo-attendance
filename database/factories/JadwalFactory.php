<?php

namespace Database\Factories;

use App\Enums\Hari;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Jadwal>
 */
class JadwalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'mapel' => fake()->randomElement(['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Pemrograman Web']),
            'guru' => fake()->name(),
            'hari' => Hari::Senin,
            'jam_mulai' => '07:00',
            'jam_selesai' => '09:00',
        ];
    }
}
