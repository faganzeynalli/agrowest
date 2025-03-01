<?php

declare(strict_types=1);

namespace App\Entity\Api;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Api_Status', type: 'object')]
class Status
{
    #[OA\Property(example: true)]
    public bool $success;

    #[OA\Property(example: 'Dəyişikliklər uğurla yadda saxlanıldı.')]
    public string $message;

    #[OA\Property(example: '<b>Dəyişikliklər uğurla yadda saxlanıldı.</b>')]
    public string $formatted_message;

    public function __construct(
        bool $success = true,
        string $message = 'Dəyişikliklər uğurla yadda saxlanıldı.',
        ?string $formattedMessage = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->formatted_message = $formattedMessage ?? $message;
    }

    public static function success(): self
    {
        return new self(true, __('Dəyişikliklər uğurla yadda saxlanıldı.'));
    }

    public static function created(): self
    {
        return new self(true, __('Məlumat uğurla yaradıldı.'));
    }

    public static function updated(): self
    {
        return new self(true, __('Uğurla yeniləndi.'));
    }

    public static function deleted(): self
    {
        return new self(true, __('Uğurla silindi.'));
    }
}
