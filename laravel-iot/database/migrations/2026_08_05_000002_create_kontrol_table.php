<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontrol', function (Blueprint $table) {
            $table->id();
            $table->string('LED', 3)->default('OFF');
            $table->timestamps();
        });

        DB::table('kontrol')->insert([
            'LED' => 'OFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kontrol');
    }
};
