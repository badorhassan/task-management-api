<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create a manager
        $manager = User::create([
            'name' => 'manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');

        // Create regular users
        $user1 = User::create([
            'name' => 'user-1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);
        $user1->assignRole('user');

        $user2 = User::create([
            'name' => 'user-2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);
        $user2->assignRole('user');
    }
}
