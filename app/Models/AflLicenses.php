<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflLicenses extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'license_id';

    public $timestamps = false;

    public $appends = ['order_url'];

    public function client()
    {
        return $this->belongsToMany(AflClients::class);
    }

    public function product()
    {
        return $this->hasMany(AflProducts::class);
    }
    public function products()
    {
        return $this->belongsTo(AflProducts::class, 'product_id', 'product_id');
    }

    public function installations()
    {
        return $this->hasMany(AflInstallations::class, 'license_code', 'license_code');
    }

    public function callbacks()
    {
        return $this->hasMany(AflCallbacks::class, 'license_code', 'license_code')->select('callback_date_time')->latest('callback_date_time');
    }
    public function getOrderUrlAttribute()
    {
        $agoraInvoicingUrl = CommonSetting::where('key', 'agora_invoicing_url')->value('value');
        if (!filter_var($agoraInvoicingUrl, FILTER_VALIDATE_URL)) {
            return $this->license_order_number;
        }
        $orderUrl = rtrim($agoraInvoicingUrl, '/') . "/orders/license/" . $this->license_order_number;
        if ($agoraInvoicingUrl && $this->license_order_number) {
            return "<a id=\"href_link\" href=\"{$orderUrl}\">{$this->license_order_number}</a>";
        }
        return $this->license_order_number;
    }
}
