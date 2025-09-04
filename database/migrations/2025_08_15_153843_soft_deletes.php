<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Define which tables should get soft deletes
    protected $tables = [
        'products' => 'product',
        'categories' => 'category', 
        'users' => 'user',
        'orders' => 'order',
        'customers' => 'customer',
        'product_images' => 'productimage'
    ];

    public function up()
    {
        foreach ($this->tables as $table => $configKey) {
            if (config("soft_deletes.models.{$configKey}", true)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->softDeletes();
                    }
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table => $configKey) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};