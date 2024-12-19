<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fetch the data types of parent columns dynamically
        $licenseIdType = $this->getColumnType('afl_licenses', 'license_id');
        $productIdType = $this->getColumnType('afl_products', 'product_id');

        Schema::create('license_options', function (Blueprint $table) use ($licenseIdType, $productIdType) {
            $table->id();

            // Define the child columns based on parent column types
            $this->addColumnType($table, 'license_id', $licenseIdType);
            $this->addColumnType($table, 'product_id', $productIdType);

            $table->string('option_group', 255);
            $table->string('option_name', 255);
            $table->string('key', 255);
            $table->string('value', 255);

            // Foreign key constraints
            $table->foreign('license_id')->references('license_id')->on('afl_licenses')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('afl_products')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('license_options');
    }

    /**
     * Get the column type of a specific column in a table.
     *
     * @param string $tableName
     * @param string $columnName
     * @return string
     */
    private function getColumnType($tableName, $columnName)
    {
        $column = DB::selectOne("SHOW COLUMNS FROM `$tableName` WHERE Field = ?", [$columnName]);

        if ($column) {
            return $column->Type;
        }

        throw new \Exception("Column `$columnName` not found in table `$tableName`.");
    }

    /**
     * Add a column with a specific type to the table schema.
     *
     * @param Blueprint $table
     * @param string $columnName
     * @param string $columnType
     */
    private function addColumnType(Blueprint $table, $columnName, $columnType)
    {
        if (str_contains($columnType, 'int')) {
            if (str_contains($columnType, 'bigint')) {
                $table->unsignedBigInteger($columnName);
            } elseif (str_contains($columnType, 'mediumint')) {
                $table->mediumInteger($columnName);
            } elseif (str_contains($columnType, 'smallint')) {
                $table->unsignedSmallInteger($columnName);
            } else {
                $table->unsignedInteger($columnName);
            }
        } else {
            throw new \Exception("Unsupported column type: `$columnType` for column `$columnName`.");
        }
    }
};
