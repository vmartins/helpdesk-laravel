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
            'name' => 'One at Sales Department',
        ])->units()->sync(1);

        Category::updateOrCreate([
            'name' => 'Two at Sales Department',
        ])->units()->sync(1);
        
        Category::updateOrCreate([
            'name' => 'One at Technical Support',
        ])->units()->sync(2);

        Category::updateOrCreate([
            'name' => 'One at Billing Support',
        ])->units()->sync(3);
    }
}
