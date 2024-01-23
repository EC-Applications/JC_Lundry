<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LaundryItemResource extends JsonResource
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
            'icon' => $this->icon,
            'name' => $this->name,
            'price' => (float)$this->whenLoaded('services')[0]->pivot->price,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
