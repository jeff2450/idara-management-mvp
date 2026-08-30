<?php

namespace App\Providers;

use App\Services\Sms\BeemSmsGateway;
use App\Services\Sms\LogSmsGateway;
use App\Services\Sms\NextSmsGateway;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Inafunga SmsGatewayInterface na implementation sahihi kulingana na
 * `config('services.sms.driver')` - angalia config/services.php na
 * architecture.md §2.4. Kubadilisha gateway ni suala la kubadilisha
 * SMS_DRIVER kwenye .env, siyo kuandika upya code ya biashara.
 */
class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsGatewayInterface::class, function () {
            return match (config('services.sms.driver', 'log')) {
                'beem' => new BeemSmsGateway(
                    apiKey: (string) config('services.sms.beem.api_key'),
                    secretKey: (string) config('services.sms.beem.secret_key'),
                    senderId: (string) config('services.sms.beem.sender_id'),
                ),
                'nextsms' => new NextSmsGateway(
                    username: (string) config('services.sms.nextsms.username'),
                    password: (string) config('services.sms.nextsms.password'),
                    senderId: (string) config('services.sms.nextsms.sender_id'),
                ),
                default => new LogSmsGateway,
            };
        });
    }
}
