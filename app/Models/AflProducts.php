<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflProducts extends Model
{
    use HasFactory;
    protected $guarded =[];

    protected $primaryKey = 'product_id';
<<<<<<< HEAD
    public $timestamps = false;

    public function license()
    {
        return $this->belongTo(AflLicenses::class);
    }

    public function installation(){
        
        return $this->belongsTo(AflInstallations::class);
    }
=======
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)
}
