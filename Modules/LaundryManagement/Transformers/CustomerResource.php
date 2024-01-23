<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'image' => $this->image,
            'is_phone_verified' => $this->is_phone_verified,
            'email_verified_at' => $this->email_verified_at,
            'status' => $this->status,
            'order_count' => $this->order_count,
            'created_at' => $this->created_at,
        ];
    }
}
