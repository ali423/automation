<?php

namespace App\Services\Processes;

use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Warehouse;
use App\Services\BaseService;
use App\Services\InventoryService;
use App\Services\CommodityUnitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportingRequestService extends BaseService
{
    protected $inventoryService;
    protected $commodityUnitService;

    public function __construct(InventoryService $inventoryService, CommodityUnitService $commodityUnitService)
    {
        $this->inventoryService = $inventoryService;
        $this->commodityUnitService = $commodityUnitService;
    }

    public function create($data, $file)
    {

        foreach ($data['commodity_id'] as $key => $value) {
            $exists_commodity = Commodity::query()->findOrFail($value);
            if (empty($data['purchase_price'][$key])) {
                $error_price = \Illuminate\Validation\ValidationException::withMessages([
                    'purchase_price.' . $key => ['قیمت خرید فرآرده باید وارد شود.'],
                ]);
                throw $error_price;
            }
            
            $commodity[$value] = [
                'amount' => $data['amount'][$key],
                'unit_id' => $data['unit'][$key],
                'purchase_price' => $data['purchase_price'][$key] ?? null,
            ];
        }
        $number = $this->generateUniqueNumber(ImportingRequest::class, 'number');
        $user = auth()->user();
        
        $request = ImportingRequest::query()->create([
            'seller_id' => $data['seller_id'],
            'status' => 'awaiting_approval',
            'number' => $number,
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
        return $request;
    }

    public function update($importing_request, $data, $file)
    {
        foreach ($data['commodity_id'] as $key => $value) {
            $exists_commodity = Commodity::query()->findOrFail($value);
            if (empty($data['purchase_price'][$key])) {
                $error_price = \Illuminate\Validation\ValidationException::withMessages([
                    'purchase_price.' . $key => ['قیمت خرید فرآرده باید وارد شود.'],
                ]);
                throw $error_price;
            }
            
            $commodity[$value] = [
                'amount' => $data['amount'][$key],
                'unit_id' => $data['unit'][$key],
                'purchase_price' => $data['purchase_price'][$key] ?? null,
            ];
        }
        $user = auth()->user();
        
        $importing_request->commodities()->sync($commodity);
        $importing_request->update([
           'seller_id'=>$data['seller_id'],
        ]);
        if (isset($data['comment'])) {
            $importing_request->comments()->create([
                'user_id' => $user->id,
                'body' => $data['comment'],
            ]);
        }
        if (!empty($file)) {
            $this->uploadFile($file, 'importing-commodity', $importing_request);
        }
        return true;
    }

    public function delete($importing_request)
    {
        $importing_request->commodities()->detach();
        $importing_request->delete();
        return true;
    }

    public function approvalImporting($importing_request)
    {
        foreach ($importing_request->commodities as $selected_commodity) {
            // Get the selected unit ID directly from the pivot
            $selectedUnitId = $selected_commodity->pivot->unit_id;
            
            // Update commodity purchase price
            $selected_commodity->update([
                'purchase_price' => $selected_commodity->pivot->purchase_price
            ]);
            
            // Convert amount to main unit for inventory storage
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $selected_commodity,
                $selected_commodity->pivot->amount,
                $selectedUnitId
            );
            
            // Add stock to inventory using the main unit
            $this->inventoryService->addStock(
                $selected_commodity->id,
                $selected_commodity->unit_id, // Use commodity's main unit
                $amountInMainUnit,
                $selected_commodity->pivot->purchase_price
            );
        }
        
        $importing_request->update([
            'status' => 'approvaled',
        ]);
        return true;
    }

    public function checkImporting($importing_request)
    {
        // Validate that the import request can be processed
        foreach ($importing_request->commodities as $commodity) {
            // Convert amount to main unit using CommodityUnitService for validation
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $commodity,
                $commodity->pivot->amount,
                $commodity->pivot->unit_id
            );
            
            // Basic validation - ensure amount is positive
            if ($amountInMainUnit <= 0) {
                $data['success'] = false;
                $data['error'] = 'مقدار کالا باید بیشتر از صفر باشد.';
                return $data;
            }
        }
        
        $data['success'] = true;
        return $data;
    }

    public function checkImportingStore($data)
    {
        // No warehouse capacity checks needed with inventory system
        $data['success'] = true;
        return $data;
    }

    public function rejectImporting($importing_request)
    {
        return $importing_request->update([
            'status' => 'rejected',
        ]);
    }



    public function validationSecondLayer($data)
    {
        $commodities = $data['commodity_id'];
        $units = $data['unit'];
        $amounts = $data['amount'];
        $array_counts = [
            count($commodities),
            count($units),
            count($amounts),
        ];
        $array_keys = array_merge(array_keys($commodities), array_keys($units), array_keys($amounts));
        if (count(array_unique($array_counts)) != 1 || count(array_unique($array_keys)) != count($commodities)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
            ]);
        }
    }

    public function getReportData($data)
    {
        $from_data = $this->convertShamsiDate($data['date_from']);
        $from_to = $this->convertShamsiDate($data['date_to']);
        $requests = ImportingRequest::query()->whereHas('commodities', function ($query) use ($data) {
            $query->where('id', $data['commodity_id']);
        })->where('status', 'approvaled')
            ->where('created_at', '>=', $from_data)
            ->where('created_at', '<=', $from_to)
            ->with('commodities')
            ->get()->toArray();
        if (count($requests) < 1){
            return [
                'error'=>'هیچ خریدی برای کالای انتخابی در تاریخ مربوطه یافت نشد .'
            ] ;
        }
       return $this->calculateAveragePrice($requests, $data['commodity_id'],$data['date_from'],$data['date_to']);
    }

    protected function convertShamsiDate($shamsi_date)
    {
        $date_arr = explode('/', $shamsi_date);
        $month = $date_arr[1];
        $day = $date_arr[2];
        if (strlen($date_arr[1]) < 2) {
            $month = '0' . $date_arr[1];
        }
        if (strlen($date_arr[2]) < 2) {
            $day = '0' . $date_arr[2];
        }
        $new_date = $date_arr[0] . '/' . $month . '/' . $day;
        return \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $new_date);
    }

    public function calculateAveragePrice($requests, $commodity_id,$date_from,$date_to)
    {
        $db_commodity = Commodity::query()->findOrFail($commodity_id);
        $res = [
            'number' => $db_commodity->number,
            'title' => $db_commodity->title,
            'commodity_purchase_price' => $db_commodity->purchase_price,
            'date_from'=>$date_from,
            'date_to'=>$date_to,
        ];
        $numerator = 0;
        $denominator = 0;
        foreach ($requests as $request) {
            $commodity_key = array_search($commodity_id, array_column($request['commodities'], 'id'));
            $commodity = $request['commodities'][$commodity_key];
            
            // Get unit information
            $unit = \App\Models\Unit::find($commodity['pivot']['unit_id']);
            $unitName = $unit ? ($unit->name . ' (' . $unit->symbol . ')') : 'نامشخص';
            
            $res['requests'][] = [
                'request_id'=>$request['id'],
                'amount' => $commodity['pivot']['amount'],
                'unit' => $unitName,
                'product_purchase_price' => $commodity['pivot']['purchase_price'],
                'created_at'=>$request['created_at'],
            ];
            
            // Convert amount to main unit for calculations
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $db_commodity,
                $commodity['pivot']['amount'],
                $commodity['pivot']['unit_id']
            );
            
            // Use purchase price without conversion
            $numerator = $numerator + ($amountInMainUnit * $commodity['pivot']['purchase_price']);
            $denominator = $denominator + $amountInMainUnit;
        }
        $res['avr_price'] = round(($numerator/$denominator),);
        return $res;
    }
    
}
