<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('model_has_units', function (Blueprint $table): void {
            $table->foreignId('unit_id')->constrained();
            $table->morphs('model');

            $table->primary(['unit_id', 'model_id', 'model_type']);
        });

        DB::unprepared('
            INSERT INTO model_has_units (unit_id, model_type, model_id)
            SELECT unit_id, "App\\\Models\\\User", id
            FROM users
            WHERE users.unit_id IS NOT NULL
        ');

        DB::unprepared('
            INSERT INTO model_has_units (unit_id, model_type, model_id)
            SELECT unit_id, "App\\\Models\\\Ticket", id
            FROM tickets
            WHERE tickets.unit_id IS NOT NULL
        ');

        DB::unprepared('
            INSERT INTO model_has_units (unit_id, model_type, model_id)
            SELECT unit_id, "App\\\Models\\\Category", id
            FROM categories
            WHERE categories.unit_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_units');
    }
};
