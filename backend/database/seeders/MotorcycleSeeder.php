<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MotorcycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        \App\Models\User::factory(5)->create([
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        \App\Models\Motorcycle::factory(20)->create();

        \App\Models\Rental::factory(15)->create();
    }
}
