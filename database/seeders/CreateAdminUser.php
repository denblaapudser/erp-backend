<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Mattias Kieler',
            'email' => 'mattias@mkieler.com',
            'password' => bcrypt('test1234')
        ])->accesses()->attach(UserAccess::all()->pluck('id'));
    }
}
