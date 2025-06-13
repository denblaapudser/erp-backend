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
            ['email' => 'mattias@mkieler.com'],
            [
            'name' => 'Mattias Kieler',
            'password' => bcrypt('test1234')
            ]
        );

        $user->accesses()->sync(UserAccess::all()->pluck('id'));
    }
}
