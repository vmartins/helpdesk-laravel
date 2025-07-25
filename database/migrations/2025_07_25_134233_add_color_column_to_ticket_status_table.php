<?php

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
        Schema::table('ticket_statuses', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->comment('Status color hex code (e.g.: #7bd158)')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_statuses', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};