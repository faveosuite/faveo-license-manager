<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    use HasFactory;

    protected $table = 'country_codes';

    protected $fillable = ['id', 'name', 'nickname','iso2', 'iso3', 'number_code', 'phone_code', 'updated_at', 'created_at'];
}
