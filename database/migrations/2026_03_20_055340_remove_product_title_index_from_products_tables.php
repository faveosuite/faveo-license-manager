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
        $tables = ['afl_products', 'afu_products'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $indexes = Schema::getIndexes($tableName);

            foreach ($indexes as $index) {
                if (in_array('product_title', $index['columns']) && !$index['primary']) {
                    Schema::table($tableName, function (Blueprint $table) use ($index) {
                        $table->dropIndex($index['name']);
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['afl_products', 'afu_products'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'product_title')) {
                continue;
            }

            $indexes = Schema::getIndexes($tableName);
            $hasProductTitleIndex = false;

            foreach ($indexes as $index) {
                if (in_array('product_title', $index['columns'])) {
                    $hasProductTitleIndex = true;
                    break;
                }
            }

            if (!$hasProductTitleIndex) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index('product_title');
                });
            }
        }
    }
};
