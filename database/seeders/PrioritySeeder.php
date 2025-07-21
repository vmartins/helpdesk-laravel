<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::create(['name' => 'Normal', 'sla' => 2880]);
        Priority::create(['name' => 'Urgent', 'sla' => 1440]);
        Priority::create(['name' => 'Critical', 'sla' => 480]);
    }
}
