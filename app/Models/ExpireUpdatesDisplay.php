<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpireUpdatesDisplay extends Model
{
    use HasFactory;
    protected $table = 'expire_updates_display';
    // protected $guarded = [];

    public function license_details(){
        return $this->hasOne(AflLicenses::class);
    }
}
