<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskReviewResource\Pages;
use App\Filament\Resources\TaskReviewResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Task\Models\TaskReview;

class TaskReviewResource extends Resource
{
    protected static ?string $model = TaskReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = ' بازبینی ‌ها';
    protected static ?string $pluralLabel = 'بازبینی ‌ها';
    protected static ?string $modelLabel = 'بازبینی ';

    protected static ?string $navigationGroup = 'عمومی';
    public static function canCreate(): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tables\Columns\ImageColumn::make('attachment')
                    ->label('Attachment')
                    ->square() // optional, for consistent size
                    ->width(100)
                    ->height(100)
                    ->toggleable(),
                Forms\Components\TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->max(fn($record) => $record->task->score ?? 0)
                    ->min(0)
                    ->default(0),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTaskReviews::route('/'),
            /* 'create' => Pages\CreateTaskReview::route('/create'), */
            'edit' => Pages\EditTaskReview::route('/{record}/edit'),
        ];
    }
}
