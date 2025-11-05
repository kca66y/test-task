<?php

namespace App\Exceptions\HuntingBooking;

use App\Contracts\HuntingBooking\HuntingBookingExceptionInterface;
use Exception;

class GuideNotActiveException extends Exception implements HuntingBookingExceptionInterface
{
    protected $message = 'Выбранный гид не найден или неактивен.';

    public function getErrors(): array
    {
        return ['guide_id' => [$this->message]];
    }
}
