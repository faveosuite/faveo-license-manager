<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AfuVersions extends Model
{
    use HasFactory;

    protected $guarded =[];
    protected $primaryKey = 'version_id';
    public $timestamps = false;

    public function product(){
        return $this->hasMany(AflProducts::class);
    }
    public function updateInstallation(){
        return $this->hasMany(AfuInstallations::class);
    }
}
