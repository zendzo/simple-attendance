<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceOverview extends BaseWidget
{
    protected ?string $heading = 'Overview';

    protected ?string $description = 'Total Ringkasan Presensi Hari ini.';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Presensi', \App\Models\Attendance::whereDate('created_at', '=', \Carbon\Carbon::today())->count())
                ->description('Total presensi hari ini')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('warning')
                ->chart($this->generateRandomArray(7)),
            Stat::make('Presensi Masuk', \App\Models\Attendance::whereDate('clock_in', '=', \Carbon\Carbon::today())->count())
                ->description('Total presensi masuk hari ini')
                ->descriptionIcon('heroicon-o-arrow-left-start-on-rectangle')
                ->color('success')
                ->chart($this->generateRandomArray(7)),
            Stat::make('Presensi Keluar', \App\Models\Attendance::whereDate('clock_in', '=', \Carbon\Carbon::today())->count())
                ->description('Total presensi keluar hari ini')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info')
                ->chart($this->generateRandomArray(7)),
            Stat::make('Total izin', \App\Models\Leave::whereDate('created_at', '=', \Carbon\Carbon::today())->count())
                ->descriptionIcon('heroicon-o-arrow-right-start-on-rectangle')
                ->description('Total pengajuan izin hari ini')
                ->color('danger')
                ->chart($this->generateRandomArray(7)),
        ];
    }

    protected function generateRandomArray($length = 7, $min = 1, $max = 20)
    {
        return array_map(function () use ($min, $max) {
            return rand($min, $max);
        }, range(1, $length));
    }
}
