<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TicketStatus::updateOrCreate(['name' => 'New']);
        TicketStatus::updateOrCreate(['name' => 'Open']);
        TicketStatus::updateOrCreate(['name' => 'In Progress']);
        TicketStatus::updateOrCreate(['name' => 'Pending']);
        TicketStatus::updateOrCreate(['name' => 'Resolved']);
        TicketStatus::updateOrCreate(['name' => 'Closed']);
    }
}
