<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\LaundryManagement\Http\Controllers\Admin\LaundryDMAuthController;
use Modules\LaundryManagement\Http\Controllers\Api\LaundryBannerController;
use Modules\LaundryManagement\Http\Controllers\Api\LaundryConfigController;
use Modules\LaundryManagement\Http\Controllers\Api\LaundryDeliveryTypeController;
use Modules\LaundryManagement\Http\Controllers\Api\LaundryDMController;
use Modules\LaundryManagement\Http\Controllers\Api\LaundryItemController;
use Modules\LaundryManagement\Http\Controllers\Api\LaundryOrderController;
use Modules\LaundryManagement\Http\Controllers\Api\ServicesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::group(['prefix' => 'laundry'], function () {
        Route::group(['prefix' => 'delivery-man'], function () {
            Route::post('login', [LaundryDMAuthController::class, 'login']);
        });
    });
});

Route::group(['prefix' => 'services'], function () {
    Route::get('list', [ServicesController::class, 'service_list']);
});

Route::group(['prefix' => 'laundry-item'], function () {
    Route::get('list/{service_id}', [LaundryItemController::class, 'get_item_list']);
});

Route::group(['prefix' => 'laundry-banner'], function () {
    Route::get('list', [LaundryBannerController::class, 'banner_list']);
});

Route::group(['prefix' => 'laundry-delivery-type'], function () {
    Route::get('list', [LaundryDeliveryTypeController::class, 'type_list']);
});
Route::group(['prefix' => 'laundry-config'], function () {
    Route::post('get-routes', [LaundryConfigController::class, 'get_routes']);
});

Route::group(['prefix' => 'customer', 'middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'laundry'], function () {
        Route::group(['prefix' => 'order'], function () {
            Route::post('submit', [LaundryOrderController::class, 'order_submit']);
            Route::get('list', [LaundryOrderController::class, 'get_order_list']);
            Route::get('running-orders', [LaundryOrderController::class, 'get_running_orders']);
            Route::get('details', [LaundryOrderController::class, 'get_order_details']);
            Route::put('cancel', [LaundryOrderController::class, 'cancel_order']);
            Route::get('track', [LaundryOrderController::class, 'track_order']);
        });
        Route::get('get-notifications', [LaundryOrderController::class, 'get_notifications']);
    });
});

Route::group(['prefix' => 'delivery-man', 'middleware' => ['dm.api']], function () {
    Route::group(['prefix' => 'laundry'], function () {
        Route::get('profile', [LaundryDMController::class, 'get_profile']);
        Route::put('update-profile', [LaundryDMController::class, 'update_profile']);
        Route::group(['prefix' => 'order'], function () {
            Route::get('list/{status}', [LaundryDMController::class, 'get_orders_by_filter']);
            Route::get('details', [LaundryDMController::class, 'get_order_details']);
            Route::put('update-order-status', [LaundryDMController::class, 'update_order_status']);
            Route::put('update-order', [LaundryDMController::class, 'update_order']);
            Route::get('running-orders-list', [LaundryDMController::class, 'get_running_orders']);
            Route::post('save-picture', [LaundryDMController::class, 'save_picture']);
            Route::get('orders-history-list/{status}', [LaundryDMController::class, 'get_orders_history']);
        });
        Route::get('get-notifications', [LaundryDMController::class, 'get_notifications']);
        Route::get('get-routes', [LaundryDMController::class, 'get_routes']);
    });
});