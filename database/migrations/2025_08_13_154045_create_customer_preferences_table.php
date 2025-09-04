<?php

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
        Schema::create('customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('preferred_roast_level', 50)->nullable();
            $table->string('preferred_grind_type', 50)->nullable();
            $table->text('favorite_origins')->nullable();
            $table->boolean('marketing_emails')->default(true);
            $table->boolean('order_notifications')->default(true);
            $table->timestamps();
            
            $table->unique('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_preferences');
    }
};
