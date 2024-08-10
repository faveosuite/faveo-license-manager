<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_code',
        'installation_domain',
        'version_number',
        'installation_ip',
        'installation_status',
        'installation_last_active_date',
    ];
}
