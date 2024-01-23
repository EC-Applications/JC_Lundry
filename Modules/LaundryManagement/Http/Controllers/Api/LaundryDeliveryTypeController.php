<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\Helpers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryDeliveryType;
use Modules\LaundryManagement\Transformers\LaundryDeliveryTypeResource;

class LaundryDeliveryTypeController extends Controller
{
    /**
     * Summary of type_list
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function type_list(Request $request)
    {
        $types = LaundryDeliveryType::active()->latest()->get();
        $resources = LaundryDeliveryTypeResource::collection($types);

        return response()->json($resources, 200);
    }

}
