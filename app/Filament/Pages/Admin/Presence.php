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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('PresensiMasuk')
            ->icon('heroicon-o-clock')
            ->form([
              TextInput::make('subject')->required(),
              RichEditor::make('body')->required(),
            ])
            ->action(function (array $data) {
              Mail::to($this->client)
              ->send(new GenericEmail(
                subject: $data['subject'],
                body: $data['body'],
              ));
            })
            ->button()
            ->size('lg')
            ->color('primary')
        ];
    }
}
