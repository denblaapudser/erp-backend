<?php

namespace Database\Seeders;

use App\Models\UserAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAccesses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserAccess::firstOrCreate(
            ['label' => 'Admin adgang'],
            ['description' => 'Giver brugeren mulighed for at kunne logge ind i administrationssystemet.']
        );
        UserAccess::firstOrCreate(
            ['label' => 'Redigér brugere'],
            ['description' => 'Giver brugeren mulighed for at redigere andre brugere.']
        );
        UserAccess::firstOrCreate(
            ['label' => 'Redigér lager'],
            ['description' => 'Giver brugeren mulighed for at redigere lageret.']
        );
    }
}
