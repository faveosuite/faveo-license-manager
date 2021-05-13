<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflLicenses extends Model
{
    use HasFactory;
<<<<<<< HEAD

    protected $guarded=[];
    protected $primaryKey = 'license_id';
    public $timestamps = false;

    public function client(){
        
        return $this->belongsToMany(AflClients::class);
    }

    public function product()
    {
         return $this->hasMany(AflProducts::class);
    }
=======
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)
}
