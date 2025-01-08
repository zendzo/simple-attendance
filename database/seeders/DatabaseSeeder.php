<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
          RolesTableSeeder::class,
          AdministratorSeeder::class,
        ]);

        User::factory()->has(Attendance::factory()->count(1))->count(10)->create();
    }
}
