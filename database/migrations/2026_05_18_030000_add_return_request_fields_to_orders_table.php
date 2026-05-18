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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('return_reason')->nullable()->after('notes');
            $table->string('return_request_status', 20)->nullable()->after('return_reason');
            $table->timestamp('return_requested_at')->nullable()->after('return_request_status');
            $table->timestamp('return_decided_at')->nullable()->after('return_requested_at');

            $table->index('return_request_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['return_request_status']);
            $table->dropColumn([
                'return_reason',
                'return_request_status',
                'return_requested_at',
                'return_decided_at',
            ]);
        });
    }
};
