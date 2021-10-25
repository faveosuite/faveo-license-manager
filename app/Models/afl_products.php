<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflProducts extends Model
{
    use HasFactory;
    protected $guarded =[];

    protected $primaryKey = 'product_id';
}
