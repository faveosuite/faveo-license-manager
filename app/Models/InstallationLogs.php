<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'installation_domain',
        'version_id',
        'installation_ip',
        'installation_status',
        'installation_last_active_date',
    ];
    public function version()
    {
        return $this->belongsTo(AfuVersions::class,'version_id',';version_id');
    }
}
