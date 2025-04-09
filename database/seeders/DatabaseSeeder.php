<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@famsheets.com',
            'password' => bcrypt('admin'),
        ]);

        \App\Models\Category::factory()->create([
            'name' => 'Food',
        ]);
        \App\Models\Category::factory()->create([
            'name' => 'Gas',
        ]);
        \App\Models\Category::factory()->create([
            'name' => 'Rent/Mortgage',
        ]);
        \App\Models\Category::factory()->create([
            'name' => 'Utilities',
        ]);
        \App\Models\Category::factory()->create([
            'name' => 'Other',
        ]);
    }
}
