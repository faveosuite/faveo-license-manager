<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflCallbacks extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'callback_id';

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(AflProducts::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(AflClients::class, 'client_id', 'client_id');
    }
}
