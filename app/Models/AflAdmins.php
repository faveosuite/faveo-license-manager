<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

//use Illuminate\Foundation\Auth\afl_admins as Authenticatable;

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
            'admin_hash',
            'admin_type_id', ];

    protected $primaryKey = 'admin_id';

    public $timestamps = false;

    public function AauthAcessToken()
    {
        return $this->hasMany('App\Models\oauth_access_token');
    }
}
