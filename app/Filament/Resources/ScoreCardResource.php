<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScoreCardResource\Pages;
use App\Models\ScoreCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class ScoreCardResource extends Resource
{
    protected static ?string $model = ScoreCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift-top';
    protected static ?string $navigationLabel = '  امتیاز‌ها';
    protected static ?string $pluralLabel = ' امتیاز‌ها';
    protected static ?string $modelLabel = ' امتیاز';

    protected static ?string $navigationGroup = 'کارت ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required(),
                Forms\Components\TextInput::make('score')
                    ->label('امتیاز')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('print_qr')
                    ->label('چاپ QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code ها')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('sm')
                    ->modalContent(function (ScoreCard $record) {
                        $items = [];

                        $ActionPayload = [
                            'type' => 'score',
                            'id' => $record->id,
                            'amount' => $record->score,
                        ];

                        $json = json_encode($ActionPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $png = QrCode::format('png')
                            ->encoding('UTF-8')
                            ->size(100)
                            ->margin(2)
                            ->generate($json);

                        $items[] = [
                            'label' => $record->name,
                            'src'   => 'data:image/png;base64,' . base64_encode($png),
                        ];

                        return view('filament.actions.qr-grid', ['items' => $items]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_all_qr')
                    ->label('دانلود همه QR ها (PDF)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $coins = ScoreCard::all()->map(function ($scoreCard) {
                            $payload = [
                                'type' => 'score',
                                'id' => $scoreCard->id,
                                'amount' => $scoreCard->score,
                            ];

                            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            $png = QrCode::format('png')
                                ->encoding('UTF-8')
                                ->size(100)
                                ->margin(2)
                                ->generate($json);

                            return [
                                'name' => $scoreCard->name,
                                'value' => $scoreCard->score,
                                'icon' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/golden-ribbon.png'))),
                                'src'  => 'data:image/png;base64,' . base64_encode($png),
                            ];
                        });

                        $pdf = PDF::loadView('pdf.qr-coins', [
                            'coins' => $coins
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'scores.pdf'
                        );
                    }),
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
            'index' => Pages\ListScoreCards::route('/'),
            'create' => Pages\CreateScoreCard::route('/create'),
            'edit' => Pages\EditScoreCard::route('/{record}/edit'),
        ];
    }
}
