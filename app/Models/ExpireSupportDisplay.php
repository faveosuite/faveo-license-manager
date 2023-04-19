<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpireSupportDisplay extends Model
{
    use HasFactory;
    protected $table = 'expire_support_display';
    public function license_details(){
        return $this->hasOne(AflLicenses::class);
    }
}
