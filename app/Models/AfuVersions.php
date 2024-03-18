<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfuVersions extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'version_id';

    public $timestamps = false;

    public function product()
    {
        return $this->hasMany(AfuProducts::class,'product_id','product_id');
    }

    public function callback()
    {
        return $this->hasMany(AfuCallbacks::class,'version_id','version_id');
    }
    public function updateInstallation()
    {
        return $this->hasMany(AfuInstallations::class);
    }
}
