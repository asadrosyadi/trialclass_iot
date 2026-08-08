<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $table = 'sensor';

    protected $fillable = [
        'suhu',
        'kelembapan',
        'potensio',
    ];

    public static function latestData(): ?self
    {
        return static::query()->orderByDesc('id')->first();
    }
}
