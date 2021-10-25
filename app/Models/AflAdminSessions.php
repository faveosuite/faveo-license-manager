<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflAdminSessions extends Model
{
    use HasFactory;
    protected $fillable = ['admin_hash',
            'admin_session_hash',
            'admin_session_date',
            'admin_session_expiry_date' ];
}
