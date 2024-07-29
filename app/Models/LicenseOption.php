<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseOption extends Model
{
    use HasFactory;

    protected $table = 'license_options';

    protected $guarded = [];

    // Define the relationship with AflLicense
    public function optionLicense()
    {
        return $this->belongsTo(AflLicenses::class, 'license_id', 'license_id');
    }

    public function optionProducts()
    {
        return $this->belongsTo(AflProducts::class, 'product_id', 'product_id');
    }
}

