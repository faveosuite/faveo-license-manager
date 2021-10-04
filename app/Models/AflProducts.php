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
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
    public $timestamps = false;

    public function license()
    {
        return $this->belongTo(AflLicenses::class);
    }

    public function installation(){
<<<<<<< HEAD
        
        return $this->belongsTo(AflInstallations::class);
    }
=======
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)
=======

        return $this->belongsTo(AflInstallations::class);
    }

    public function version(){
        return $this->belongsToMany(AfuVersions::class);
    }
    public function updateInstallations(){
        return $this->belongsTo(AfuInstallations::class);
    }

>>>>>>> 22c0e54 (table changes)
}
