<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\DeliveryMan;
use App\Models\Documents;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryZone;

class LaundryDMController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $zone_id = $request->query('zone_id', 'all');

        $delivery_men = DeliveryMan::laundryDm()->when(is_numeric($zone_id), function ($query) use ($zone_id) {
            return $query->where('zone_id', $zone_id);
        })->with(['zone'])->latest()->paginate(config('default_pagination'));
        $zone = is_numeric($zone_id) ? LaundryZone::findOrFail($zone_id) : null;

        return view('laundrymanagement::admin-views.delivery-man.list', compact('delivery_men', 'zone'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('laundrymanagement::admin-views.delivery-man.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'email' => 'required|unique:delivery_men',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:delivery_men',
            'zone_id' => 'required',
            'earning' => 'required',
            'password' => 'required|min:6',
            'father_name' => 'required',
        ], [
            'f_name.required' => trans('messages.first_name_is_required'),
            'zone_id.required' => trans('messages.select_a_zone'),
            'earning.required' => trans('messages.select_dm_type')
        ]);

        if ($request->has('image')) {
            $image_name = Helpers::upload('delivery-man/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        // $id_img_names = [];
        // if (!empty($request->file('identity_image'))) {
        //     foreach ($request->identity_image as $img) {
        //         $identity_image = Helpers::upload('delivery-man/', 'png', $img);
        //         array_push($id_img_names, $identity_image);
        //     }
        //     $identity_image = json_encode($id_img_names);
        // } else {
        //     $identity_image = json_encode([]);
        // }
        $delivery_man_count = DeliveryMan::all()->count();
        $last_id = sprintf("%06d", $delivery_man_count > 0 ? $delivery_man_count + 1 : 1);
        $zone_id= $request->zone_id;
        $swr_id = "{$zone_id}{$last_id}";
        $dm = new DeliveryMan();
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        // $dm->identity_number = $request->identity_number;
        // $dm->identity_type = $request->identity_type;
        $dm->zone_id = $request->zone_id;
        // $dm->identity_image = $identity_image;
        $dm->image = $image_name;
        $dm->active = 1;
        $dm->earning = $request->earning;
        $dm->father_name = $request->father_name;
        $dm->password = bcrypt($request->password);
        $dm->code = $swr_id;
        $dm->vehicle_type_id = $request->vehicle_type_id;
        $dm->is_laundry_dm = 1;
        if(DeliveryMan::where('code',$dm->code)->first()) {
            $dm->code = DeliveryMan::orderBy('code', 'desc')->first()->code + ($delivery_man_count + 1);
        }
        $dm->save();
        // try {
        //     DB::beginTransaction();
            
        //     if ($request->document) {
        //         $documents = [];
        //         foreach ($request->document as $doc) {
        //             $files = [];
        //             if(Documents::where('type',$doc['type'])->where('data',$doc['data'])->first()){
        //                 DB::rollBack();
        //                 Toastr::error(trans("messages.doc_already_exits",['type'=>$doc['type']]));
        //                 return back();
        //             }
        //             if (!empty($doc['doc_file'])) {
        //                 foreach ($doc['doc_file'] as $key => $img) {
        //                     array_push($files, Helpers::upload('delivery-man/', $doc['file_type'] == 'image' ? 'png' : $doc['file_type'], $img, "dm_{$dm->id}_{$doc['type']}_{$doc['data']}_{$key}"));
        //                 }
        //             }
        //             $documents[] = [
        //                 'delivery_man_id' => $dm->id,
        //                 'type' => $doc['type'],
        //                 'data' => $doc['data'],
        //                 'document_type' => $doc['file_type'],
        //                 'files' => json_encode($files)
        //             ];
        //         }
        //         Documents::insert($documents);
        //     }
        //     DB::commit();
        // } catch (Exception $ex) {
        //     DB::rollBack();
        //     Toastr::error($ex->getMessage());
        //     return back();
        // }

        Toastr::success(trans('messages.deliveryman_added_successfully'));
        return redirect()->route('admin.laundry.delivery-man.list');
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $delivery_men = DeliveryMan::laundryDm()->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%")
                    ->orWhere('code', 'like', "%{$value}%");
            }
        })->where('type', 'zone_wise')->get();
        return response()->json([
            'view' => view('admin-views.delivery-man.partials._table', compact('delivery_men'))->render(),
            'count' => $delivery_men->count()
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $delivery_man = DeliveryMan::with("documents")->laundryDm()->find($id);
        return view('laundrymanagement::admin-views.delivery-man.edit', compact('delivery_man'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'email' => 'required|unique:delivery_men,email,' . $id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:delivery_men,phone,' . $id,
            'earning' => 'required',
            'father_name' => 'required',
        ], [
            'f_name.required' => trans('messages.first_name_is_required'),
            'earning.required' => trans('messages.select_dm_type')
        ]);

        $delivery_man = DeliveryMan::with("documents")->laundryDm()->find($id);

        if ($request->has('image')) {
            $image_name = Helpers::update('delivery-man/', $delivery_man->image, 'png', $request->file('image'));
        } else {
            $image_name = $delivery_man['image'];
        }

        $delivery_man->f_name = $request->f_name;
        $delivery_man->l_name = $request->l_name;
        $delivery_man->email = $request->email;
        $delivery_man->phone = $request->phone;
        $delivery_man->zone_id = $request->zone_id;
        $delivery_man->image = $image_name;
        $delivery_man->earning = $request->earning;
        $delivery_man->father_name = $request->father_name;
        $delivery_man->vehicle_type_id = $request->vehicle_type_id;
        $delivery_man->password = strlen($request->password) > 1 ? bcrypt($request->password) : $delivery_man['password'];
        try {
            DB::beginTransaction();
            $delivery_man->save();
            if ($request->document) {
                foreach ($request->document as $k => $doc) {
                    $files = [];
                    if(Documents::where('delivery_man_id','!=',$delivery_man->id)->where('type',$doc['type'])->where('data',$doc['data'])->first()){
                        DB::rollBack();
                        Toastr::error(trans("messages.doc_already_exits",['type'=>$doc['type']]));
                        return back();
                    }
                    if (!$data = $delivery_man->documents->where('type', $doc['type'])->first()) {
                        $data = new Documents();
                        $data->delivery_man_id = $delivery_man->id;
                        $data->created_at = now();
                    } else {
                        $files = $data->files;
                    }

                    if ($request->hasFile("document.{$k}.doc_file")) {
                        foreach ($files as $previous_file) {
                            if (Storage::disk('public')->exists('delivery-man/' . $previous_file)) {
                                Storage::disk('public')->delete('delivery-man/' . $previous_file);
                            }
                        }
                        $files = [];
                        foreach ($doc['doc_file'] as $key => $img) {
                            $files[] = Helpers::upload('delivery-man/', $doc['file_type'] == 'image' ? 'png' : $doc['file_type'], $img, "dm_{$delivery_man->id}_{$doc['type']}_{$doc['data']}_{$key}");
                        }
                    }
                    $data->data = $doc['data'];
                    $data->files = $files;
                    $data->type = $doc['type'];
                    $data->document_type = $doc['file_type'];
                    $data->updated_at = now();
                    $data->save();
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Toastr::error($ex->getMessage());
            return back();
        }
        Toastr::success(trans('messages.deliveryman_updated_successfully'));
        return redirect()->route('admin.laundry.delivery-man.list');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $delivery_man = DeliveryMan::laundryDm()->findOrFail($id);

        if (Storage::disk('public')->exists('delivery-man/' . $delivery_man['image'])) {
            Storage::disk('public')->delete('delivery-man/' . $delivery_man['image']);
        }
        if($identity_images = json_decode($delivery_man['identity_image'], true)){
            foreach ($identity_images as $img) {
                if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }
        }
        $delivery_man->documents()->each(function ($document) {
            $document->delete();
        });
        $delivery_man->delete();

        Toastr::success(trans('messages.deliveryman_deleted_successfully'));
        return back();
    }

    /**
     * Summary of add_delivery_man
     * @param mixed $order_id
     * @param mixed $delivery_man_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_delivery_man(Request $request)
    {
        if ($request->delivery_man_id == 0) {
            return response()->json([
                    'errors'=>[
                        ['delivery_man_id'=> trans('messages.deliveryman').' '.trans('messages.not_found')]
                    ]
                ], 404);
        }

        $deliveryman = DeliveryMan::laundryDm()->where('id', $request->delivery_man_id)->active()->first();

        if ($deliveryman) {
            foreach ($request->order_ids as $order_id) {
                $order = LaundryOrder::find($order_id);
                if (($order->deliveryman_id == $request->delivery_man_id && in_array($order->order_status, ['ready_for_delivery', 'out_for_delivery'])) || ($order->pickup_deliveryman_id == $request->delivery_man_id && in_array($order->order_status, ['pending', 'confirmed','out_for_pickup']))) {
                    return response()->json(['message' => trans('messages.order_already_assign_to_this_deliveryman'), ], 400);
                }
                if ($order->delivery_man) {
                    $dm = $order->delivery_man;
                    $dm->current_orders = $dm->current_orders > 1 ? $dm->current_orders - 1 : 0;
                    // $dm->decrement('assigned_order_count');
                    $dm->save();
    
                    // $data = [
                    //     'title' => trans('messages.order_push_title'),
                    //     'description' => trans('messages.you_are_unassigned_from_a_order'),
                    //     'order_id' => '',
                    //     'image' => '',
                    //     'type' => 'assign'
                    // ];
                    // Helpers::send_push_notif_to_device($dm->fcm_token, $data);
    
                    // DB::table('user_notifications')->insert([
                    //     'data' => json_encode($data),
                    //     'delivery_man_id' => $dm->id,
                    //     'created_at' => now(),
                    //     'updated_at' => now()
                    // ]);
                }elseif($order->pickup_delivery_man && is_null($order->deliveryman_id)){
                    $dm = $order->pickup_delivery_man;
                    $dm->current_orders = $dm->current_orders > 1 ? $dm->current_orders - 1 : 0;
                    // $dm->decrement('assigned_order_count');
                    $dm->save();
                }
                if (in_array($order->order_status, ['pending', 'confirmed','out_for_pickup'])) {
                    $order->pickup_deliveryman_id = $request->delivery_man_id;
                }elseif(in_array($order->order_status, ['ready_for_delivery', 'out_for_delivery'])){
                    $order->deliveryman_id = $request->delivery_man_id;
                }
                $order->order_status = $order->order_status == 'pending'?'confirmed':($order->order_status == 'ready_for_delivery'?'ready_for_delivery':$order->order_status);
                $order[$order->order_status] = now();
                $order->save();
    
                $deliveryman->current_orders = $deliveryman->current_orders + 1;
                $deliveryman->save();
                $deliveryman->increment('assigned_order_count');                            
            }
            return response()->json([], 200);
        }
            
        return response()->json(['message'=> 'Deliveryman not available!'], 400);
    }

    /**
     * Summary of preview
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @param mixed $tab
     * @param mixed $sub_tab
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function preview(Request $request, $id, $tab = 'profile')
    {
        $dm = DeliveryMan::with(['reviews', 'suspension_logs', 'suspension_log', 'documents'])->withCount([
            'laundry_delivered_orders as delivered_orders_count' => function ($query) {
                $query->whereOrderStatus('delivered');
            }, 'laundry_picked_orders as for_picking_up_orders' => function ($query) {
                $query->whereIn('order_status', ['confirmed','out_for_pickup']);
            }, 'laundry_delivered_orders as for_delivered_orders' => function ($query) {
                $query->whereIn('order_status', ['ready_for_delivery','out_for_delivery']);
            }, 'laundry_delivered_orders as canceled_or_failed_orders' => function ($query) {
                $query->whereIn('order_status', ['cancelled']);
            }
        ])->where(['id' => $id])->first();
        // dd($dm);
        if ($tab == 'profile') {
            return view('laundrymanagement::admin-views.delivery-man.view.profile', compact('dm'));
        } else if ($tab == 'order') {
            $status = $request->query('status');
            $from = $request->query('from');
            $to = $request->query('to');
            $on_going = ['confirmed','out_for_pickup','ready_for_delivery','out_for_delivery'];
            $delivery = ['ready_for_delivery','out_for_delivery'];
            $pickup = ['confirmed', 'out_for_pickup'];
            $search=$request->query('search');
            $key = explode(' ', $search);

            $orders = LaundryOrder::when($status == 'on_going', function ($query) use ($on_going) {
                return $query->whereIn('order_status', $on_going);
            })
            ->when($status == 'pickup', function ($query) use($pickup){
                return $query->whereIn('order_status', $pickup);
            })
            ->when($status == 'delivery', function ($query) use ($delivery) {
                return $query->whereIn('order_status', $delivery);
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null, function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($search), function($query) use($key){
                foreach ($key as $value) {
                    $query->where('id', 'like', "%{$value}%");
                }
            })->where(function($query) use($dm){
                $query->where('deliveryman_id',$dm->id)->orWhere('pickup_deliveryman_id',$dm->id);
            })->latest()->paginate(config('default_pagination'));
            // dd($orders);
            // return $order_logs;
            return view('laundrymanagement::admin-views.delivery-man.view.order', compact('dm', 'orders', 'status'));
        }
        
        return view('errors.404');
    }
}
