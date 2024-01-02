<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleCron extends Model
{
    use HasFactory;
    protected $table = "schedule_crons";
    protected $fillable = [
        'scenario',  
        'value',     
        'status',    
    ];

    public function getAllActiveCron()
    {
        return $this->where('status', 1)->get(['value', 'command'])->map(function ($tasks) {
            return [$tasks['value'] => $tasks['command']];
        })->toArray();
    }

    public function getConditionValue($value)
    {
        $condition_value = explode(',', $value);
        $result = ['condition' => $condition_value, 'time' => '', 'day' => ''];
        if (is_array($condition_value)) {
            $result = ['condition' => $this->checkArray(0, $condition_value), 'time' => $this->checkArray(1, $condition_value), 'day' => $this->checkArray(2, $condition_value)];
        }

        return $result;
    }

    public function checkArray($key, $array)
    {
    return is_array($array) && array_key_exists($key, $array) ? $array[$key] : '';
    }
}
