<?php

namespace Database\Seeders;

use App\Models\InventoryProducts;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestInventoryProducts extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InventoryProducts::factory(150)->create();
    }
}
