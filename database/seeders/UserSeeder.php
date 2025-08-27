<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. create a super admin
        $superAdmin = User::updateOrCreate([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ], [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        $superAdmin->units()->sync(Unit::all());
        $superAdmin->syncRoles('Super Admin');

        // 2. create a admin unit
        $adminUnit = User::updateOrCreate([
            'name' => 'Admin Unit',
            'email' => 'adminunit@example.com',
        ], [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        $adminUnit->units()->sync(1);
        $adminUnit->syncRoles('Admin Unit');

        // 3. create a staff unit
        $staffUnit = User::updateOrCreate([
            'name' => 'Staff Unit',
            'email' => 'staffunit@example.com',
        ], [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        $staffUnit->units()->sync(1);
        $staffUnit->syncRoles('Staff Unit');

        // 4. create a user
        $staffUnit = User::updateOrCreate([
            'name' => 'User',
            'email' => 'user@example.com',
        ], [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}
