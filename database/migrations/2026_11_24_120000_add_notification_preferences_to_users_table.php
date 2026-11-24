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
        // Both default to true so current behavior is kept: who is eligible
        // for each email is still decided by the user's role.
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('notify_ticket_created')->default(true)->after('is_guest');
            $table->boolean('notify_ticket_assigned')->default(true)->after('notify_ticket_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['notify_ticket_created', 'notify_ticket_assigned']);
        });
    }
};
