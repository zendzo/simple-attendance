<?php

namespace App\Filament\Pages\Admin;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Presence extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Presensi';

    protected static string $view = 'filament.pages.admin.presence';

    protected static ?string $title = 'Presensi Karayawan';

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        $presenceIn = auth()->user()->attendances()
            ->whereDate('clock_in', Carbon::today())
            ->first();
        $presenceOut = \App\Models\Attendance::where('user_id', auth()
            ->user()->id)
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->first();
        $presenceComplete = \App\Models\Attendance::where('user_id', auth()
            ->user()->id)
            ->whereDate('clock_in', Carbon::today())
            ->whereDate('clock_out', Carbon::today())
            ->first();

        if ($presenceIn) {
            return [
                Action::make('PresensiMasuk')
                    ->icon('heroicon-o-clock')
                    ->action(function (array $data) use ($presenceComplete) {
                        if ($presenceComplete) {
                            Notification::make()
                                ->title('Anda Sudah Melakukan Presensi Hari Ini')
                                ->danger()
                                ->send();
                        } else {
                            $attendance = $this->createPresence($data);

                            return redirect()->route('filament.admin.pages.presence', $attendance);
                        }

                    })
                    ->button()
                    ->size('lg')
                    ->color('primary'),
            ];
        } elseif ($presenceOut) {
            return $this->hadlePresenceOut($presenceOut);
        } else {
            return [
                Action::make('Sudah Presensi Hari Ini')
                    ->icon('heroicon-o-check-circle'),
            ];
        }

    }

    private function hadlePresenceOut($presence)
    {
        return [
            Action::make('PresensiKeluar')
                ->icon('heroicon-o-clock')
                ->action(function (array $data) use ($presence) {
                    $presence->status = 'keluar';
                    $presence->clock_out = now();
                    $presence->updated_at = now();
                    $presence->save();

                    return redirect()->route('filament.admin.pages.presence', $presence);
                })
                ->button()
                ->size('lg')
                ->color('danger'),
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

                        return redirect()->route('filament.admin.pages.presence', $attendance);
                    })
                    ->button()
                    ->size('lg')
                    ->color('primary'),
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
