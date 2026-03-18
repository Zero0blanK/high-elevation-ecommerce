<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->after('code');
            $table->decimal('maximum_discount', 10, 2)->nullable()->after('minimum_amount');
            $table->integer('usage_limit_per_customer')->nullable()->after('used_count');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['description', 'maximum_discount', 'usage_limit_per_customer']);
        });
    }
};
