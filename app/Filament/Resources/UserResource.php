<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationLabel = 'Data User';

  protected static ?string $navigationIcon = 'heroicon-o-user-group';

  protected static ?string $navigationGroup = 'Master Data';

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::count();
  }
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->label('Nama')
          ->required(),
        Forms\Components\TextInput::make('username')
          ->unique()
          ->required(),
        Forms\Components\Select::make('role_id')
          ->label('Jabatan')
          ->relationship('role', 'name')
          ->required(),
        Forms\Components\TextInput::make('password')
          ->password()
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->searchable(),
        Tables\Columns\TextColumn::make('username')
          ->searchable(),
        Tables\Columns\TextColumn::make('role.name')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Terdaftar')
          ->dateTime()
          ->sortable(),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Filter::make('created_at')
          ->label('Terdaftar')
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
          ->indicateUsing(function (array $data): array{
            $indicators = [];
            if($data['dari_tanggal'] ?? null){
              $indicators[] = 'Dari '.Carbon::parse($data['dari_tanggal'])->format('d-m-Y');
            }
            if ($data['sampai_tanggal'] ?? null) {
              $indicators[] = 'Sampai ' . Carbon::parse($data['sampai_tanggal'])->format('d-m-Y');
            }

            return $indicators;
          })
          ->columnSpan(2)->columns(2),
        SelectFilter::make('Jabatan')
          ->relationship('role', 'name')
          ->searchable()
          ->preload()
          ->multiple(),
      ], layout: FiltersLayout::AboveContent)->filtersFormColumns(2)
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
      RelationManagers\AttendancesRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }
}
