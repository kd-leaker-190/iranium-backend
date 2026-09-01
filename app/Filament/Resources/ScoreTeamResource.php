<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScoreTeamResource\Pages;
use App\Filament\Resources\ScoreTeamResource\RelationManagers;
use App\Models\ScoreTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Team;

class ScoreTeamResource extends Resource
{
    protected static ?string $model = ScoreTeam::class;

    protected static ?string $navigationLabel = 'امتیاز‌ها';
    protected static ?string $pluralLabel = 'امتیاز‌ها';
    protected static ?string $modelLabel = 'امتیاز';

    protected static ?string $navigationGroup = 'عمومی';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->label('تیم')
                    ->options(Team::class)
                    ->relationship('team', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('score')
                    ->label('امتیاز')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('تیم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('امتیاز')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListScoreTeams::route('/'),
            'create' => Pages\CreateScoreTeam::route('/create'),
            'edit' => Pages\EditScoreTeam::route('/{record}/edit'),
        ];
    }
}
