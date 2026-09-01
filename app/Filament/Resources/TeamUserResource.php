<?php

namespace App\Filament\Resources;

use App\Enums\TeamUserRole;
use App\Filament\Resources\TeamUserResource\Pages;
use App\Models\TeamUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamUserResource extends Resource
{
    protected static ?string $model = TeamUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = ' اعضای تیم‌ها';

    protected static ?string $pluralLabel = 'اعضای تیم‌ها';

    protected static ?string $modelLabel = 'عضو تیم';

    protected static ?string $navigationGroup = 'تیم';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'name')
                    ->label('تیم')
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('last_name')
                    ->label('نام خانوادگی')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('grade_level')
                    ->label('مقطع تحصیلی')
                    ->required()
                    ->maxLength(191),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('تاریخ تولد')
                    ->jalali()
                    ->required(),
                // Forms\Components\TextInput::make('national_code')
                //     ->label('کد ملی')
                //     ->maxLength(191),
                Forms\Components\Select::make('role')
                    ->label('نقش')
                    ->options(TeamUserRole::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('تیم')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('نام خانوادگی')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade_level')
                    ->label('مقطع تحصیلی')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_verified')
                    ->label('تایید کاربر'),
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
            'index' => Pages\ListTeamUsers::route('/'),
            'create' => Pages\CreateTeamUser::route('/create'),
            'edit' => Pages\EditTeamUser::route('/{record}/edit'),
        ];
    }
}
