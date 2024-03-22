<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    use HasFactory;

    protected $table = 'exception_logs';

    protected $fillable = ['log_category_id', 'file', 'line', 'trace', 'message'];

    public function category()
    {
        return $this->belongsTo(\App\Models\LogCategory::class, 'log_category_id');
    }
}
