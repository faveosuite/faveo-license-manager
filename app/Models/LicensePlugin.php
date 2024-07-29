<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensePlugin extends Model
{
    use HasFactory;

    protected $table = 'license_plugins';

    protected $guarded = [];

    // Define the relationship with AflLicense
    public function pluginLicense()
    {
        return $this->belongsTo(AflLicenses::class, 'license_id', 'license_id');
    }

    // Define the relationship with AflProduct
    public function pluginProduct()
    {
        return $this->belongsTo(AflProducts::class, 'product_id', 'product_id');
    }
}

