<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflInstallations extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'installation_id';

    public function product(){
        return $this->hasMany(AflProducts::class);
    }
}
