<?php

namespace App\Filament\App\Pages;

use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Leave extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static string $view = 'filament.app.pages.leave';

    protected static ?string $navigationLabel = 'Izin | Cuti';

    protected static ?string $title = 'Cuti Karayawan';

    protected static ?int $navigationSort = 2;

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make('create')
                ->label('Ajuakan Izin | Cuti')
                ->icon('heroicon-o-calendar-date-range')
                ->modalHeading('Buat izin baru')
                ->button()
                ->form([
                    Forms\Components\Select::make('reason')
                        ->label('Alasan')
                        ->options([
                            'sakit' => 'Sakit',
                            'izin' => 'Izin',
                            'cuti' => 'Cuti',
                            'lainnya' => 'Lainnya',
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Handle the form submission
                    // For example, you can create a new leave record in the database
                    auth()->user()->leaves()->create($data);
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Cuti Berhasil Diajukan')
                        ->body('Cuti Anda berhasil diajukan. Mohon tunggu persetujuan dari atasan.'),
                )
                ->after(function () {
                    return redirect()->route('filament.app.pages.leave');
                }),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Livewire\LeavePageWidget::class,
        ];
    }
}
