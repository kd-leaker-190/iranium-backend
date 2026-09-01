<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationLabel = 'بازی‌ها';
    protected static ?string $pluralLabel = 'بازی‌ها';
    protected static ?string $modelLabel = 'بازی';
    protected static ?string $navigationGroup = 'عمومی';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام انگلیسی')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('file_name')
                    ->label('نام فایل')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('عنوان')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام انگلیسی'),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('نام فایل'),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان'),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
