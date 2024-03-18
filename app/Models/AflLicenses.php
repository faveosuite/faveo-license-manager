<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    public function clients(){
        return $this->belongsTo(AflClients::class, 'client_id', 'client_id');
    }
    public function product()
    {
        return $this->belongsTo(AflProducts::class, 'product_id', 'product_id');
    }
    public function getInstallationAttribute()
    {
    return AflInstallations::where('product_id',$this->product_id)
        ->when($this->license_code,function ($query){
            $query->where('license_code', $this->license_code);
        })
        ->when($this->client_id,function ($query){
            $query->Where('client_id', $this->client_id);
        })
        ->get();
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

    public function getInstallationCountAttribute()
    {
        return $this->getInstallationAttribute()->count();
    }
    public function getCallBacksAttribute()
    {
        return AflCallbacks::where('product_id',$this->product_id)
            ->when($this->license_code,function ($query){
                $query->where('license_code', $this->license_code);
            })
            ->when($this->client_id,function ($query){
                $query->Where('client_id', $this->client_id);
            })
            ->get();
    }

    public function getLatestCallBacksAttribute()
    {
        return AflCallbacks::where('product_id',$this->product_id)
            ->when($this->license_code,function ($query){
                $query->where('license_code', $this->license_code);
            })
            ->when($this->client_id,function ($query){
                $query->Where('client_id', $this->client_id);
            })
            ->latest('callback_date_time')->value('callback_date_time');
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

    public function scopeWithClientEmailOrLicenseCode($query)
    {
        $query->addSelect([
            DB::raw("
                CASE
                    WHEN client_id IS NOT NULL THEN (SELECT client_email FROM users WHERE client_id = afl_licenses.client_id LIMIT 1)
                    ELSE license_code
                END As client_email_or_license_code
            ")
        ]);
    }
}
