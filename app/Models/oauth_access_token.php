<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\afl_admins;

class oauth_access_token extends Model
{
    use HasFactory;

    public function afl_admin(){
        return belongsTo('afl_admins');
    }
}
