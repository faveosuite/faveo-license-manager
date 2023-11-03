<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
