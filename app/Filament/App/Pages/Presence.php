<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

class Presence extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-clock';

  protected static ?string $navigationLabel = 'Presensi';

  protected static string $view = 'filament.app.pages.presence';

  protected static ?string $title = 'Presensi Karayawan';

  public function getHeaderWidgetsColumns(): int | array
  {
    return 1;
  }

  protected function getHeaderActions(): array
  {
    $presence = \App\Models\Attendance::where('user_id', auth()
      ->user()->id)
      ->whereDate('clock_in', now())
      ->first();
    $presenceInAndOut = \App\Models\Attendance::where('user_id', auth()
      ->user()->id)
      ->whereDate('clock_in', now())
      ->whereNotNull('clock_out')
      ->first();

    if (!$presence) {
      return [
        Action::make('PresensiMasuk')
          ->icon('heroicon-o-clock')
          ->action(function (array $data) {
            $attendance = $this->createPresence($data);
            return redirect()->route('filament.app.pages.presence', $attendance);
          })
          ->button()
          ->size('lg')
          ->color('primary')
      ];
    } elseif (!$presenceInAndOut) {
      return $this->handlePresenceAction($presence);
    } else {
      return [
        Action::make('Sudah Presensi Hari Ini')
          ->icon('heroicon-o-check-circle')
      ];
    }
  }

  private function handlePresenceAction($presence)
  {
    return [
      Action::make('Keluar')
        ->action(function (array $data) use ($presence) {
          $presence->status = 'keluar';
          $presence->clock_out = now();
          $presence->updated_at = now();
          $presence->save();

          return redirect()->route('filament.app.pages.presence', $presence);
        })
        ->button()
        ->size('lg')
        ->color('danger')
    ];
  }

  protected function createPresence(array $data)
  {
    $data['status'] = 'masuk';
    $data['clock_in'] = now();
    $data['clock_out'] = null;
    $data['user_id'] = auth()->id();
    $data['created_at'] = now();
    $data['updated_at'] = now();
    $data['deleted_at'] = null;
    $attendance = new \App\Models\Attendance($data);
    $attendance->save();

    return $attendance;
  }

  protected function handlePresenceInAndOut($presence)
  {
    return [
      Action::make('Keluar')
        ->action(function (array $data) use ($presence) {
          $presence->status = 'keluar';
          $presence->clock_out = now();
          $presence->updated_at = now();
          $presence->save();

          return redirect()->route('filament.app.pages.presence', $presence);
        })
        ->button()
        ->size('lg')
        ->color('danger')
    ];
  }

  public function getActions(): array
  {
    if ($this->hasPresenceToday()) {
      return $this->handlePresenceInAndOut($this->presence);
    } else {
      return [
        Action::make('PresensiMasuk')
          ->icon('heroicon-o-clock')
          ->action(function (array $data) {
            $attendance = $this->createPresence($data);
            return redirect()->route('filament.app.pages.presence', $attendance);
          })
          ->button()
          ->size('lg')
          ->color('primary')
      ];
    }
  }

  public function getHeaderWidgets(): array
  {
    return [
      \App\Livewire\PresencePageWidget::class,
    ];
  }
}