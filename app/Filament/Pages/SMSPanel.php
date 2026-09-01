<?php

namespace App\Filament\Pages;

use App\Models\Team;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SMSPanel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationGroup = 'پنل پیامک';
    
    protected static ?string $navigationLabel = 'ارسال پیامک';
    
    protected static ?string $title = 'ارسال پیامک';
    
    protected static ?string $slug = 'sms-panel';
    
    protected static ?int $navigationSort = 1;
    
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_SMSPanel') ?? false;
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'پنل پیامک';
    }
    
    protected static string $view = 'filament.pages.sms-panel';
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('message')
                    ->label('پیام')
                    ->required()
                    ->rows(4)
                    ->placeholder('متن پیام خود را وارد کنید...'),
                
                Select::make('teams')
                    ->label('انتخاب تیم ها')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(Team::all()->pluck('name', 'id'))
                    ->required()
                    ->placeholder('تیم های مورد نظر را انتخاب کنید...'),
            ])
            ->statePath('data');
    }
    
    public function sendSms(): void
    {
        $data = $this->form->getState();
        
        $this->form->validate();
        
        $smsToken = config('sms.token');
        
        if (!$smsToken) {
            Notification::make()
                ->title('خطا در تنظیمات')
                ->body('SMS_TOKEN تنظیم نشده است')
                ->danger()
                ->send();
            return;
        }
        
        try {
            $selectedTeams = Team::whereIn('id', $data['teams'] ?? [])->get();
            $phoneNumbers = $selectedTeams->pluck('phone')->filter()->toArray();
            
            if (empty($phoneNumbers)) {
                Notification::make()
                    ->title('خطا در ارسال')
                    ->body('هیچ شماره تلفنی برای تیم های انتخاب شده یافت نشد')
                    ->danger()
                    ->send();
                return;
            }
            
            
            $smsData = [
                'sending_type' => 'webservice',
                'from_number' => '+985000125475',
                'message' => $data['message'],
                'params' => [
                    'recipients' => $phoneNumbers
                ]
            ];
            
            $response = $this->sendSmsToIPPanel($smsToken, $smsData);
            
            if ($response && isset($response['meta']['status']) && $response['meta']['status']) {
                Notification::make()
                    ->title('پیامک ارسال شد')
                    ->body('پیام با موفقیت به ' . count($phoneNumbers) . ' شماره ارسال شد')
                    ->success()
                    ->send();
            } else {
                Log::error($response);
                $errorMessage = $response['meta']['message'] ?? 'خطا در ارسال پیامک';
                Notification::make()
                    ->title('خطا در ارسال')
                    ->body($errorMessage)
                    ->danger()
                    ->send();
            }
            
        } catch (\Exception $e) {
            Log::error($e);
            Notification::make()
                ->title('خطا در ارسال')
                ->body('خطا: ' . $e->getMessage())
                ->danger()
                ->send();
        }
        
        $this->form->fill();
    }
    
    private function sendSmsToIPPanel(string $token, array $data): ?array
    {
        $baseUrl = env('IPPANEL_BASE_URL', 'https://edge.ippanel.com/v1');
        $endpoint = $baseUrl . '/api/send';
        
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->timeout(30)
            ->post($endpoint, $data);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('HTTP Error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('HTTP Request Error: ' . $e->getMessage());
        }
    }
} 