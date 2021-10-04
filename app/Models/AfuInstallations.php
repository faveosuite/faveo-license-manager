<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfuInstallations extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'installation_id';
    public $timestamps = false;
    public function product(){
        return $this->hasMany(AflProducts::class);
    }

    public function client(){

        return $this->belongsToMany(AflClients::class);
    }
    public function version(){

        return $this->belongsToMany(AfuVersions::class);
    }
}
