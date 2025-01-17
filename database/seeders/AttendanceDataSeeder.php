<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::all();

        // Create one month attendace data for each user
        // start from 1st of last month to this date
        // fill the clock_in date with random time between 8:00 to 10:00
        // fill the clock_out date with random time between 16:00 to 18:00
        // add leave data between 1 to 5 days, max 2 days in a month
        // fill star_date and end_date with start_date = today and end_date = today + 1
        $user->each(function ($user) {
            $date = now()->subMonth()->startOfMonth();
            $endDate = now()->startOfMonth();

            while ($date->lte($endDate)) {
                $clockIn = $date->copy()->hour(rand(8, 10))->minute(rand(0, 59));
                $clockOut = $date->copy()->hour(rand(16, 18))->minute(rand(0, 59));

                $user->attendances()->create([
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'status' => 'present',
                ]);

                $date->addDay();
            }
        });
    }
}
