<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = ' منطقه‌ها';
    protected static ?string $pluralLabel = 'منطقه‌ها';
    protected static ?string $modelLabel = 'منطقه';

    protected static ?string $navigationGroup = 'عمومی';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required(),
                Forms\Components\Grid::make(2)
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Toggle::make('lockable')
                            ->label('قفل شونده')
                            ->default(false),
                        Forms\Components\Toggle::make('locked')
                            ->label('قفل')
                            ->default(false),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('x')
                            ->label('X افقی')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('y')
                            ->label('Y عمودی')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('x')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('y')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('locked')
                    ->label('وضعیت')
                    ->getStateUsing(fn($record) => $record->locked ? 'قفل' : 'باز'),

                IconColumn::make('lockable')
                    ->label('قفل شونده')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
