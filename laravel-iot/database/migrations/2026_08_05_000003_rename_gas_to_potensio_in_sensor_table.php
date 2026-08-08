<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE sensor CHANGE gas potensio INT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sensor CHANGE potensio gas INT NULL');
    }
};
