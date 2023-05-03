<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflLicenses extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'license_id';

    public $timestamps = false;

    public function client()
    {
        return $this->belongsToMany(AflClients::class);
    }

    public function product()
    {
        return $this->hasMany(AflProducts::class);
    }

    public function products()
    {
        return $this->belongsTo(AflProducts::class, 'product_id', 'product_id');
    }

    public function installations()
    {
        return $this->hasMany(AflInstallations::class, 'license_code', 'license_code');
    }

    public function callbacks()
    {
        return $this->hasMany(AflCallbacks::class, 'license_code', 'license_code')->select('callback_date_time')->latest('callback_date_time');
    }
}
