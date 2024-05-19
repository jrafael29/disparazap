<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MessageTypeSeeder::class
        ]);
        // \App\Models\User::factory(10)->create();
        \App\Models\User::factory()->create([
            'name' => 'El Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('@Admin2024Power'),
            'isAdmin' => true
        ]);
        \App\Models\User::factory()->create([
            'name' => 'El User',
            'email' => 'user@user.com',
            'password' => Hash::make('@User2024'),
            'isAdmin' => false
        ]);
        \App\Models\User::factory()->create([
            'name' => 'DisparaZap',
            'email' => 'one@mail.com',
            'password' => Hash::make('oneone'),
            'isAdmin' => false
        ]);
        // \App\Models\Contact::factory(10)->create();
        // \App\Models\UserContact::factory(1)->create();
    }
}
