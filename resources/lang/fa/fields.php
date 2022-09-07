<?php

return [
    'name' => 'نام',
    'type' => 'نوع کالا',
    'mobile' => 'شماره موبایل',
    'comp_name' => 'نام شرکت',
    'address' => 'آدرس',
    'warning_limit' => 'حد هشدار موجودی',
    'sell-price' => 'قیمت فروش',
    'sell-price_per_unit' => 'قیمت فروش هر واحد کالا',
    'sales_price' => 'قیمت فروش',
    'purchase_price' => 'قیمت خرید',
    'purchase_unit_price' => 'قیمت خرید (ریال)',
    'avr_purchase_price' => 'میانگین قیمت خرید هر کیلوگرم(ریال)',
    'capacity' => 'گنجایش',
    'comment' => 'توضیحات',
    'date_from'=>'از تاریخ',
    'date_to'=>'تا تاریخ',
    'empty_space' => 'فضای خالی(کیلوگرم)',
    'full_space' => 'فضای پر(کیلوگرم)',
    'file' => 'فایل',
    'base_price' => 'قیمت پایه هر کیلوگرم (ریال)',
    'lastname' => 'نام خانوادگی',
    'customer' => 'خریدار',
    'seller' => 'فروشنده',
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
    'total_price' => ' مبلغ کل فاکتور(ریال)',
    'unit' => 'واحد اندازه گیری',
    'deadline' => 'تاریخ سررسید',
    'done_date' => 'زمان تحویل سفارش',
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
            'twenty_liters' => 'گالن (20 لیتری)',
        ],
        'material_type' => 'ماده اولیه تشکیل دهنده',
        'material_amount' => 'مقدار درصد فرآورده',
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
        'number' => 'شماره درخواست',
        'status' => [
            'awaiting_approval' => 'در انتظار تایید',
            'approvaled' => 'تایید شده',
            'rejected' => 'رد شده',
            'expired' => 'منقضی شده',
            'done' => 'کامل شده',
        ]
    ],
    'withdrawal-request' => [
        'number' => 'شماره درخواست',
        'status' => [
            'awaiting_approval' => 'در انتظار تایید',
            'approvaled' => 'تایید شده',
            'rejected' => 'رد شده',
            'expired' => 'منقضی شده',
            'done' => 'کامل شده',
        ]
    ],
    'order' => [
        'status' => [
            'pending' => 'درحال پردازش',
            'done' => 'تحویل شده',
        ]
    ]
];
?>
