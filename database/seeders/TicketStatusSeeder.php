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
        TicketStatus::insert([
            ['name' => 'New'],
            ['name' => 'Open'],
            ['name' => 'In Progress'],
            ['name' => 'Pending'],
            ['name' => 'Resolved'],
            ['name' => 'Closed'],
        ]);
    }
}
