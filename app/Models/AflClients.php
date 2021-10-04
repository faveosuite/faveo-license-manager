<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflClients extends Model
{
    use HasFactory;
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
    protected $guarded =[];

    protected $primaryKey = 'client_id';

    public function license()
    {
        return $this->hasMany(AflLicenses::class);
    }

    public function installation()
    {
        return $this->hasMany(AflInstallations::class);
    }
<<<<<<< HEAD
=======
   protected $guarded =[];

    protected $primaryKey = 'client_id';
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)
=======

    public function updateInstallation()
    {
        return $this->hasMany(AfuInstallations::class);
    }
>>>>>>> 22c0e54 (table changes)
}
