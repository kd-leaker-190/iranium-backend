<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActionResource\Pages;
use App\Models\Action;
use Filament\Forms;

use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Task\Enum\TaskType;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Filament\Custom\FileInput;
use Filament\Forms\Components\MorphToSelect;
use Modules\MCQ\Models\MCQ;
use Modules\FileUpload\Models\FileUpload;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = ' عملیات‌ها';
    protected static ?string $pluralLabel = 'عملیات‌ها';
    protected static ?string $modelLabel = 'عملیات';
    protected static ?string $navigationGroup = 'عملیات‌ها';
    public static function form(Form $form): Form
    {
        $tasks = [

            MorphToSelect::make('taskable')
                ->types([
                    MorphToSelect\Type::make(MCQ::class)
                        ->label('سوال چند گزینه ای')
                        ->getOptionLabelFromRecordUsing(fn(MCQ $record): string => "{$record->question}")
                        ->modifyOptionsQueryUsing(fn(Builder $query, $state) => $query->where(function ($q) use ($state) {
                            $q->whereDoesntHave('task')
                                ->orWhere('id', $state); // keep selected option
                        })
                        ),

                    MorphToSelect\Type::make(FileUpload::class)
                        ->label('آپلود فایل')
                        ->titleAttribute('description')
                        ->modifyOptionsQueryUsing(fn(Builder $query, $state) => $query->where(function ($q) use ($state) {
                            $q->whereDoesntHave('task')
                                ->orWhere('id', $state); // keep selected option
                        })
                        ),
                ])
                ->label('نوع وظیفه')
                ->columnSpanFull()
                ->required(),
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('score')
                    ->label('امتیاز')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\TextInput::make('duration')
                    ->label('مدت زمان')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\TextInput::make('order')
                    ->label('ترتیب')
                    ->numeric()
                    ->hint('ترتیب عملیات به صورت خودکار پر میشود.')
                    ->readOnly(),
            ]),

            Forms\Components\Toggle::make('need_review')
                ->label('نیاز به بازبینی')
                ->default(false),
        ];

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام عملیات')
                    ->required(),

                Forms\Components\TextInput::make('score')
                    ->label('امتیاز')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\DateTimePicker::make('release')
                    ->default(now())
                    ->jalali()
                    ->seconds(false)
                    ->label('تاریخ انتشار')
                    ->required(),

                Forms\Components\Select::make('region_id')
                    ->required()
                    ->label('منطقه')
                    ->relationship('region', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Repeater::make('tasks')
                    ->label('وظیفه‌ها')
                    ->relationship('tasks')
                    ->schema($tasks)
                    ->reorderable()
                    ->minItems(1),

                Forms\Components\Repeater::make('dependency')
                    ->label('پیشنیاز ها')
                    ->relationship('dependency')
                    ->schema([
                        Forms\Components\Select::make('depends_on_action_id')
                            ->required()
                            ->label('شناسه عملیات')
                            ->relationship('action', 'name')
                            ->preload()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->searchable(),
                    ])->default([])
                    ->reorderable(),

                FileInput::make($form, 'attachment_boy')->required()->multiple()->label("فایل راهنما پسر"),
                FileInput::make($form, 'attachment_girl')->required()->multiple()->label("فایل راهنما دختر"),
                FileInput::make($form, 'icon')->required()->label("آیکون"),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('score')->label('امتیاز')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('نام عملیات')->searchable(),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->counts('tasks')
                    ->label('تعداد وظایف')
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('print_qr')
                    ->label('چاپ QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code ها')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->color('gray')
                    ->modalWidth('sm')
                    ->modalContent(function (Action $record) {
                        $items = [];

                        $ActionPayload = [
                            'type' => 'action_start',
                            'id' => $record->id,
                        ];

                        $json = json_encode($ActionPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $png = QrCode::format('png')
                            ->encoding('UTF-8')
                            ->size(100)
                            ->margin(2)
                            ->generate($json);

                        $items[] = [
                            'label' => "شروع عملیات - " . $record->name,
                            'src' => 'data:image/png;base64,' . base64_encode($png),
                        ];

                        $ActionPayload = [
                            'type' => 'action_end',
                            'id' => $record->id,
                        ];

                        $json = json_encode($ActionPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $png = QrCode::format('png')
                            ->encoding('UTF-8')
                            ->size(100)
                            ->margin(2)
                            ->generate($json);

                        $items[] = [
                            'label' => "پایان عملیات - " . $record->name,
                            'src' => 'data:image/png;base64,' . base64_encode($png),
                        ];
                        return view('filament.actions.qr-grid', ['items' => $items]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActions::route('/'),
            'create' => Pages\CreateAction::route('/create'),
            'edit' => Pages\EditAction::route('/{record}/edit'),
        ];
    }
}
