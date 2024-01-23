<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use App\Models\DMReview;
use App\Models\Documents;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DMTimeLog;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\SuspensionLog;
use App\Models\TimeLog;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

class DeliveryManController extends Controller
{
    public function index()
    {
        return view('admin-views.delivery-man.index');
    }

    public function list(Request $request)
    {
        $zone_id = $request->query('zone_id', 'all');
        $delivery_men = DeliveryMan::when(is_numeric($zone_id), function ($query) use ($zone_id) {
            return $query->where('zone_id', $zone_id);
        })->with(['zone','suspension_log'])->where('type', 'zone_wise')->latest()->paginate(config('default_pagination'));
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;
        return view('admin-views.delivery-man.list', compact('delivery_men', 'zone'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $delivery_men = DeliveryMan::where(function ($q) use ($key) {
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

    public function reviews_list()
    {
        $reviews = DMReview::with(['delivery_man', 'customer'])->whereHas('delivery_man', function ($query) {
            $query->where('type', 'zone_wise');
        })->latest()->paginate(config('default_pagination'));
        return view('admin-views.delivery-man.reviews-list', compact('reviews'));
    }

    public function preview(Request $request, $id, $tab = 'info', $sub_tab = 'order-cash-in-hand')
    {
        $dm = DeliveryMan::with(['reviews', 'suspension_logs', 'suspension_log', 'documents'])->withCount([
            'orders', 'orders as delivered_orders_count' => function ($query) {
                $query->wherePaymentMethod('cash_on_delivery');
            }, 'orders as ongoing_orders' => function ($query) {
                $query->Ongoing();
            }, 'orders as delivered_orders' => function ($query) {
                $query->delivered();
            }, 'orders as canceled_or_failed_orders' => function ($query) {
                $query->whereIn('order_status', ['failed', 'canceled']);
            }
        ])->withSum('account_transactions', 'amount')->where('type', 'zone_wise')->where(['id' => $id])->first();

        if ($tab == 'info') {
            $reviews = DMReview::where(['delivery_man_id' => $id])->latest()->paginate(config('default_pagination'));
            return view('admin-views.delivery-man.view.info', compact('dm', 'reviews'));
        } else if ($tab == 'transaction') {
            $date = $request->query('date');
            $from = $request->query('from');
            $to = $request->query('to');
            $search =$request->query('search');
            return view('admin-views.delivery-man.view.transaction', compact('dm', 'date', 'from', 'to', 'sub_tab','search'));
        } else if ($tab == 'profile') {
            return view('admin-views.delivery-man.view.profile', compact('dm'));
        } else if ($tab == 'suspension') {
            $date = $request->query('date');
            return view('admin-views.delivery-man.view.suspension', compact('dm', 'date'));
        } else if ($tab == 'timelog') {
            $from = $request->query('from');
            $to = $request->query('to');
            $timelogs = DMTimeLog::when(isset($from) && isset($to) && $from != null && $to != null, function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })->where('delivery_man_id', $id)->latest()->paginate(config('default_pagination'));
            return view('admin-views.delivery-man.view.timelog', compact('dm', 'timelogs'));
        } else if ($tab == 'order') {
            $status = $request->query('status');
            $from = $request->query('from');
            $to = $request->query('to');
            $on_going = ['processing', 'out_for_delivery', 'picked_up', 'hand_over'];
            $search=$request->query('search');
            $key = explode(' ', $search);
            $order_logs = OrderLog::where('delivery_man_id', $dm->id)
            ->select(DB::raw('count(*) as order_count, is_accept'))
            ->groupBy('is_accept')->get();
            $orders = Order::when((isset($status) && $status != 'on_going'), function ($query) use ($status) {
                $query->where('order_status', $status);
            })->when($status == 'on_going', function ($query) use ($on_going) {
                $query->whereIn('order_status', $on_going);
            })->when(isset($from) && isset($to) && $from != null && $to != null, function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($search), function($query) use($key){
                foreach ($key as $value) {
                    $query->where('id', 'like', "%{$value}%");
                }
            })->whereDeliveryManId($dm->id)->latest()->paginate(config('default_pagination'));
            // return $order_logs;
            return view('admin-views.delivery-man.view.order', compact('dm', 'orders', 'status', 'order_logs'));
        } else if ($tab == 'online_percentage') {
            $from = $request->query('from');
            $to = $request->query('to');
            $timelogs = TimeLog::with('deliveryman')->when(isset($from) && isset($to) && $from != null && $to != null, function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })->where('user_id', $id)->latest()->paginate(config('default_pagination'));

            return view('admin-views.delivery-man.view.online-percentage', compact('dm', 'timelogs'));
        }
        return view('errors.404');
    }

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
        $dm->active = 0;
        $dm->earning = $request->earning;
        $dm->father_name = $request->father_name;
        $dm->password = bcrypt($request->password);
        $dm->code = $swr_id;
        if(DeliveryMan::where('code',$dm->code)->first()) {
            $dm->code = DeliveryMan::orderBy('code', 'desc')->first()->code + ($delivery_man_count + 1);
        }
        try {
            DB::beginTransaction();
            $dm->save();
            if ($request->document) {
                $documents = [];
                foreach ($request->document as $doc) {
                    $files = [];
                    if(Documents::where('type',$doc['type'])->where('data',$doc['data'])->first()){
                        DB::rollBack();
                        Toastr::error(trans("messages.doc_already_exits",['type'=>$doc['type']]));
                        return back();
                    }
                    if (!empty($doc['doc_file'])) {
                        foreach ($doc['doc_file'] as $key => $img) {
                            array_push($files, Helpers::upload('delivery-man/', $doc['file_type'] == 'image' ? 'png' : $doc['file_type'], $img, "dm_{$dm->id}_{$doc['type']}_{$doc['data']}_{$key}"));
                        }
                    }
                    $documents[] = [
                        'delivery_man_id' => $dm->id,
                        'type' => $doc['type'],
                        'data' => $doc['data'],
                        'document_type' => $doc['file_type'],
                        'files' => json_encode($files)
                    ];
                }
                Documents::insert($documents);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Toastr::error($ex->getMessage());
            return back();
        }

        Toastr::success(trans('messages.deliveryman_added_successfully'));
        return redirect('admin/delivery-man/list');
    }

    public function edit($id)
    {
        $delivery_man = DeliveryMan::with("documents")->find($id);
        // dd($delivery_man->documents->where('type', 'DL'));
        return view('admin-views.delivery-man.edit', compact('delivery_man'));
    }

    public function status(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
        $delivery_man->status = $request->status;

        try {
            if ($request->status == 0) {
                $delivery_man->auth_token = null;
                if (isset($delivery_man->fcm_token)) {
                    $data = [
                        'title' => trans('messages.suspended'),
                        'description' => trans('messages.your_account_has_been_suspended'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'block'
                    ];
                    Helpers::send_push_notif_to_device($delivery_man->fcm_token, $data);

                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'delivery_man_id' => $delivery_man->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(trans('messages.push_notification_faild'));
        }

        $delivery_man->save();

        Toastr::success(trans('messages.deliveryman_status_updated'));
        return back();
    }

    public function suspension_create(Request $request)
    {
        $request->validate([
            'suspension_start' => 'required',
            'suspension_end' => 'required|after:suspension_start',
        ], [
            'suspension_end.after' => __('messages.End time must be after the start time')
        ]);

        $delivery_man = DeliveryMan::find($request->id);

        $sp_log = new SuspensionLog();
        $sp_log->delivery_man_id = $delivery_man->id;
        $sp_log->suspension_start = $request->suspension_start;
        $sp_log->suspension_end = $request->suspension_end;
        $sp_log->details = $request->details;

        $suspension_time = Carbon::parse($request->suspension_start);
        $now_time = Carbon::parse(now());
        $now_time_plus_two = Carbon::parse(now())->addMinutes(2);
        // dd($suspension_time, $now_time, $now_time_plus_two);

        try {
            if ($sp_log->save()) {
                $delivery_man->auth_token = null;
                if ($suspension_time->eq($now_time) || $now_time_plus_two->gte($suspension_time)) {
                    if (isset($delivery_man->fcm_token)) {
                        $data = [
                            'title' => trans('messages.suspended'),
                            'description' => trans('messages.your_account_has_been_suspended'),
                            'order_id' => '',
                            'image' => '',
                            'type' => 'suspend'
                        ];
                        Helpers::send_push_notif_to_device($delivery_man->fcm_token, $data);

                        DB::table('user_notifications')->insert([
                            'data' => json_encode($data),
                            'delivery_man_id' => $delivery_man->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Toastr::warning($e->getMessage());
        }

        Toastr::success(trans('messages.deliveryman_status_updated'));
        return back();
    }

    public function suspension_destroy($suspension_id)
    {
        $sp_log = SuspensionLog::findOrFail($suspension_id);
        $sp_log->delete();
        Toastr::success(trans('messages.rider_suspension_deleted_successfully'));
        return back();
    }

    public function reviews_status(Request $request)
    {
        $review = DMReview::find($request->id);
        $review->status = $request->status;
        $review->save();
        Toastr::success(trans('messages.review_visibility_updated'));
        return back();
    }

    public function earning(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
        $delivery_man->earning = $request->status;

        $delivery_man->save();

        Toastr::success(trans('messages.deliveryman_type_updated'));
        return back();
    }

    public function update_application(Request $request)
    {
        $delivery_man = DeliveryMan::findOrFail($request->id);
        $delivery_man->application_status = $request->status;
        if ($request->status == 'approved') $delivery_man->status = 1;
        $delivery_man->save();

        Toastr::success(trans('messages.application_status_updated_successfully'));
        return back();
    }

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

        $delivery_man = DeliveryMan::with("documents")->find($id);

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
        return redirect('admin/delivery-man/list');
    }

    public function delete(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
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

    public function get_deliverymen(Request $request)
    {
        $key = explode(' ', $request->q);
        $zone_ids = isset($request->zone_ids) ? (count($request->zone_ids) > 0 ? $request->zone_ids : []) : 0;
        $data = DeliveryMan::when($zone_ids, function ($query) use ($zone_ids) {
            return $query->whereIn('zone_id', $zone_ids);
        })
            ->when($request->earning, function ($query) {
                return $query->earning();
            })
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('identity_number', 'like', "%{$value}%");
                }
            })->active()->limit(8)->get(['id', DB::raw('CONCAT(f_name, " ", l_name) as text')]);
        return response()->json($data);
    }

    public function get_account_data(DeliveryMan $deliveryman)
    {
        $wallet = $deliveryman->wallet;
        $cash_in_hand = 0;
        $balance = 0;

        if ($wallet) {
            $cash_in_hand = $wallet->collected_cash;
            $balance = round($wallet->total_earning - $wallet->total_withdrawn - $wallet->pending_withdraw, config('round_up_to_digit'));
        }
        return response()->json(['cash_in_hand' => $cash_in_hand, 'earning_balance' => $balance], 200);
    }

    public function settings(Request $request)
    {
        $documents = Helpers::get_business_settings('documents_type');
        return view('admin-views.delivery-man.settings', compact('documents'));
    }

    public function update_settings(Request $request)
    {
        $documents = Helpers::get_business_settings('documents_type');
        $data = is_array($documents) ? $documents : [];
        if (in_array($request->document_title, array_column($data, 'document_title'))) {
            Toastr::error(trans('messages.document_already_exists'));
            return back();
        }
        array_push($data, [
            'document_title' => $request->document_title,
            'document_type' => $request->document_type,
            'file_count' => $request->number_of_files,
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'documents_type'], [
            'value' => json_encode($data)
        ]);
        Toastr::success(trans('messages.document_type_added'));
        return back();
    }

    public function delete_documents($key)
    {
        $data = Helpers::get_business_settings('documents_type');
        if ($data && array_key_exists($key, $data)) {
            array_splice($data, $key, 1);
            DB::table('business_settings')->updateOrInsert(['key' => 'documents_type'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(trans('messages.document_type_deleted'));
            return back();
        }
        Toastr::error(trans('messages.not_found'));
        return back();
    }
    // public function rider_performance(Request $request)
    // {
    //     $dm = DeliveryMan::with(['reviews','suspension_logs','suspension_log','documents'])->withCount(['orders','orders as delivered_orders_count'=>function($query){
    //         $query->wherePaymentMethod('cash_on_delivery');
    //     },'orders as ongoing_orders'=>function($query){
    //         $query->Ongoing();
    //     },'orders as delivered_orders'=>function($query){
    //         $query->delivered();
    //     },'orders as canceled_or_failed_orders'=>function($query){
    //         $query->whereIn('order_status',['failed','canceled']);
    //     }
    //     ])->withSum('account_transactions', 'amount')->where('type','zone_wise')
    //     //->where(['id' => $id])
    //     ->get();
    //         $status = $request->query('status');
    //         $on_going = ['processing', 'out_for_delivery', 'picked_up', 'hand_over'];

    //         $orders = Order::when($status != 'on_going' , function($query)use($status){
    //             $query->where('order_status', $status);
    //         })->when($status == 'on_going', function($query)use($on_going){
    //             $query->whereIn('order_status', $on_going);
    //         })->whereDeliveryManId($dm->id)->latest()->paginate(config('default_pagination'));

    //         return view('admin-views.delivery-man.view.order', compact('dm', 'orders', 'status'));


    // }
    public function rider_performance($status, Request $request)
    {
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'restaurant', 'delivery_man'])
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('restaurant', function ($q) use ($request) {
                    return $q->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->whereRaw('created_at <> schedule_at');
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'delivered', function ($query) {
                return $query->Delivered();
            })
            ->when($status == 'canceled', function ($query) {
                return $query->Canceled();
            })
            ->when($status == 'failed', function ($query) {
                return $query->failed();
            })
            ->when($status == 'refunded', function ($query) {
                return $query->Refunded();
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->Scheduled();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(($status != 'all' && $status != 'scheduled' && $status != 'canceled' && $status != 'refund_requested' && $status != 'refunded' && $status != 'delivered' && $status != 'failed'), function ($query) {
                return $query->OrderScheduledIn(30);
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('restaurant', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('order_status', $request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                return $query->scheduled();
            })
            ->when(isset($request->order_type), function ($query) use ($request) {
                return $query->where('order_type', $request->order_type);
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->orWhere('id', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->where('delivery_man_id', '!=', null)
            ->Notpos()
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));
        // dd($orders);
        $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
        $scheduled = isset($request->scheduled) ? $request->scheduled : 0;
        $vendor_ids = isset($request->vendor) ? $request->vendor : [];
        $zone_ids = isset($request->zone) ? $request->zone : [];
        $from_date = isset($request->from_date) ? $request->from_date : null;
        $to_date = isset($request->to_date) ? $request->to_date : null;
        $order_type = isset($request->order_type) ? $request->order_type : null;
        $total = $orders->total();

        $dm = DeliveryMan::with(['reviews', 'suspension_logs', 'suspension_log', 'documents'])->withCount([
            'orders', 'orders as delivered_orders_count' => function ($query) {
                $query->wherePaymentMethod('cash_on_delivery');
            }, 'orders as ongoing_orders' => function ($query) {
                $query->Ongoing();
            }, 'orders as delivered_orders' => function ($query) {
                $query->delivered();
            }, 'orders as canceled_or_failed_orders' => function ($query) {
                $query->whereIn('order_status', ['failed', 'canceled']);
            }
        ])->withSum('account_transactions', 'amount')->where('type', 'zone_wise')
            //  ->where('delivery_man_id','!=',null)
            ->get();
        // dd($dm);

        return view('admin-views.delivery-man.rider-performance', compact('dm', 'orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'order_type'));
    }

    // public function delivery_search(Request $request)
    // {
    //     $key = explode(' ', $request['search']);
    //     $orders=Order::where(function ($q) use ($key) {
    //         foreach ($key as $value) {
    //             $q->orWhere('id', 'like', "%{$value}%")
    //                 ->orWhere('order_status', 'like', "%{$value}%")
    //                 ->orWhere('transaction_reference', 'like', "%{$value}%");
    //         }
    //     })->pos()->limit(100)->get();
    //     $parcel_order =  false;
    //     return view('admin-views.delivery-man.rider-performance', compact('dm','orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'order_type'));
    // }

}
