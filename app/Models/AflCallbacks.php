<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflCallbacks extends Model
{
    use HasFactory;

     protected $guarded=[];
    protected $primaryKey = 'callback_id';
    public $timestamps = false;
}
