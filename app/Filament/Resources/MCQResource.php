<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MCQResource\Pages;
use Modules\MCQ\Models\MCQ;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MCQResource extends Resource
{
    protected static ?string $model = MCQ::class;

    protected static ?string $navigationLabel = 'سوالات چند گزینه ای';
    protected static ?string $pluralLabel = 'سوالات چند گزینه ای';
    protected static ?string $modelLabel = 'سوال چند گزینه ای';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'عمومی';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label('سوال')
                    ->required(),
                Forms\Components\TextInput::make('answer')
                    ->label('جواب')
                    ->required(),
                Forms\Components\Repeater::make('options')
                    ->columnSpanFull()
                    ->label('گزینه ها')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('عنوان')
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->label('مقدار')
                            ->required(),
                    ])
                    ->minItems(2)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('سوال')
                    ->searchable(),
                Tables\Columns\TextColumn::make('answer')
                    ->label('جواب')
                    ->searchable(),
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
            'index' => Pages\ListMCQS::route('/'),
            'create' => Pages\CreateMCQ::route('/create'),
            'edit' => Pages\EditMCQ::route('/{record}/edit'),
        ];
    }
}
