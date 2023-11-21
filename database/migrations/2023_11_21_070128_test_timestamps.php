<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'created_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->timestamps();
                });
            }
        }
      

        $columnInfo = DB::select("SHOW COLUMNS FROM afl_api_keys WHERE Field = 'api_key_ip'")[0];
        $isNullable = $columnInfo->Null === 'YES';

        if ($isNullable === false) {
            DB::statement("ALTER TABLE afl_api_keys MODIFY api_key_ip VARCHAR(125) NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
    protected function getAllTables()
    {
        $tables = [];

        $tablesRaw = DB::select('SHOW TABLES');

        foreach ($tablesRaw as $table) {
            $tables[] = reset($table);
        }

        return $tables;
    }
};
