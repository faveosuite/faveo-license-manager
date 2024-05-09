<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateFormat extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'date_formats';

    protected $fillable = ['id', 'format', 'is_active'];
}
