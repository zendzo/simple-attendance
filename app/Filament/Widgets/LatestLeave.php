<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms;

class LatestLeave extends BaseWidget
{
  protected int|string|array $columnSpan = 1;

  public function getColumns(): int|string|array
  {
    return [
      'md' => 4,
      'xl' => 5,
    ];
  }

  public function table(Table $table): Table
  {
    return $table
      ->query(
        Leave::query()
          ->with('user', 'user.role')
          ->whereDate('created_at', now()->toDateString())
      )
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->numeric()
          ->sortable(),
        Tables\Columns\TextColumn::make('start_date')
          ->date()
          ->sortable(),
        Tables\Columns\TextColumn::make('end_date')
          ->date()
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->searchable(),
        Tables\Columns\TextColumn::make('status')
          ->color(fn(string $state): string => match ($state) {
            'pending' => 'info',
            'rejected' => 'danger',
            'approved' => 'success',
            default => 'gray',
          })
          ->icon(fn(string $state): string => match ($state) {
            'pending' => 'heroicon-o-clock',
            'rejected' => 'heroicon-o-x-circle',
            'approved' => 'heroicon-o-check-circle',
            default => 'heroicon-o-question-mark-circle',
          }),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])->defaultSort('created_at', 'desc')
      ->filters([])
      ->actions([
        Tables\Actions\EditAction::make()
          // ->label('Approval')
          ->modalHeading('Approval Cuti')
          // ->button()
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
            Forms\Components\Select::make('status')
              ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
              ])
              ->required(),
          ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }
}
