<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LaundryDeliveryTypeResource extends JsonResource
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
            'title' => $this->title,
            'duration' => $this->duration,
            'charge' => $this->charge,
            'status' => $this->status,
        ];
    }
}
