<?php

namespace App\Http\Controllers\Admin;


use App\Exceptions\NonLoggableException;
use App\Models\ExceptionLog;
use App\Models\LogCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Handles all write related operations while logging
 * NOTE: while passing any category, please make sure that this category exists in LogSeeder.php
 *
 * @author sandesh menath <sandesh.menath@ladybirdweb.com>
 */
class LogWriteController
{
    /**
     * Logs exception along with trace
     *
     * @param  Exception|object  $e exception
     * @param  string  $category category to which it belongs
     * @return null
     */
    public function exception($e, $category = 'default')
    {
        try {
            if (! ($e instanceof NonLoggableException)) {
                $category = LogCategory::FirstOrCreate(['name' => $category]);

                return $category->exception()->create([
                    'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
                    'trace' => nl2br($e->getTraceAsString()),
                ]);
            }
        } catch(\Exception $e) {
            // ignore exception
            // Most probably this scenario will not arrive but just for fallback, since this might result is auto-update failure
        }
    }
}
