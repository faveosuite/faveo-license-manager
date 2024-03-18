<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timezones';
    protected $fillable = ['name', 'location'];

    protected $appends = ['timezone_name'];

    // added a new  accessor timezone_name
    public function getTimezoneNameAttribute()
    {
        $extractGMT = explode(' ', $this->location);
        $timezone = reset($extractGMT).' '.$this->name;

        return $timezone;
    }
}
