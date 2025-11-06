<?php

namespace App\Http\Controllers\Api;

use App\DTO\HuntingBookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\HuntingBooking\StoreRequest;
use App\Http\Resources\HuntingBookingResource;
use App\Services\HuntingBookingService;
use Illuminate\Http\JsonResponse;

class HuntingBookingController extends Controller
{
    public function __construct(
        protected HuntingBookingService $service
    ) {}

    /**
     * POST /api/v1/bookings
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $dto = HuntingBookingData::fromArray($request->validated());

        $booking = $this->service->create($dto);

        return response()->json([
            'status' => 'success',
            'data' => new HuntingBookingResource($booking),
        ], 201);
    }
}
