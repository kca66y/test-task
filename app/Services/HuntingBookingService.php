<?php

namespace App\Services;

use App\DTO\HuntingBookingData;
use App\Exceptions\HuntingBooking\GuideBusyException;
use App\Exceptions\HuntingBooking\GuideNotActiveException;
use App\Exceptions\HuntingBooking\InvalidParticipantsCountException;
use App\Exceptions\HuntingBooking\PastTourDateException;
use App\Models\Guide;
use App\Models\HuntingBooking;
use Carbon\Carbon;

class HuntingBookingService
{
    /**
     * @throws GuideBusyException
     * @throws GuideNotActiveException
     * @throws InvalidParticipantsCountException
     * @throws PastTourDateException
     */
    public function create(HuntingBookingData $data): HuntingBooking
    {
        if ($data->participants_count < 1 || $data->participants_count > 10) {
            throw new InvalidParticipantsCountException;
        }

        if ($data->date->isBefore(Carbon::today())) {
            throw new PastTourDateException;
        }

        $guideExistsAndActive = Guide::query()
            ->whereKey($data->guide_id)
            ->where('is_active', true)
            ->exists();

        if (! $guideExistsAndActive) {
            throw new GuideNotActiveException;
        }

        $busy = HuntingBooking::query()
            ->where('guide_id', $data->guide_id)
            ->whereDate('date', $data->date->toDateString())
            ->exists();

        if ($busy) {
            throw new GuideBusyException;
        }

        return HuntingBooking::create($data->toArray());
    }
}
