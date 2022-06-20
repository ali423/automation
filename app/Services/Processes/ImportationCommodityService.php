<?php

namespace App\Services\Processes;

use App\Models\ImportingRequest;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ImportationCommodityService extends BaseService
{
    public function __construct()
    {
        //
    }

    public function create($data, $file)
    {

        foreach ($data['commodity_id'] as $key => $value) {
            $commodity[$value] = [
                'warehouses_id' => $data['warehouse_id'][$key],
                'amount' => $this->calculateCommodityAmount($data['amount'][$key], $data['unit'][$key]),
            ];
        }
        $user = auth()->user();
        DB::transaction(function () use ($data, $commodity, $user,$file) {
            $request = ImportingRequest::query()->create([
                'status' => 'awaiting_approval'
            ]);
            $request->commodities()->attach($commodity);
            if (isset($data['comment'])) {
                $request->comments()->create([
                    'user_id' => $user->id,
                    'body' => $data['comment'],
                ]);
            }
            if (!empty($file)) {
                $data['file'] = $file->storeAs('public/upload/importing-commodity', str_shuffle(time()) . $file->getClientOriginalName());
                $request->files()->create([
                    'user_id' => $user->id,
                    'source' => $data['file'],
                ]);
            }
        });
        return true;

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
