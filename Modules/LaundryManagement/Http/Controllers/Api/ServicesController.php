<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\Helpers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryItem;
use Modules\LaundryManagement\Entities\Services;
use Modules\LaundryManagement\Transformers\ServicesResource;
use App\Utils\PaginateCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function service_list(Request $request)
    {
        $keys = explode(' ', $request['search']);
        $services = Services::query()->active()
        ->when($request->search, fn ($query) =>
            $query->where(fn ($q) =>
                array_map(fn ($key) => $q->where('name', 'LIKE', '%' . $key . '%'), $keys)
            )
        )
        ->latest()
        ->get();

        $resources = ServicesResource::collection($services);

        return response()->json($resources, 200);
    }

 }
