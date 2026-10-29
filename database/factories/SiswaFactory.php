<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    public function definition(): array
    {
        $nama = fake()->firstName().' '.fake()->lastName();

        return [
            'kelas_id' => Kelas::factory(),
            'siswa_nama' => mb_substr($nama, 0, 50),
            'siswa_nis' => fake()->unique()->numerify('23###'),
            'siswa_password' => '123456',
            'siswa_email' => fake()->unique()->safeEmail(),
            'siswa_hp' => fake()->numerify('08##########'),
            'siswa_alamat' => fake()->address(),
        ];
    }
}
