<?php

namespace App\Exceptions\HuntingBooking;

use App\Contracts\HuntingBooking\HuntingBookingExceptionInterface;
use Exception;

class InvalidParticipantsCountException extends Exception implements HuntingBookingExceptionInterface
{
    protected $message = 'Количество участников должно быть от 1 до 10.';

    public function getErrors(): array
    {
        return ['participants_count' => [$this->message]];
    }
}
