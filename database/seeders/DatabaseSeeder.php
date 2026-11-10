<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Dev',
            'email' => 'dev@gmail.com',
            'usertype' => 'dev',
        ]);

        User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor@gmail.com',
            'usertype' => 'vendor',
        ]);

        Vendor::create([
            'name' => 'Grafika Printing',
            'email' => 'grafika@gmail.com',
            'phone' => '081234567890',
            'address' => 'Jl. Grafika No. 1',
            'website' => 'grafika-printing.com',
            'is_active' => true
        ])->vendorUser()->attach(2);
    }
}
