<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorReadingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'suhu' => $this->suhu !== null ? (float) $this->suhu : null,
            'kelembapan' => $this->kelembapan !== null ? (float) $this->kelembapan : null,
            'potensio' => $this->potensio !== null ? (int) $this->potensio : null,
            'recorded_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
