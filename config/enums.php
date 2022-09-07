<?php

return [
    'models' => [
        'App\Models\Role' => [
            'fa_name' => 'نقش',
            'url' => 'role',
            'relations' => [
                'permissions' => [
                    'fa_name' => 'دسترسی ها',
                    'primary_key' => 'role_id',
                ],
                'users' => [
                    'fa_name' => 'کاربران',
                    'primary_key' => 'role_id',
                ],
            ]
        ],
        'App\Models\User' => [
            'fa_name' => 'کاربران',
            'url' => 'user',
            'relations' => [
                'role' => [
                    'fa_name' => 'نقش',
                ],
            ]
        ],
        'App\Models\Commodity' => [
            'fa_name' => 'کالا ها',
            'url' => 'commodity',
            'relations' => [
                'materials' => [
                    'fa_name' => 'فرمول ساخت',
                    'primary_key' => 'material_id',
                    'pivots' => [
                        'percentage' => 'درصد تشکیل دهنده'
                    ],
                ],
                'warehouses' => [
                    'fa_name' => 'انبار کالا',
                    'primary_key' => 'commodity_id',
                    'pivots' => [
                        'commodity_amount' => 'مقدار کالا'
                    ],
                ],
            ]
        ],
        'App\Models\Warehouse' => [
            'fa_name' => 'انبار ها',
            'url' => 'warehouse',
            'relations' => [
                'commodities' => [
                    'fa_name' => 'کالای های موجود در انبار',
                    'primary_key' => 'warehouse_id',
                    'pivots' => [
                        'commodity_amount' => 'مقدار کالا'
                    ],
                ],
            ]
        ],
        'App\Models\ImportingRequest' => [
            'fa_name' => 'درخواست ورود کالا',
            'url' => 'importing-request',
            'relations' => [
                'commodities' => [
                    'fa_name' => 'کالا ها',
                    'primary_key' => 'importation_id',
                    'pivots' => [
                        'amount' => 'مقدار کالا',
                        'warehouses_id' => 'انبار',
                        'unit' => 'واحد اندازه گیری',
                    ],
                ],
            ]
        ],
        'App\Models\Customer' => [
            'fa_name' => 'مشتری ها',
            'url' => 'customer',
            'relations' => [

            ]
        ],
        'App\Models\Seller' => [
            'fa_name' => 'فروشنده ها',
            'url' => 'seller',
            'relations' => [

            ]
        ],
        'App\Models\WithdrawalRequest' => [
            'fa_name' => 'درخواست فروش کالا',
            'url' => 'withdrawal-request',
            'relations' => [
                'commodities' => [
                    'fa_name' => 'کالا ها',
                    'primary_key' => 'withdrawal_id',
                    'pivots' => [
                        'amount' => 'مقدار کالا',
                        'price' => 'قیمت فروش',
                        'unit' => 'واحد اندازه گیری',
                    ],
                ],
            ]
        ],
        'App\Models\Order' => [
            'fa_name' => 'سفارشات',
            'url' => 'order',
            'relations' => [

            ]
        ],
    ],

    'activity_types' => [
        'create' => 'ایجاد',
        'update' => 'ویرایش',
        'delete' => 'حذف',
        'sync' => 'ویرایش گزینه ها',
        'attach' => 'ضمیمه کردن',
        'detach' => 'حذف گزینه ها',
        'pivot_update' => 'ویرایش متغیر',
    ],
    'db_enums' => [

    ],
    'sms_key'=>'4377594C753757655072325257523741317A3346695A6D436836747052547970',
];
