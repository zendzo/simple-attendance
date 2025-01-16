<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Leave;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $title = 'Cuti | Izin Semua User';

    protected static ?string $navigationLabel = 'Cuti | Izin';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $navigationGroup = 'Laporan';

    /**
     * @param  array  $data
     * @return User
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereMonth('created_at', '=', \Carbon\Carbon::now()->month)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Data Izin | Cuti')
            ->description('Daftar izin dan cuti yang diajukan oleh karyawan')
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
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'info',
                        'rejected' => 'danger',
                        'approved' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
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
            ->headerActions([
              ExportAction::make()->exports([
                ExcelExport::make('table')
                  ->fromTable()
                  ->withFilename(Carbon::now()->format('Y-m-d H:i:s')),
              ]),
            ])
            ->defaultSort('created_at', 'desc')
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
        SelectFilter::make('Karyawan')
          ->relationship('user', 'name')
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
