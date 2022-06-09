<?php

namespace App\Services\Processes;

use function PHPUnit\Framework\isNull;

class ImportationCommodityService
{
    public function __construct()
    {
        //
    }

    public function create($data,$file){

        if (!isNull($file)){
            $data['logo'] = $file->storeAs('public/upload/vc', str_shuffle(time()) . $file->getClientOriginalName());
        }

    }
}
