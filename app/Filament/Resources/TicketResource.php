<?php

namespace App\Filament\Resources;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'تیکت ها';
    protected static ?string $pluralLabel = 'تیکت ها';
    protected static ?string $modelLabel = 'تیکت';
    protected static ?string $navigationGroup = 'پشتیبانی';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('team_name')
                    ->label('تیم')
                    ->formatStateUsing(fn($record) => $record?->team?->name)
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('ticket_category_name')
                    ->label('دسته بندی')
                    ->formatStateUsing(fn($record) => $record?->ticketCategory?->name)
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('subject')
                    ->label('موضوع')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('priority')
                    ->label('اولویت')
                    ->formatStateUsing(fn(?string $state) => TicketPriority::tryFrom($state)?->getLabel() ?? $state)
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options(TicketStatus::class),
                Forms\Components\DateTimePicker::make('closed_at')
                    ->label('تاریخ بسته شدن تیکت')
                    ->jalali(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('تیم')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('موضوع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticketCategory.name')
                    ->label('دسته بندی')
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('اولویت')
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->color(fn($state) => match ($state) {
                        TicketPriority::LOW => 'info',
                        TicketPriority::MEDIUM => 'warning',
                        TicketPriority::HIGH => 'danger',
                        default => 'gray',
                    })
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->color(fn($state) => match ($state) {
                        TicketStatus::WAITING_FOR_ADMIN => 'info',
                        TicketStatus::WAITING_FOR_TEAM => 'success',
                        TicketStatus::CLOSED => 'primary',
                        default => 'gray',
                    })
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('closed_at')
                    ->label('تایخ بسته شدن تیکت')
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد تیکت')
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاریخ ویرایش تیکت')
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('مشاهده'),
                Tables\Actions\Action::make('changeStatus')
                    ->requiresConfirmation()
                    ->label('بستن تیکت')
                    ->color('danger')
                    ->icon('letsicon-close-ring')
                    ->iconPosition('before')
                    ->visible(fn(Ticket $ticket) => $ticket->status !== TicketStatus::CLOSED)
                    ->action(fn(Ticket $ticket) => $ticket->update([
                        'status' => 'closed',
                        'closed_at' => now()
                    ]))
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
            \App\Filament\Resources\TicketResource\RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            // 'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/manage'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
