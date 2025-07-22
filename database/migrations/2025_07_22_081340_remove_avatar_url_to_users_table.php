<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'solved_at']);
            $table->timestamp('status_updated_at')->after('ticket_statuses_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['status_updated_at']);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('solved_at')->nullable();
        });
    }
};
