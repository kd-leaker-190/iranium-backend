<?php

namespace App\Jobs;

use App\Models\Notify;
use App\Models\Team;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendNotifyJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public Notify $notify;

    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    public function handle(): void
    {
        Log::error('handling notify job');
        if ($this->notify->sms) {
            $phones = $this->notify->teams->pluck('phone')->toArray();
            $this->sendSms($this->notify->content, $phones);
        }

        if ($this->notify->app) {
            foreach ($this->notify->teams as $team) {
                $team->appNotifications()->create([
                    'title' => $this->notify->title,
                    'content' => $this->notify->content,
                ]);
            }
        }
    }

    public function sendSms(string $content, array $phoneNumbers): void
    {
        $smsToken = config('services.ippanel.token');

        if (count($phoneNumbers) <= 0)
            return;

        $smsData = [
            'sending_type' => 'webservice',
            'from_number' => config('services.ippanel.number'),
            'message' => $content,
            'params' => [
                'recipients' => $phoneNumbers
            ]
        ];
        Log::error('sending sms');
        $this->sendSmsToIPPanel($smsToken, $smsData);
    }

    private function sendSmsToIPPanel(string $token, array $data): ?array
    {
        $baseUrl = config('services.ippanel.base_url');
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
