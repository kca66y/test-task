<?php

namespace App\Http\Controllers\Api;

use App\DTO\GuideFiltersData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guide\IndexRequest;
use App\Http\Resources\GuideResource;
use App\Services\GuideService;
use Illuminate\Http\JsonResponse;

class GuideController extends Controller
{
    public function __construct(
        protected GuideService $service
    ) {}

    public function index(IndexRequest $request): JsonResponse
    {
        $filters = GuideFiltersData::fromArray($request->validated());

        $guides = $this->service->getActiveGuides($filters);

        return response()->json([
            'status' => 'success',
            'data' => GuideResource::collection($guides),
        ]);
    }
}
