<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorReadingRequest;
use App\Http\Resources\SensorReadingResource;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SensorReadingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->integer('per_page', 20), 100);

        $readings = Sensor::query()
            ->latest('id')
            ->paginate($perPage);

        return SensorReadingResource::collection($readings);
    }

    public function store(StoreSensorReadingRequest $request): JsonResponse
    {
        $reading = Sensor::create($request->validated());

        return SensorReadingResource::make($reading)
            ->response()
            ->setStatusCode(201);
    }

    public function latest(): SensorReadingResource
    {
        $reading = Sensor::latestData();

        abort_if(! $reading, 404, 'Belum ada data sensor.');

        return SensorReadingResource::make($reading);
    }
}
