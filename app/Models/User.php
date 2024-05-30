<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Crypt;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'client_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'client_iso2',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'client_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
}
