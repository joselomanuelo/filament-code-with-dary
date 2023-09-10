<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@filament.com'
        ]);
    }
}
