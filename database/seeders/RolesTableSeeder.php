<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'description' => 'User with all access'],
            ['name' => 'Manager', 'description' => 'User with all access'],
            ['name' => 'Employee', 'description' => 'User with limited access'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
