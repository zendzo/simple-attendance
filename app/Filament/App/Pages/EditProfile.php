<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static string $view = 'filament.app.pages.edit-profile';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $title = 'Profile';

  public function form(Form $form): Form
    {
      return $form->schema([
        TextInput::make('username')
          ->required()
          ->maxLength(255),
          // $this->getNameFormComponent(),
          // $this->getPasswordFormComponent(),
          // $this->getPasswordConfirmationFormComponent(),
      ]);
  }
    
}
