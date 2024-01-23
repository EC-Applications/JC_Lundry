<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\Helpers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryItem;
use Modules\LaundryManagement\Transformers\LaundryItemResource;

class LaundryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function get_item_list(Request $request, $service_id)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        
        $items = LaundryItem::active()
        ->with([
            'services' => function ($query) use ($service_id) {
                $query->where('services_id', $service_id);
            }
        ])
        ->whereHas('services', function ($query) use ($service_id) {
            $query->where('services_id', $service_id);
        })
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $resources = LaundryItemResource::collection($items);

        return response()->json(Helpers::response_formatter($resources,$request['limit'],$request['offset']), 200);
    }
}
