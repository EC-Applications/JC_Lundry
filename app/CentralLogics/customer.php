<?php

namespace App\CentralLogics;

use Illuminate\Support\Facades\Storage;
use App\Models\User;

class CustomerLogic
{
    private User $user;

    private function __construct(User $user) {
        $this->user = $user;
    }

    public function update_zone($zone_id)
    {
        $this->user->zone_id = $zone_id;
        $this->user->save();
    }

    public function update_order_count()
    {
        $this->user->increment('order_count');
    }


}
