<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Filament\Actions;
use Filament\Forms;

class Leave extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static string $view = 'filament.app.pages.leave';

     protected static ?string $navigationLabel = 'Izin | Cuti';

     protected static ?string $title = 'Cuti Karayawan';

     protected static ?int $navigationSort = 2;
     
    /**
     * @return int|array
     */
     public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

  protected function getActions(): array
  {
      return [
        Actions\Action::make('create')
          ->label('Create Leave')
          ->modalHeading('Create New Leave')
          ->button()
          ->form([
            Forms\Components\TextInput::make('reason')
              ->label('Reason')
              ->required(),
            Forms\Components\DatePicker::make('start_date')
              ->label('Start Date')
              ->required(),
            Forms\Components\DatePicker::make('end_date')
              ->label('End Date')
              ->required(),
          ])
          ->action(function (array $data) {
            // Handle the form submission
            // For example, you can create a new leave record in the database
            auth()->user()->leaves()->create($data);
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
