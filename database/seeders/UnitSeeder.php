<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::updateOrCreate(['name' => 'Sales Department']);
        Unit::updateOrCreate(['name' => 'Technical Support']);
        Unit::updateOrCreate(['name' => 'Billing Support']);
    }
}
