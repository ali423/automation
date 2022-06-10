<?php

return [
    'name' => 'نام',
    'type' => 'نوع',
    'amount' => 'قیمت فروش (تومان)',
    'capacity' => 'گنجایش (لیتر)',
    'lastname' => 'نام خانوادگی',
    'full_name' => 'نام نام خانوادگی',
    'title' => 'عنوان',
    'status' => 'وضعیت',
    'user_name' => 'نام کاربری',
    'password' => 'کلمه عبور',
    'c_password' => 'تکرار کلمه عبور',
    'permissions' => 'دسترسی ها',
    'created_at' => 'تاریخ ایجاد',
    'creator' => 'ایجاد کننده',
    'details' => 'جزئیات',
    'unit' => 'واحد اندازه گیری',
    'role' => [
        'name' => 'نام نقش',
        'title' => 'عنوان نقش',
    ],
    'user' => [
        'status' => [
            'active' => 'فعال',
            'inactive' => 'غیر فعال',
        ],
    ],
    'commodity' => [
        'number' => 'شماره کالا',
        'name' => 'نام کالا',
        'amount' => 'مقدار کالا',
        'units' => [
            'kg' => 'کیلوگرم',
            'keg' => 'بشکه',
            'twenty_liters' => 'بسته بیست لیتری',
        ],
        'types' => [
            'material' => 'ماده اولیه',
            'product' => 'فرآورده',
        ],
    ],
    'warehouse' => [
        'name' => 'نام انبار جهت ذخیره کالا',
        'types' => [
            'tank' => 'مخزن تانکر',
            'hall' => 'سالن',
        ],
        'status' => [
            'active' => 'فعال',
            'inactive' => 'غیر فعال',
        ],
    ],
    'commodity_importing' => [
        'status' => [
            'awaiting_approval' => 'در انتظار تایید',
            'approvaled' => 'تایید شده',
            'rejected' => 'رد شده',
            'expired' => 'منقضی شده',
            'done' => 'کامل شده',
        ]
    ]
];
?>
