<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = ' کاربر‌ها';
    protected static ?string $pluralLabel = 'کاربر‌ها';
    protected static ?string $modelLabel = 'کاربر';

    protected static ?string $navigationGroup = 'سیستم';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('نام')->required(),
                Forms\Components\TextInput::make('phone')->label('شماره تلفن')->regex('/^09[0-9]{9}$/')->unique(ignoreRecord: true)->required(),
                Forms\Components\TextInput::make('email')->label('ایمیل')->email()->unique(ignoreRecord: true)->required(),
                Forms\Components\Select::make('roles')
                    ->label('نقش‌ها')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('password')
                    ->label('رمز عبور')
                    ->minLength(8)
                    ->placeholder("********")
                    ->password()
                    ->hint('اگر قصد تغییر ندارید این فیلد را خالی بگذارید')
                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed()
                    ->revealable(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('تایید رمز عبور')
                    ->placeholder("********")
                    ->minLength(8)
                    ->password()
                    ->hint('رمز عبور را دوباره وارد کنید')
                    ->revealable()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
