<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflSettings extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'SETTING_ID';
    public $timestamps = false;

}