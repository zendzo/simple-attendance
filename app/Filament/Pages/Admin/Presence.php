<?php

namespace App\Filament\Pages\Admin;

use Filament\Pages\Page;
use Filament\Actions\Action;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Mail;

class Presence extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Presensi';

    protected static string $view = 'filament.pages.admin.presence';

    protected static ?string $title = 'Presensi Karayawan';

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        $presence = \App\Models\Attendance::where('user_id', auth()->user()->id)->whereDate('clock_in', now())->first();

        if (!$presence) {
          return [
            Action::make('PresensiMasuk')
            ->icon('heroicon-o-clock')
            ->form([
                // DateTimePicker::make('clock_in')
                //     ->required(),
                // DateTimePicker::make('clock_out'),
                // TextInput::make('status')
                //     ->required(),
            ])
            ->action(function (array $data) {
                $data['status'] = 'masuk';
                $data['clock_in'] = now();
                $data['clock_out'] = null;
                $data['user_id'] = auth()->id();
                $data['created_at'] = now();
                $data['updated_at'] = now();
                $data['deleted_at'] = null;
                $attendance = new \App\Models\Attendance($data);
                $attendance->save();

                // dd($attendance);
                return redirect()->route('filament.admin.pages.presence', $attendance);
            })
            ->button()
            ->size('lg')
            ->color('primary')
        ];
        }

        return [
            Action::make('PresensiKeluar')
            ->icon('heroicon-o-clock')
            ->form([
                // DateTimePicker::make('clock_in')
                //     ->required(),
                // DateTimePicker::make('clock_out'),
                // TextInput::make('status')
                //     ->required(),
            ])
            ->action(function (array $data) use ($presence) {
                $presence->status = 'keluar';
                $presence->clock_out = now();
                $presence->updated_at = now();
                $presence->save();

                // dd($presence);
                return redirect()->route('filament.admin.pages.presence', $presence);
            })
            ->button()
            ->size('lg')
            ->color('danger')
          ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Livewire\PresencePageWidget::class,
        ];
    }
}
