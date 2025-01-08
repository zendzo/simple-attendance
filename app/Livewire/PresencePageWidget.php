<?php

namespace App\Livewire;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Attendance;

class PresencePageWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->with('user')->orderBy('clock_in', 'desc')
                    ->where('user_id', auth()->id())
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clock_in')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clock_out')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
