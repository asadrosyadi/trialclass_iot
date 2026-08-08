<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrol extends Model
{
    protected $table = 'kontrol';

    protected $fillable = [
        'LED',
    ];

    public static function current(): self
    {
        return static::query()->orderByDesc('id')->first()
            ?? static::create(['LED' => 'OFF']);
    }
}
