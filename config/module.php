<?php

return [
    'module_type'=>[
        'food', 'laundry'
    ],

    'food'=>[
        'order_status'=>['accepted'=>true],
        'order_place_to_schedule_interval'=>false,
        'add_on'=>true,
        'stock'=>false,
        'veg_non_veg'=>true,
        'unit'=>false,
        'order_attachment'=>false,
        'always_open'=>false,
        'item_available_time'=>true,
        'show_restaurant_text'=>true,
        'is_parcel'=>false,
        'description'=>'In this type, you can set item available time, no stock management for items and has option to add add-on.',
    ],
   
    'laundry'=>[
        'order_status'=>['accepted'=>false],
        'order_place_to_schedule_interval'=>false,
        'add_on'=>false,
        'stock'=>false,
        'veg_non_veg'=>false,
        'unit'=>false,
        'order_attachment'=>false,
        'always_open'=>true,
        'item_available_time'=>false,
        'show_restaurant_text'=>false,
        'is_parcel'=>true,
        'description'=>'',
    ],
];
