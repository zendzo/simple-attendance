<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeavesRelationManager extends RelationManager
{
    protected static string $relationship = 'leaves';

    protected static ?string $title = 'Izin | Cuti';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading('Data Izin | Cuti '.auth()->user()->name)
            ->columns([
                  Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                  Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                  Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                  Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
