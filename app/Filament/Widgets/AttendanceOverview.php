<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceOverview extends BaseWidget
{
  protected function getStats(): array
  {
    return [
      Stat::make('Total Presensi', \App\Models\Attendance::count())
        ->description('Total presensi hari ini')
        ->descriptionIcon('heroicon-o-clock')
        ->color('Amber')
        ->chart($this->generateRandomArray(7)),
      Stat::make('Presensi Masuk', \App\Models\Attendance::whereDate('clock_in', '=', \Carbon\Carbon::today())->count())
        ->description('Total presensi masuk hari ini')
        ->descriptionIcon('heroicon-o-clock')
        ->color('success')
        ->chart($this->generateRandomArray(7)),
      Stat::make('Presensi Keluar', \App\Models\Attendance::whereDate('clock_in', '=', \Carbon\Carbon::today())->count())
        ->description('Total presensi keluar hari ini')
        ->descriptionIcon('heroicon-o-clock')
        ->color('danger')
        ->chart($this->generateRandomArray(7)),
      Stat::make('Presensi Lengkap', \App\Models\Attendance::whereDate('clock_in', \Carbon\Carbon::today())->whereDate('clock_out', \Carbon\Carbon::today())->count())
        ->descriptionIcon('heroicon-o-clock')
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
