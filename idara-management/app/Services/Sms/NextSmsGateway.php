<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implementation mbadala ya NextSMS (angalia stacks.md §4). Kama Beem,
 * THIBITISHA muundo halisi wa request/response dhidi ya
 * https://messaging-service.co.tz/ (NextSMS) API docs kabla ya production.
 */
class NextSmsGateway implements SmsGatewayInterface
{
    public function __construct(
        private readonly string $username,
        private readonly string $password,
        private readonly string $senderId,
    ) {}

    public function send(string $phoneNumber, string $message): SmsSendResult
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->acceptJson()
                ->post('https://messaging-service.co.tz/api/sms/v1/text/single', [
                    'from' => $this->senderId,
                    'to' => $this->normalize($phoneNumber),
                    'text' => $message,
                ]);

            if ($response->successful()) {
                return SmsSendResult::success($response->json('messages.0.messageId'));
            }

            Log::warning('NextSMS haikutumwa', ['phone' => $phoneNumber, 'response' => $response->body()]);

            return SmsSendResult::failure("NextSMS API error: HTTP {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('NextSMS exception', ['phone' => $phoneNumber, 'error' => $e->getMessage()]);

            return SmsSendResult::failure($e->getMessage());
        }
    }

    private function normalize(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '255'.substr($phone, 1);
        }

        return $phone;
    }
}
