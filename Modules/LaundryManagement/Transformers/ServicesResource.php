<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicesResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'laundry_items' => LaundryItemResource::make($this->whenLoaded('laundry_items')),
            'laundry_banners' => LaundryBannerResource::make($this->whenLoaded('laundry_banners')),
            'module' => $this->whenLoaded('module'),
        ];
    }
}
