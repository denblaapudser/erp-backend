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
        $adminAccess = UserAccess::firstOrCreate(
            ['type' => 'adminAccess'],
            [
                'label' => 'Admin adgang', 
                'description' => 'Giver brugeren mulighed for at kunne logge ind i administrationssystemet.'
            ],
        );
        UserAccess::firstOrCreate(
            ['type' => 'editUsers'],
            [
                'child_of' => $adminAccess->id,
                'label' => 'Redigér brugere',
                'description' => 'Giver brugeren mulighed for at redigere andre brugere.'
            ]
        );
        UserAccess::firstOrCreate(
            ['type' => 'editInventory'],
            [
                'child_of' => $adminAccess->id,
                'label' => 'Redigér lager',
                'description' => 'Giver brugeren mulighed for at redigere lageret.'
            ]
        );
        UserAccess::firstOrCreate(
            ['type' => 'employeeERPAccess'],
            [
                'label' => 'Medarbejder adgang',
                'description' => 'Giver brugeren mulighed for at logge ind i medarbejder systemet.'
            ]
        );
    }
}
