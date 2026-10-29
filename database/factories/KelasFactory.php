<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kelas_nama' => fake()->unique()->numerify('Kelas ##'),
            'wali_kelas' => fake()->name(),
        ];
    }
}
