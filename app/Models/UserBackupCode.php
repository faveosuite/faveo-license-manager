<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBackupCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'backup_codes'
    ];
}
