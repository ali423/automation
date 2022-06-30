<?php

return [
    'name' => 'نام',
    'type' => 'نوع',
    'mobile' => 'شماره موبایل',
    'comp_name' => 'نام شرکت',
    'address' => 'آدرس',
    'warning_limit' => 'حد هشدار موجودی(کیلوگرم)',
    'sales_price' => 'قیمت فروش  هر کیلوگرم(تومان)',
    'purchase_price' => 'قیمت خرید هر کیلوگرم (تومان)',
    'capacity' => 'گنجایش (کیلوگرم)',
    'comment' => 'توضیحات',
    'empty_space' => 'فضای خالی(کیلوگرم)',
    'full_space' => 'فضای پر(کیلوگرم)',
    'file' => 'فایل',
    'base_price' => 'قیمت پایه هر کیلوگرم (تومان)',
    'lastname' => 'نام خانوادگی',
    'full_name' => 'نام نام خانوادگی',
    'title' => 'عنوان',
    'status' => 'وضعیت',
    'zip_code' => 'کد پستی',
    'phone' => 'تلفن ثابت',
    'national_code' => 'کد ملی',
    'economic_code' => 'کد اقتصادی',
    'user_name' => 'نام کاربری',
    'password' => 'کلمه عبور',
    'c_password' => 'تکرار کلمه عبور',
    'permissions' => 'دسترسی ها',
    'created_at' => 'تاریخ ایجاد',
    'creator' => 'ایجاد کننده',
    'commodities_list' => 'لیست کالا ها',
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
        'warehouse_amount' => 'مقدار موجود در این انبار',
        'units' => [
            'kg' => 'کیلوگرم',
            'keg' => 'بشکه',
            'twenty_liters' => 'بسته بیست لیتری',
        ],
        'material_type' => 'ماده اولیه تشکیل دهنده',
        'material_amount' => 'مقدار درصد کیلوگرم فرآورده',
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
    'importing_request' => [
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
