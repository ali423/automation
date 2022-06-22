<?php

namespace App\Services\Processes;

use App\Models\ImportingRequest;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ImportingRequestService extends BaseService
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
                'amount' => $data['amount'][$key],
                'unit' => $data['unit'][$key],
            ];
        }
        $user = auth()->user();
        DB::transaction(function () use ($data, $commodity, $user, $file) {
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
                $this->uploadFile($file, 'importing-commodity', $request);
            }
        });
        return true;
    }

    public function update($importing_request, $data, $file)
    {
        foreach ($data['commodity_id'] as $key => $value) {
            $commodity[$value] = [
                'warehouses_id' => $data['warehouse_id'][$key],
                'amount' => $data['amount'][$key],
                'unit' => $data['unit'][$key],
            ];
        }
        $user = auth()->user();
        DB::transaction(function () use ($data, $commodity, $user, $file, $importing_request) {
            $importing_request->commodities()->sync($commodity);
            if (isset($data['comment'])) {
                $importing_request->comments()->create([
                    'user_id' => $user->id,
                    'body' => $data['comment'],
                ]);
            }
            if (!empty($file)) {
                $this->uploadFile($file, 'importing-commodity', $importing_request);
            }
        });
        return true;
    }
    public function delete($importing_request){
        DB::transaction(function () use ($importing_request) {
            $importing_request->commodities()->detach();
            $importing_request->delete();
        });
        return true;
    }
}
