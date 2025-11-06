<?php

namespace App\Exceptions\HuntingBooking;

use App\Contracts\HuntingBooking\HuntingBookingExceptionInterface;
use Exception;

class PastTourDateException extends Exception implements HuntingBookingExceptionInterface
{
    protected $message = 'Нельзя забронировать тур на прошедшую дату.';

    public function getErrors(): array
    {
        return ['date' => [$this->message]];
    }
}
