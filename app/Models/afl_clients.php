<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflClients extends Model
{
    use HasFactory;
    protected $guarded =[];

    protected $primaryKey = 'client_id';

    public function license()
    {
        return $this->hasMany(AflLicenses::class);
    }
}
