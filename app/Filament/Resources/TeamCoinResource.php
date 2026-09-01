<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamCoinResource\Pages;
use App\Filament\Resources\TeamCoinResource\RelationManagers;
use App\Models\TeamCoin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamCoinResource extends Resource
{
    protected static ?string $model = TeamCoin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';



    protected static ?string $navigationLabel = 'تغییرات سکه‌ها ';
    protected static ?string $pluralLabel = 'تغییرات سکه‌ها';
    protected static ?string $modelLabel = 'تغییرات سکه';

    protected static ?string $navigationGroup = 'عمومی';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->required()
                    ->label('شناسه تیم')
                    ->relationship('team', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\Placeholder::make('team_qr')
                    ->label('شناسه تیم')
                    ->content(fn() => view('livewire.team-qr-scanner')),
                Forms\Components\TextInput::make('coin')
                    ->required()
                    ->label('سکه اضافی')
                    ->numeric(),
                Forms\Components\TextInput::make('comment')
                    ->label('دلیل سکه اضافی')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->searchable(),
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
            'index' => Pages\ListTeamCoins::route('/'),
            'create' => Pages\CreateTeamCoin::route('/create'),
            'edit' => Pages\EditTeamCoin::route('/{record}/edit'),
        ];
    }
}
