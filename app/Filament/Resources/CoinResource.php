<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoinResource\Pages;
use App\Models\Coin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class CoinResource extends Resource
{
    protected static ?string $model = Coin::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = ' سکه‌ها';
    protected static ?string $pluralLabel = 'سکه‌ها';
    protected static ?string $modelLabel = 'سکه';

    protected static ?string $navigationGroup = 'کارت ها';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required(),
                Forms\Components\TextInput::make('coin')
                    ->label('مقدار سکه')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('coin')
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
                Tables\Actions\Action::make('print_qr')
                    ->label('چاپ QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('QR Code ها')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('sm')
                    ->modalContent(function (Coin $record) {
                        $items = [];

                        $ActionPayload = [
                            'type' => 'coin',
                            'id' => $record->id,
                            'amount' => $record->coin,
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
                        $coins = Coin::all()->map(function ($coin) {
                            $payload = [
                                'type' => 'coin',
                                'id' => $coin->id,
                                'amount' => $coin->coin,
                            ];

                            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            $png = QrCode::format('png')
                                ->encoding('UTF-8')
                                ->size(100)
                                ->margin(2)
                                ->generate($json);

                            return [
                                'name' => $coin->name,
                                'value' => $coin->coin,
                                'icon' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/coin.png'))),
                                'src'  => 'data:image/png;base64,' . base64_encode($png),
                            ];
                        });

                        $pdf = PDF::loadView('pdf.qr-coins', [
                            'coins' => $coins
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'coins.pdf'
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
            'index' => Pages\ListCoins::route('/'),
            'create' => Pages\CreateCoin::route('/create'),
            'edit' => Pages\EditCoin::route('/{record}/edit'),
        ];
    }
}
