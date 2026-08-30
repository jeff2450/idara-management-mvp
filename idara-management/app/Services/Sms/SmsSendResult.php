<?php

namespace App\Services\Sms;

/**
 * Matokeo ya kujaribu kutuma SMS moja - angalia SmsGatewayInterface.
 */
final class SmsSendResult
{
    public function __construct(
        public readonly bool $delivered,
        public readonly ?string $providerMessageId = null,
        public readonly ?string $error = null,
    ) {}

    public static function success(?string $providerMessageId = null): self
    {
        return new self(delivered: true, providerMessageId: $providerMessageId);
    }

    public static function failure(string $error): self
    {
        return new self(delivered: false, error: $error);
    }
}
