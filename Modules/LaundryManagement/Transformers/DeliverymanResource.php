<?php

namespace Modules\LaundryManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliverymanResource extends JsonResource
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
            "id" => $this->id, 
            "f_name" => $this->f_name, 
            "l_name" => $this->l_name, 
            "phone" => $this->phone, 
            "email" => $this->email, 
            "identity_number" => $this->identity_number, 
            "identity_type" => $this->identity_type, 
            "identity_image" => $this->iidentity_image, 
            "image" => $this->image, 
            "fcm_token" => $this->fcm_token, 
            "created_at" => $this->created_at, 
            "updated_at" => $this->updated_at, 
            "status" => $this->status, 
            "active" =>  $this->active, 
            "earning" =>  $this->earning, 
            "current_orders" => $this->current_orders,
            "type" => $this->type, 
            "application_status" => $this->application_status, 
            "order_count" => $this->order_count, 
            "assigned_order_count" => $this->assigned_order_count, 
            "father_name" => $this->father_name,  
            "documents" => $this->documents,
            'zone' => $this->whenLoaded('zone')
        ];
    }
}
