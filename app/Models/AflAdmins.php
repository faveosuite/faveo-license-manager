<?php

namespace App\Models;


use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Illuminate\Foundation\Auth\afl_admins as Authenticatable;
use Illuminate\Notifications\Notifiable;




class AflAdmins extends Model
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
<<<<<<< HEAD
        'admin_hash',
        'admin_type_id'];
        
protected $primaryKey = 'admin_id';
public $timestamps = false;
=======
        'admin_hash'];
        
protected $primaryKey = 'admin_id';
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)

public function AauthAcessToken(){
    return $this->hasMany('App\Models\oauth_access_token');
}

}
