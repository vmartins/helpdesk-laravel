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
        Priority::updateOrCreate(['name' => 'Normal', 'sla' => 2880]);
        Priority::updateOrCreate(['name' => 'Urgent', 'sla' => 1440]);
        Priority::updateOrCreate(['name' => 'Critical', 'sla' => 480]);
    }
}
