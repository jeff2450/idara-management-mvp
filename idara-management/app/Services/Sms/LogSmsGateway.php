<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Gateway "bandia" kwa mazingira ya local/testing - haiiti mtandao wowote,
 * inaandika tu ujumbe kwenye log (storage/logs/laravel.log). Hii ndiyo
 * SMS_DRIVER default (angalia config/services.php) ili mfumo uweze
 * kujaribiwa bila akaunti halisi ya Beem/NextSMS.
 */
class LogSmsGateway implements SmsGatewayInterface
{
    public function send(string $phoneNumber, string $message): SmsSendResult
    {
        Log::info('[SMS - simulizi]', ['to' => $phoneNumber, 'message' => $message]);

        return SmsSendResult::success(providerMessageId: 'log-'.uniqid());
    }
}
