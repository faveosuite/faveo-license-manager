<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflReports extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'report_id';
}
