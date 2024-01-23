<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LaundryOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => CustomerResource::make($this->whenLoaded('customer')),
            'deliveryman_id' => NULL,
            'order_status' => $this->order_status,
            'order_amount' => $this->order_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
            'pickup_coordinates' => $this->pickup_coordinates,
            'pickup_address' => $this->pickup_address,
            'destination_coordinates' => $this->destination_coordinates,
            'destination_address' => $this->destination_address,
            'laundry_delivery_type' => LaundryDeliveryTypeResource::make($this->whenLoaded('delivery_type')),
            'delivery_time' => $this->delivery_time,
            'delivery_charge' => $this->delivery_charge,
            'bar_code' => $this->bar_code,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'distance' => $this->distance,
            'note' => $this->note,
            'pickup_schedule_at' => $this->pickup_schedule_at,
            'delivery_schedule_at' => $this->delivery_schedule_at,
            'pending' => $this->pending,
            'confirmed' => $this->confirmed,
            'out_for_pickup' => $this->out_for_pickup,
            'picked_up' => $this->picked_up,
            'arrived' => $this->arrived,
            'processing' => $this->processing,
            'ready_for_delivery' => $this->ready_for_delivery,
            'out_for_delivery' => $this->out_for_delivery,
            'delivered' => $this->delivered,
            'cancelled' => $this->cancelled,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at,
            'details' => LaundryOrderDetailsResource::collection($this->whenLoaded('details')),
            'details_count' => $this->whenLoaded('details', function () {
                return $this->details->count();
            }),
        ];
    }
}
