<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_username' => fake()->unique()->userName(),
            'admin_password' => 'password',
            'admin_nama' => fake()->name(),
        ];
    }
}
