<?php

namespace App\Services;

class BaseService
{
    public function calculateCommodityAmount($amount,$unit){
        switch ($unit) {
            case 'keg':
                return round($amount*185,2);
            case 'kg':
                return $amount;
            case 'twenty_liters':
                return round($amount*17.8,2);
        }
    }
    public function uploadFile($file,$patch,$attached){
        $user=auth()->user();
        $file_name=$file->getClientOriginalName();
        $data['file'] = $file->storeAs('public/upload/'.$patch, str_shuffle(time()) . $file_name);
        $attached->files()->create([
            'user_id' => $user->id,
            'source' => $data['file'],
            'name'=>explode('.',$file_name)[0],
            'format'=>$file->extension(),
             'size'=>$file->getSize(),
        ]);
    }
}
