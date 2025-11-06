<?php

namespace App\Contracts\HuntingBooking;

interface HuntingBookingExceptionInterface
{
    public function getErrors(): array;

    public function getMessage(): string;
}
