<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfuProducts extends Model
{
    use HasFactory;
    protected $guarded =[];

    protected $primaryKey = 'product_id';
    public $timestamps = false;

    public function version(){
        return $this->belongsToMany(AfuVersions::class);
    }
    public function updateInstallations(){
        return $this->belongsTo(AfuInstallations::class);
    }
}
