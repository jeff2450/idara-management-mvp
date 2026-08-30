<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implementation ya Beem Africa (angalia stacks.md §4). Muundo wa ujumbe
 * hapa chini ni kulingana na muundo wa kawaida wa REST API ya Beem
 * (base64 auth ya api_key:secret_key + JSON body) - THIBITISHA dhidi ya
 * https://beem.africa/dashboard/api-documentation kabla ya kutumia
 * production, kwani maelezo ya API yanaweza kubadilika.
 */
class BeemSmsGateway implements SmsGatewayInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $secretKey,
        private readonly string $senderId,
    ) {}

    public function send(string $phoneNumber, string $message): SmsSendResult
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
                ->acceptJson()
                ->post('https://apisms.beem.africa/v1/send', [
                    'source_addr' => $this->senderId,
                    'encoding' => 0,
                    'message' => $message,
                    'recipients' => [
                        ['recipient_id' => 1, 'dest_addr' => $this->normalize($phoneNumber)],
                    ],
                ]);

            if ($response->successful()) {
                return SmsSendResult::success($response->json('request_id'));
            }

            Log::warning('Beem SMS haikutumwa', ['phone' => $phoneNumber, 'response' => $response->body()]);

            return SmsSendResult::failure("Beem API error: HTTP {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Beem SMS exception', ['phone' => $phoneNumber, 'error' => $e->getMessage()]);

            return SmsSendResult::failure($e->getMessage());
        }
    }

    /** Beem inahitaji namba za kimataifa bila '+' (mfano 255712345678). */
    private function normalize(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '255'.substr($phone, 1);
        }

        return $phone;
    }
}
