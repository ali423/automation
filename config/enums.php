<?php

return [
    'models' => [
        'App\Models\Role' => [
            'fa_name' => 'نقش',
            'url' => 'role',
            'relations'=>[
                'permissions'=>'دسترسی ها'
            ]
        ],
        'App\Models\User' => [
            'fa_name' => 'کاربران',
            'url' => 'user',
            'relations'=>[

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

];
