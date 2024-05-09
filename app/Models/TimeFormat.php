<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeFormat extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'time_formats';

    protected $fillable = ['id', 'format', 'hours', 'is_active'];
}
