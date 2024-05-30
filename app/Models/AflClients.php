<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Crypt;

class AflClients extends Model
{
    use HasFactory,HasApiTokens;

    protected $table ="users";

    protected $guarded = [];

    protected $hidden = [
        'client_password',
        'remember_token',
    ];
    protected $fillable = [
        'client_id',
        'client_fname',
        'client_lname',
        'client_username',
        'client_email',
        'client_mobile',
        'client_mobile_code',
        'client_timezone_id',
        'client_profile_pic',
        'google2fa_secret',
        'google2fa_activation_date',
        'is_2fa_enabled',
    ];

    protected $primaryKey = 'client_id';

    public function license()
    {
        return $this->hasMany(AflLicenses::class);
    }

    public function installation()
    {
        return $this->hasMany(AflInstallations::class);
    }

    public function updateInstallation()
    {
        return $this->hasMany(AfuInstallations::class);
    }
    public function callbacks()
    {
        return $this->hasMany(AflCallbacks::class, 'client_id', 'client_id');
    }
    public function getClientProfilePicAttribute($value)
    {
        $image = $this->attributes['client_email'] ? \Gravatar::src($this->attributes['client_email']) : asset('themes/default/img/default.png');

        if ($value) {
            $filePath = storage_path('app/public/common/images/users/' . $value);
            if (is_file($filePath)) {
                $mime = \File::mimeType($filePath);
                $extension = \File::extension($filePath);
                if (str_starts_with($mime, 'image/') && in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $image = asset('storage/common/images/users/' . $value);
                }
            }
        }

        return $image;
    }
    protected function google2faSecret(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $value ? Crypt::decrypt($value) : null;
            },
            set: function ($value) {
                return $value ? Crypt::encrypt($value) : null;
            }
        );
    }
}
