<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Scopes\ZoneScope;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryVehicleType;
use Modules\LaundryManagement\Entities\LaundryZone;

class DeliveryMan extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'zone_id' => 'integer',
        'status'=>'boolean',
        'active'=>'integer',
        'available'=>'integer',
        'earning'=>'float',
        'restaurant_id'=>'integer',
        'current_orders'=>'integer',
        'order_count' => 'integer',
        'assigned_order_count' => 'integer',
        'is_laundry_dm' => 'boolean',
        'vehicle_type_id' => 'integer',
        'delivered_orders_count' => 'integer',
        'collected_orders_count' => 'integer',
        'before_delivered_orders_count' => 'integer',
        'before_picked_orders_count' => 'integer'
    ];

    protected $hidden = [
        'password',
        'auth_token',
    ];

    public function wallet()
    {
        return $this->hasOne(DeliveryManWallet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function laundry_delivered_orders()
    {
        return $this->hasMany(LaundryOrder::class, 'deliveryman_id', 'id');
    }
    public function laundry_picked_orders()
    {
        return $this->hasMany(LaundryOrder::class, 'pickup_deliveryman_id', 'id');
    }

    public function dm_time_logs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function order_transaction()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function todays_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereDate('created_at',now());
    }

    public function this_week_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function this_month_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
    }

    public function todaysorders()
    {
        return $this->hasMany(Order::class)->whereDate('accepted',now());
    }

    public function this_week_orders()
    {
        return $this->hasMany(Order::class)->whereBetween('accepted', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function delivery_history()
    {
        return $this->hasMany(DeliveryHistory::class, 'delivery_man_id');
    }

    public function last_location()
    {
        return $this->hasOne(DeliveryHistory::class, 'delivery_man_id')->latest();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function laundry_zone()
    {
        return $this->belongsTo(LaundryZone::class, 'zone_id');
    }

    public function laundry_vehicle_type()
    {
        return $this->belongsTo(LaundryVehicleType::class, 'vehicle_type_id', 'id');
    }


    public function reviews()
    {
        return $this->hasMany(DMReview::class);
    }

    public function rating()
    {
        return $this->hasMany(DMReview::class)
            ->select(DB::raw('avg(rating) average, count(delivery_man_id) rating_count, delivery_man_id'))
            ->groupBy('delivery_man_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1)->where('application_status','approved');
    }

    public function scopeEarning($query)
    {
        return $query->where('earning', 1);
    }

    public function scopeAvailable($query)
    {
        return $query->where('current_orders', '<' ,config('dm_maximum_orders'));
    }

    public function scopeZonewise($query)
    {
        return $query->where('type','zone_wise');
    }

    public function scopeLaundryDm($query)
    {
        return $query->withOutGlobalScopes()->where('is_laundry_dm', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }

    public function documents()
    {
        return $this->hasMany(Documents::class, 'delivery_man_id');
    }

    public function suspension_logs()
    {
        return $this->hasMany(SuspensionLog::class, 'delivery_man_id');
    }

    public function suspension_log()
    {
        return $this->hasOne(SuspensionLog::class, 'delivery_man_id')->where(function($query){
            $query->where('suspension_start','<=',now()->format('Y-m-d H:i:s'))->where('suspension_end','>=',now()->format('Y-m-d H:i:s'));
        });
    }

    public function getStatusAttribute($value){
        return $this->suspension_log?false:$value;
    }

    public function time_logs()
    {
        return $this->hasMany(DMTimeLog::class, 'delivery_man_id');
    }

    public function account_transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'from_id')->whereFromType('deliveryman');
    }
}
