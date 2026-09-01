<?php

namespace App\Filament\Resources;

use App\Exports\TeamsExport;
use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = ' تیم‌ها';
    protected static ?string $pluralLabel = 'تیم‌ها';
    protected static ?string $modelLabel = 'تیم';

    protected static ?string $navigationGroup = 'تیم';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('content')
                    ->label('عکس پروفایل')
                    ->disk('ftp')
                    ->avatar()
                    ->directory('profiles'),

                Forms\Components\TextInput::make('team_identifier')
                    ->label('شناسه تیم')
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required(),

                Forms\Components\TextInput::make('bio')
                    ->label('شعار')
                    ->required(),

                Forms\Components\Select::make('gender')
                    ->label('جنسیت')
                    ->options([
                        1 => 'پسر',
                        0 => 'دختر',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('start')
                    ->label('تاریخ شروع')
                    ->required()
                    ->default(now())
                    ->jalali()
                    ->seconds(false),

                Forms\Components\TextInput::make('phone')
                    ->label('شماره تلفن')
                    ->tel(),

                Forms\Components\TextInput::make('score')
                    ->label('امتیاز')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\TextInput::make('coin')
                    ->label('سکه')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ])
            ->columns(1);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),

                Tables\Columns\TextColumn::make('team_identifier')
                    ->label('شناسه تیم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('score')
                    ->label('امتیاز')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('coin')
                    ->label('سکه')
                    ->numeric()
                    ->sortable(),

                ViewColumn::make('hash_qr')
                    ->label('QR Code')
                    ->view('filament.tables.columns.qr-code'),

                Tables\Columns\TextColumn::make('total_mission_score')
                    ->label('مجموع امتیازات توکن‌ها')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start')
                    ->label('تاریخ شروع')
                    ->jalaliDateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('بروزرسانی شده در')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('start')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('از تاریخ')
                            ->jalali(),
                        Forms\Components\DatePicker::make('until')
                            ->label('تا تاریخ')
                            ->jalali(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('start', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('start', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('خروجی اکسل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return Excel::download(new TeamsExport, 'teams.xlsx');
                    }),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
