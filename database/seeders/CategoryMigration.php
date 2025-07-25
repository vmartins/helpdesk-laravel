<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryMigration extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::updateOrCreate([
            'unit_id' => 1,
            'name' => ' One at Sales Department',
        ]);

        Category::updateOrCreate([
            'unit_id' => 1,
            'name' => ' Two at Sales Department',
        ]);
        
        Category::updateOrCreate([
            'unit_id' => 2,
            'name' => ' One at Technical Support',
        ]);

        Category::updateOrCreate([
            'unit_id' => 3,
            'name' => ' One at Billing Support',
        ]);
    }
}
