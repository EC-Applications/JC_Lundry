<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Food;

class FoodObserver
{
    /**
     * Handle the Food "created" event.
     *
     * @param  \App\Models\Food  $food
     * @return void
     */
    public function created(Food $food)
    {
        //
    }

    /**
     * Handle the Food "updated" event.
     *
     * @param  \App\Models\Food  $food
     * @return void
     */
    public function updated(Food $food)
    {
        if ($food->isDirty() && (auth('vendor_employee')->check() || auth('vendor')->check() || request('vendor') || auth('admin')->check())) {
            if (auth('vendor_employee')->check()) {
                $loable_type = 'App\Models\VendorEmployee';
                $logable_id = auth('vendor_employee')->id();
            } elseif (auth('vendor')->check() || request('vendor')) {
                $loable_type = 'App\Models\Vendor';
                $logable_id = auth('vendor')->id();
            } elseif (auth('admin')->check()) {
                $loable_type = 'App\Models\Admin';
                $logable_id = auth('admin')->id();
            }
            $log = new Log();
            $log->logable_type = $loable_type;
            $log->logable_id = $logable_id;
            $log->action = 'updated';
            $log->model = 'Food';
            $log->model_id = $food->id;
            $log->current_state = $food->getChanges();
            $log->save();
        }
    }

    /**
     * Handle the Food "deleted" event.
     *
     * @param  \App\Models\Food  $food
     * @return void
     */
    public function deleted(Food $food)
    {
        if(auth('vendor_employee')->check() || auth('vendor')->check() || request('vendor') || auth('admin')->check()){
            if (auth('vendor_employee')->check()) {
                $loable_type = 'App\Models\VendorEmployee';
                $logable_id = auth('vendor_employee')->id();
            } elseif (auth('vendor')->check() || request('vendor')) {
                $loable_type = 'App\Models\Vendor';
                $logable_id = auth('vendor')->id();
            } elseif (auth('admin')->check()) {
                $loable_type = 'App\Models\Admin';
                $logable_id = auth('admin')->id();
            }
            $log = new Log();
            $log->logable_type = $loable_type;
            $log->logable_id = $logable_id;
            $log->action = 'deleted';
            $log->model = 'Food';
            $log->model_id = $food->id;
            $log->current_state = $food->getChanges();
            $log->save();            
        }

    }

    /**
     * Handle the Food "restored" event.
     *
     * @param  \App\Models\Food  $food
     * @return void
     */
    public function restored(Food $food)
    {
        //
    }

    /**
     * Handle the Food "force deleted" event.
     *
     * @param  \App\Models\Food  $food
     * @return void
     */
    public function forceDeleted(Food $food)
    {
        //
    }
}
