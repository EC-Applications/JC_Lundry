<?php

namespace Modules\LaundryManagement\Entities;

use App\CentralLogics\Helpers;
use App\Models\DeliveryMan;
use App\Models\User;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    use HasFactory,SpatialTrait;
    protected $casts = [
        'pickup_address' => 'array',
        'destination_address' => 'array',
        'bar_code' => 'integer',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'order_amount' => 'float'
    ];

    protected $fillable = [];

    protected $spatialFields = [
        'pickup_coordinates',
        'destination_coordinates',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function delivery_type()
    {
        return $this->belongsTo(LaundryDeliveryType::class, 'laundry_delivery_type_id');
    }

    public function details()
    {
        return $this->hasMany(LaundryOrderDetails::class, 'laundry_orders_id', 'id');
    }

    public function pickup_delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class, 'pickup_deliveryman_id')->withoutGlobalScopes();
    }

    public function delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class, 'deliveryman_id')->withoutGlobalScopes();
    }

    public function scopePending($query)
    {
        return $query->where('order_status','pending');
    }

    public function scopeConfirmedOrder($query)
    {
        return $query->where('order_status', 'confirmed');
    }

    public function scopeOutForPickup($query)
    {
        return $query->where('order_status', 'out_for_pickup');
    }

    public function scopePickedUpByDeliveryman($query)
    {
        return $query->where('order_status', 'picked_up');
    }

    public function scopeArrivedAtWarehouse($query)
    {
        return $query->where('order_status', 'arrived');
    }

    public function scopeProcessing($query)
    {
        return $query->where('order_status', 'processing');
    }

    public function scopeReadyForDelivery($query)
    {
        return $query->where('order_status', 'ready_for_delivery');
    }
    public function scopeOutForDelivery($query)
    {
        return $query->where('order_status', 'out_for_delivery');
    }

    public function scopeCanceled($query)
    {
        return $query->where('order_status','cancelled');
    }
    
    public function scopeDelivered($query)
    {
        return $query->where('order_status','delivered');
    }

    public function scopeSearchingForDeliveryman($query)
    {
        return $query->where(function ($query) {
            return $query->whereNull('deliveryman_id')->orWhereNull('pickup_deliveryman_id');
        })->whereIn('order_status',['pending','ready_for_delivery']);;
    }

    public function scopeOngoing($query)
    {
        return $query->whereIn('order_status', ['confirmed','out_for_pickup','picked_up','arrived','processing','ready_for_delivery','out_for_delivery']);
    }
    
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {


        });

        self::created(function ($model) {
            if ($model->isDirty('order_status')) {
                info($model->order_status);
                $value = null;
                if ($model->order_status == 'pending') {
                    $key = 'New Laundry order placed notification';
                    $value = 'Your Laundry order has been placed succesfully';
                } elseif ($model->order_status == 'confirmed') {
                    info($model->order_status);

                    $key = 'Laundry Order confirmed notification';
                    $value = 'Your Laundry order is confirmed';
                } elseif ($model->order_status == 'out_for_pickup') {
                    info($model->order_status);

                    $key = 'Picking up Laundry order notification';
                    $value = 'A deliveryman on the way to pickup, Get ready!';
                } elseif ($model->order_status == 'picked_up') {
                    info($model->order_status);
                    $key = 'Laundry Order picked  up notification';
                    $value = 'Deliveryman picked up your order';
                } elseif (in_array($model->order_status,['arrived','processing','ready_for_delivery'])) {
                    info($model->order_status);
                    $key = 'Laundry Order processing notification';
                    $value = 'Laundry Order is processing';
                } elseif ($model->order_status == 'out_for_delivery') {
                    info($model->order_status);
                    $key = 'Laundry Order delivery notification';
                    $value = 'A deliveryman on the way to deliver your Laundry order';
                } elseif ($model->order_status == 'delivered') {
                    info($model->order_status);
                    $key = 
                    $value = 'Your Laundry order is delivered successfully';
                } elseif ($model->order_status == 'canceled') {
                    info($model->order_status);
                    $key = 'Laundry Order cancelled notification';
                    $value = 'Your Laundry order is cancelled';
                }

                $fcm_token = $model?->customer?->cm_firebase_token;
                if ($fcm_token) {
                    $data = [
                        'title' => $key,
                        'description' => $value,
                        'order_id' => $model->id,
                        'image' => '',
                        'type'=>'laundry',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }
        });

        self::updating(function ($model) {
            if ($model->isDirty('order_status')) {
                $value = null;
                if ($model->order_status == 'pending') {
                    $key = 'New Laundry order placed notification';
                    $value = 'Your Laundry order has been placed succesfully';
                } elseif ($model->order_status == 'confirmed') {
                    $key = 'Laundry Order confirmed notification';
                    $value = 'Your Laundry order is confirmed';
                } elseif ($model->order_status == 'out_for_pickup') {
                    $key = 'Picking up Laundry order notification';
                    $value = 'Deliveryman on the way to pickup, Get ready!';
                } elseif ($model->order_status == 'picked_up') {
                    $key = 'Laundry Order picked  up notification';
                    $value = 'Deliveryman picked up your order';
                } elseif (in_array($model->order_status,['processing'])) {
                    $key = 'Laundry Order processing notification';
                    $value = 'Laundry Order is processing';
                } elseif ($model->order_status == 'out_for_delivery') {
                    $key = 'Laundry Order delivery notification';
                    $value = 'A deliveryman on the way to deliver your Laundry order';
                } elseif ($model->order_status == 'delivered') {
                    $key = "Your Laundry Order has been delivered";
                    $value = 'Your Laundry order is delivered successfully';
                } elseif ($model->order_status == 'cancelled') {
                    $key = 'Laundry Order cancelled notification';
                    $value = 'Your Laundry order is cancelled';
                }

                $fcm_token = $model?->customer?->cm_firebase_token;
                if ($fcm_token && !is_null($value)) {
                    $data = [
                        'title' => $key,
                        'description' => $value,
                        'order_id' => $model->id,
                        'image' => '',
                        'type'=>'laundry',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }
            $data_array = [
                'title' => trans('messages.you_have_a_new_order_request'),
                'description' => '',
                'order_id' => $model->id,
                'image' => '',
                'type'=>'laundry',
            ];
            if ($model->isDirty('pickup_deliveryman_id')) {
                $dm = DeliveryMan::find($model->pickup_deliveryman_id);
                $fcm_token = $dm->fcm_token;
                if ($fcm_token) {
                    Helpers::send_push_notif_to_device($fcm_token, $data_array);
                }
            }
            if ($model->isDirty('deliveryman_id')) {
                $dm = DeliveryMan::find($model->deliveryman_id);
                $fcm_token = $dm->fcm_token;
                if ($fcm_token) {
                    Helpers::send_push_notif_to_device($fcm_token, $data_array);
                }
            }
        });

        self::updated(function ($model) {
            // ... code here
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            // ... code here
        });
    }
}
