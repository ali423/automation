<?php

return [
    'model' => [
        'Role' => [
            'fa_name' => 'نقش',
            'url' => 'role',
            'relations'=>[
                'permissions'=>'دسترسی ها'
            ]
        ],
    ],
    'activity_types' => [
        'create' => 'ایجاد',
        'update' => 'ویرایش',
        'delete' => 'حذف',
        'sync' => 'ویراش گزینه ها',
        'attach' => 'ضمیمه کردن',
        'detach' => 'حذف گزینه ها',
        'pivot_update' => 'ویرایش متغیر',
    ],

];
