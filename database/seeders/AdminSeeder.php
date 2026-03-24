<?php
namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@fasolivre.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );

        Admin::updateOrCreate(
            ['email' => 'edito@fasolivre.com'],
            [
                'name' => 'Editor',
                'password' => Hash::make('password456'),
            ]
        );
    }
}
