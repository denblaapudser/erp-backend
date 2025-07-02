<?php

use App\Models\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('qty');
            $table->boolean('should_alert')->default(false);
            $table->integer('alert_threshold')->default(0);
            $table->string('restock_url')->nullable();
            $table->foreignIdFor(Image::class)->nullable()->constrained('images')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
