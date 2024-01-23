<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Milon\Barcode\DNS1D;
use Modules\LaundryManagement\Http\Controllers\Admin\FacilityRoomTrackController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryBannerController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryDeliveryTypeController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryDMController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryItemController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryOrderController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryVehicleTypeController;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryZoneController;
use Modules\LaundryManagement\Http\Controllers\Admin\ServicesController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['prefix' => 'laundry', 'as' => 'laundry.', 'middleware' => ['module:laundry']], function () {
        Route::group(['prefix' => 'service', 'as' => 'service.'], function () {
            Route::get('/', [ServicesController::class, 'index'])->name('index');
            Route::get('create', [ServicesController::class, 'create'])->name('create');
            Route::post('store', [ServicesController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ServicesController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [ServicesController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [ServicesController::class, 'destroy'])->name('delete');
            Route::get('status/{id}/{status}', [ServicesController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'item', 'as' => 'item.'], function () {
            Route::get('/', [LaundryItemController::class, 'index'])->name('index');
            Route::get('create', [LaundryItemController::class, 'create'])->name('create');
            Route::post('store', [LaundryItemController::class, 'store'])->name('store');
            Route::get('edit/{id}', [LaundryItemController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [LaundryItemController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [LaundryItemController::class, 'destroy'])->name('delete');
            Route::get('status/{id}/{status}', [LaundryItemController::class, 'status'])->name('status');
            Route::post('search', [LaundryItemController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
            Route::get('/', [LaundryBannerController::class, 'index'])->name('index');
            Route::post('store', [LaundryBannerController::class, 'store'])->name('store');
            Route::get('edit/{banner}', [LaundryBannerController::class, 'edit'])->name('edit');
            Route::put('update/{banner}', [LaundryBannerController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [LaundryBannerController::class, 'status'])->name('status');
            Route::delete('delete/{banner}', [LaundryBannerController::class, 'destroy'])->name('delete');
            Route::post('search', [LaundryBannerController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'vehicle-type', 'as' => 'vehicle-type.'], function () {
            Route::get('/', [LaundryVehicleTypeController::class, 'index'])->name('index');
            Route::post('store', [LaundryVehicleTypeController::class, 'store'])->name('store');
            Route::get('edit/{banner}', [LaundryVehicleTypeController::class, 'edit'])->name('edit');
            Route::put('update/{banner}', [LaundryVehicleTypeController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [LaundryVehicleTypeController::class, 'status'])->name('status');
            Route::delete('delete/{banner}', [LaundryVehicleTypeController::class, 'destroy'])->name('delete');
            Route::post('search', [LaundryVehicleTypeController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'delivery-type', 'as' => 'delivery-type.'], function () {
            Route::get('/', [LaundryDeliveryTypeController::class, 'index'])->name('index');
            Route::post('store', [LaundryDeliveryTypeController::class, 'store'])->name('store');
            Route::get('edit/{type}', [LaundryDeliveryTypeController::class, 'edit'])->name('edit');
            Route::put('update/{type}', [LaundryDeliveryTypeController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [LaundryDeliveryTypeController::class, 'status'])->name('status');
            Route::delete('delete/{type}', [LaundryDeliveryTypeController::class, 'destroy'])->name('delete');
            Route::post('search', [LaundryDeliveryTypeController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::get('list/{status}', [LaundryOrderController::class, 'index'])->name('list');
            Route::get('status', [LaundryOrderController::class, 'status'])->name('status');
            Route::get('details/{id}', [LaundryOrderController::class, 'details'])->name('details');
            Route::post('search', [LaundryOrderController::class, 'search'])->name('search');
            Route::get('filter/reset', [LaundryOrderController::class, 'filter_reset'])->name('filter_reset');
            Route::post('filter', [LaundryOrderController::class, 'filter'])->name('filter');
            Route::post('add-payment-ref-code/{id}',[LaundryOrderController::class, 'add_payment_ref_code'])->name('add-payment-ref-code');
            Route::post('update-shipping/{order}', [LaundryOrderController::class, 'update_shipping'])->name('update-shipping');
        });

        Route::group(['prefix' => 'dispatch', 'as' => 'dispatch.'], function () {
            Route::get('list/{status}', [LaundryOrderController::class, 'dispatch_list'])->name('list');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
            Route::get('list', [LaundryDMController::class, 'index'])->name('list');
            Route::get('add', [LaundryDMController::class, 'create'])->name('add');
            Route::post('store', [LaundryDMController::class, 'store'])->name('store');
            Route::post('search', [LaundryDMController::class, 'search'])->name('search');
            Route::get('edit/{id}', [LaundryDMController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [LaundryDMController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [LaundryDMController::class, 'destroy'])->name('delete');
            Route::post('add-delivery-man', [LaundryDMController::class, 'add_delivery_man'])->name('add-delivery-man');
            Route::get('preview/{id}/{tab?}/{sub_tab?}', [LaundryDMController::class, 'preview'])->name('preview');
        });

        Route::group(['prefix' => 'in-house-tracking', 'as' => 'in-house-tracking.'], function () {
            Route::get('facility-room-check', [FacilityRoomTrackController::class, 'facility_room_check_index'])->name('facility-room-check-index');
            Route::post('facility-room-check', [FacilityRoomTrackController::class, 'facility_room_check'])->name('facility-room-check');
            Route::get('processing-items', [FacilityRoomTrackController::class, 'processing_items'])->name('processing-items');
            Route::get('get-items-barcodes', [FacilityRoomTrackController::class, 'get_items_barcode'])->name('get-items-barcodes');
            Route::get('ready-for-delivery', [FacilityRoomTrackController::class, 'ready_for_delivery_index'])->name('ready-for-delivery');
            Route::post('ready-for-delivery', [FacilityRoomTrackController::class, 'ready_for_delivery'])->name('ready-for-delivery');
        });

        Route::group(['prefix' => 'zone', 'as' => 'zone.'], function () {
            Route::get('/', [LaundryZoneController::class, 'index'])->name('home');
            Route::post('store', [LaundryZoneController::class, 'store'])->name('store');
            Route::get('edit/{id}', [LaundryZoneController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [LaundryZoneController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [LaundryZoneController::class, 'status'])->name('status');
            Route::post('search', [LaundryZoneController::class, 'search'])->name('search');
            Route::get('zone-filter/{id}', [LaundryZoneController::class, 'search'])->name('zonefilter');
            Route::get('get-all-zone-cordinates/{id?}', [LaundryZoneController::class, 'get_all_zone_cordinates'])->name('zoneCoordinates');
        });
    });

});