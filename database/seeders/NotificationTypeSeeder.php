<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use App\Notifications\LowInventoryStockAlert;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationType::firstOrCreate(
            ['type' => LowInventoryStockAlert::class],
            [
                'label' => 'Lav lagerbeholdning', 
                'send_by_default' => false, 
                'description' => 'Send notifikation når lagerbeholdningen er lav.'
            ]
        );
    }
}
