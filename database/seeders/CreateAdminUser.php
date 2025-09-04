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
        $user = User::firstOrCreate(
            [
                'username' => 'mkieler'
            ],
            [
                'email' => 'mattias@mkieler.com',
                'name' => 'Mattias Kieler',
                'password' => 'test1234',
                'pin' => '0449',
            ]
        );

        $user->accesses()->sync(UserAccess::all()->pluck('id'));
    }
}
