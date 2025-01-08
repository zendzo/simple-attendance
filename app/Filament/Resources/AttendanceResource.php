<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
  protected static ?string $model = Attendance::class;

  protected static ?string $title = 'Presensi Semua User';

  protected static ?string $navigationLabel = 'Presensi';

  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

  protected static ?string $navigationGroup = 'Laporan';


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('user_id')
          ->relationship('user', 'name')
          ->required(),
        Forms\Components\DateTimePicker::make('clock_in')
          ->required(),
        Forms\Components\DateTimePicker::make('clock_out'),
        Forms\Components\TextInput::make('status')
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->numeric()
          ->sortable(),
        Tables\Columns\TextColumn::make('user.role.name')
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
      ])->defaultSort('created_at', 'desc')
      ->filters([
        Filter::make('created_at')
          ->label('Presensi')
          ->form([
            DatePicker::make('dari_tanggal'),
            DatePicker::make('sampai_tanggal'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['dari_tanggal'],
                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
              )
              ->when(
                $data['sampai_tanggal'],
                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
              );
          })
          ->indicateUsing(function (array $data): array {
            $indicators = [];
            if ($data['dari_tanggal'] ?? null) {
              $indicators[] = 'Dari ' . Carbon::parse($data['dari_tanggal'])->format('d-m-Y');
            }
            if ($data['sampai_tanggal'] ?? null) {
              $indicators[] = 'Sampai ' . Carbon::parse($data['sampai_tanggal'])->format('d-m-Y');
            }

            return $indicators;
          })
          ->columnSpan(2)->columns(2),
        SelectFilter::make('Jabatan')
          ->relationship('user.role', 'name')
          ->searchable()
          ->preload()
          ->multiple(),
      ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListAttendances::route('/'),
      'create' => Pages\CreateAttendance::route('/create'),
      'edit' => Pages\EditAttendance::route('/{record}/edit'),
    ];
  }
}
