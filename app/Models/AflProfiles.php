<?php

namespace App\Models;

<<<<<<< HEAD

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

=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflProfiles extends Model
{
    use HasFactory;
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)
}
