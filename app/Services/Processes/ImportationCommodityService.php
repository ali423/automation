<?php

namespace App\Services\Processes;

use App\Models\ImportationCommodity;

class ImportationCommodityService
{
    public function __construct()
    {
        //
    }

    public function create($data,$file){
        if (!empty($file)){
            $data['file'] = $file->storeAs('public/upload/importing-commodity/', str_shuffle(time()) . $file->getClientOriginalName());
        }
       return ImportationCommodity::query()->create($data);
    }

//    protected function calculateAmountBySelectedUnit($amount,$unit){
//        switch ($unit) {
//            case 'kg':
//               return $amount;
//            case 'keg':
//                code to be executed if n=label2;
//    break;
//            case label3:
//                code to be executed if n=label3;
//    break;
//    ...
//            default:
//                code to be executed if n is different from all labels;
//        }
//
//    }
}
