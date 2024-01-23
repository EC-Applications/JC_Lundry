<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\Helpers;
use App\Models\Banner;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryBanner;
use Modules\LaundryManagement\Transformers\LaundryBannerResource;

class LaundryBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function banner_list(Request $request)
    {
        $query = LaundryBanner::with([
            'service' => function ($query) {
                return $query->active();
        }])->whereHas('service', function ($query) {
            return $query->active();
        })
        ->active()
        ->latest()
        ->get();
        $banner_list = LaundryBannerResource::collection($query);

        return response()->json($banner_list, 200);
    }

}
