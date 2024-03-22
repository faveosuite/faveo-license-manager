<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogCategory extends Model
{
    use HasFactory;

    protected $table = 'log_categories';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function exception()
    {
        return $this->hasMany(\App\Models\ExceptionLog::class);
    }
}
