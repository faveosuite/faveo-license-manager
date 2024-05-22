<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AflReports extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'report_id';
    public function user() {
        return $this->belongsTo(AflClients::class, 'account_id', 'client_id');
    }
    public function product() {
        return $this->belongsTo(AflProducts::class, 'product_id');
    }
    public function scopeWithUserFormatted($query)
    {
        $query->addSelect([
            'user_formatted' => User::select(DB::raw("
                CASE
                    WHEN client_email LIKE '%_@__%.__%' THEN
                        CASE
                            WHEN client_fname IS NOT NULL AND client_lname IS NOT NULL THEN CONCAT(client_fname, ' ', client_lname)
                            ELSE client_email
                        END
                    ELSE 'System'
                END
            "))
                ->whereColumn('afl_reports.account_id', 'users.client_id')
                ->limit(1)
        ]);
    }
}
