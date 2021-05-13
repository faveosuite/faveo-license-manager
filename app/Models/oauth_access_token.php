<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AflAdmins;

class OauthAccessToken extends Model
{
    use HasFactory;

    public function afl_admin(){
        return belongsTo('afl_admins');
    }
}
