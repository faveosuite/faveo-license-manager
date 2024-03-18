<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class GoogleRecaptchaSetting extends Model
{
    protected $table = 'google_recaptcha_settings';

    protected $fillable = [
        'google_site_key',
        'google_secret_key',
    ];

    public $timestamps = false;
}
