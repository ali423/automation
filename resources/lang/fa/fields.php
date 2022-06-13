<?php

return [
    'name' => 'نام',
    'type' => 'نوع',
    'sales_price'=>'قیمت فروش (تومان)',
    'purchase_price'=>'قیمت خرید (تومان)',
    'capacity'=>'گنجایش (لیتر)',
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
        'material_type'=>'ماده اولیه تشکیل دهنده',
        'material_amount'=>'مقدار در صد کیلوگرم فرآورد',
        'types' => [
            'material' => 'ماده اولیه',
            'product' => 'فرآورده',
        ],
    ],
    'warehouse' => [
        'types' => [
            'tank' => 'مخزن تانکر',
            'hall' => 'سالن',
        ],
        'status' => [
            'active' => 'فعال',
            'inactive' => 'غیر فعال',
        ],
    ]
];
?>
