<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLedStatusRequest;
use App\Http\Resources\LedStatusResource;
use App\Models\Kontrol;

class LedStatusController extends Controller
{
    public function show(): LedStatusResource
    {
        return LedStatusResource::make(Kontrol::current());
    }

    public function update(UpdateLedStatusRequest $request): LedStatusResource
    {
        $kontrol = Kontrol::current();
        $kontrol->update(['LED' => $request->validated('status')]);

        return LedStatusResource::make($kontrol);
    }

    public function toggle(): LedStatusResource
    {
        $kontrol = Kontrol::current();
        $kontrol->update(['LED' => $kontrol->LED === 'ON' ? 'OFF' : 'ON']);

        return LedStatusResource::make($kontrol);
    }
}
