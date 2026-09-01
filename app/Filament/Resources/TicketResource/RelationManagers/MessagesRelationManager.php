<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use App\Enums\TicketStatus;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';
    protected static ?string $title = 'گفتگو';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('sender_label')
                    ->label('فرستنده')
                    ->badge()
                    ->color(fn ($record) => $record->sender_color),
                Tables\Columns\TextColumn::make('body')
                    ->label('پیام')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('زمان')
                    ->dateTime()
                    ->jalaliDateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make('reply')
                    ->label('پاسخ به تیکت')
                    ->modalHeading('ارسال پاسخ')
                    ->form([
                        Forms\Components\Textarea::make('body')
                            ->label('متن پاسخ')
                            ->required()
                            ->rows(6),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $user = auth()->user();

                        $data['sender_id'] = $user?->id;
                        $data['sender_type'] = $user?->getMorphClass();

                        return $data;
                    })
                    ->after(function () {
                        $ticket = $this->getOwnerRecord();
                        $ticket->update(['status' => TicketStatus::WAITING_FOR_TEAM]);
                    })
                    ->createAnother(false),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
