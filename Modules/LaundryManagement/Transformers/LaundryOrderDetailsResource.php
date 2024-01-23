<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LaundryOrderDetailsResource extends JsonResource
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
            'laundry_order' => LaundryOrderResource::make($this->whenLoaded('laundry_order')),
            'service' => ServicesResource::make($this->whenLoaded('service')),
            'laundry_item' => LaundryItemResource::make($this->whenLoaded('laundry_item')),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'bar_code' => $this->bar_code,
            'processing' => $this->processing,
            'processed' => $this->processed,
            'created_at' => $this->created_at,
        ];
    }
}
