<?php

namespace App\Services\Sms;

/**
 * Angalia architecture.md §2.4: "Interface ya SmsGatewayInterface yenye
 * implementation ya BeemSmsGateway (au NextSmsGateway) - hii inaruhusu
 * kubadilisha gateway bila kubadilisha code ya biashara".
 */
interface SmsGatewayInterface
{
    public function send(string $phoneNumber, string $message): SmsSendResult;
}
