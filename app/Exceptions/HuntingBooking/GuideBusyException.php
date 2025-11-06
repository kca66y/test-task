<?php

namespace App\Exceptions\HuntingBooking;

use App\Contracts\HuntingBooking\HuntingBookingExceptionInterface;
use Exception;

class GuideBusyException extends Exception implements HuntingBookingExceptionInterface
{
    protected $message = 'Гид уже занят на указанную дату.';

    public function getErrors(): array
    {
        return ['guide_id' => [$this->message]];
    }
}
