<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArGameResource\Pages;
use App\Filament\Resources\ArGameResource\RelationManagers;
use App\Models\ArGame;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArGameResource extends Resource
{
    protected static ?string $model = ArGame::class;

    protected static ?string $navigationLabel = 'بازی های مجازی';
    protected static ?string $pluralLabel = 'بازی های مجازی';
    protected static ?string $modelLabel = 'بازی مجازی';
    protected static ?string $navigationGroup = 'عمومی';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            'index' => Pages\ListArGames::route('/'),
            'create' => Pages\CreateArGame::route('/create'),
            'edit' => Pages\EditArGame::route('/{record}/edit'),
        ];
    }
}
