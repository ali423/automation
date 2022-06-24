<?php

return [
    'models' => [
        'App\Models\Role' => [
            'fa_name' => 'نقش',
            'url' => 'role',
            'relations' => [
                'permissions' => 'دسترسی ها'
            ]
        ],
        'App\Models\User' => [
            'fa_name' => 'کاربران',
            'url' => 'user',
            'relations' => [

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

    ]

];
