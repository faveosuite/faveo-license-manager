<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflProducts extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'product_id';

    public $timestamps = false;

    public function licenses()
    {
        return $this->hasMany(AflLicenses::class, 'product_id', 'product_id');
    }

    public function installations()
    {
        return $this->hasMany(AflInstallations::class, 'product_id', 'product_id');
    }

    public function callbacks()
    {
        return $this->hasMany(AflCallbacks::class, 'product_id', 'product_id');
    }

    public function reports()
    {
        return $this->hasMany(AflReports::class, 'product_id', 'product_id');
    }

    public function versions()
    {
        return $this->hasMany(AfuVersions::class,'product_id','product_id');
    }
}
