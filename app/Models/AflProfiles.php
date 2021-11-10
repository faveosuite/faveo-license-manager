<?php

namespace App\Models;


use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class AflProfiles extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable =

        ['admin_id',
        'admin_fname',
        'admin_lname',
        'admin_email',
        'admin_password',
        'admin_ip',
        'admin_date',
        'admin_reset',
        'admin_hash'];


public function AauthAcessToken(){
    return $this->hasMany('App\Models\oauth_access_token');
}

}
