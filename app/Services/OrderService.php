<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Commodity;
use App\Services\CommodityUnitService;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected $commodityUnitService;

    public function __construct(CommodityUnitService $commodityUnitService)
    {
        $this->commodityUnitService = $commodityUnitService;
    }

    /**
     * Create a new order with multiple items
     */
    public function create($data)
    {
        $user = auth()->user();
        
        $order = Order::create([
            'customer_id' => $data['customer_id'],
            'deadline' => $data['deadline'],
            'status' => 'pending'
        ]);

        // Create order items
        if (isset($data['commodity_id']) && is_array($data['commodity_id'])) {
            foreach ($data['commodity_id'] as $index => $commodityId) {
                // Validate unit for this commodity
                $unitId = $data['unit_id'][$index] ?? null;
                if ($unitId) {
                    $commodity = Commodity::find($commodityId);
                    if ($commodity) {
                        $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
                        if (!$selectableUnits->contains('id', $unitId)) {
                            throw new \Exception("واحد انتخاب شده برای کالای {$commodity->title} معتبر نیست.");
                        }
                    }
                }
                
                // Determine price: prefer provided price; fallback to commodity sales_price
                $providedPrice = $data['price'][$index] ?? null;
                $fallbackPrice = optional(Commodity::find($commodityId))->sales_price ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'commodity_id' => $commodityId,
                    'commodity_amount' => $data['commodity_amount'][$index] ?? 0,
                    'unit_id' => $unitId,
                    'price' => ($providedPrice !== null && $providedPrice !== '') ? $providedPrice : $fallbackPrice,
                ]);
            }
        }

        // Add comment if provided
        if (isset($data['comment'])) {
            $order->comments()->create([
                'user_id' => $user->id,
                'body' => $data['comment'],
            ]);
        }

        // Upload file if provided
        if (isset($data['file']) && !empty($data['file'])) {
            $this->uploadFile($data['file'], 'order', $order);
        }

        return $order;
    }

    /**
     * Update an existing order
     */
    public function update(Order $order, $data)
    {
        $user = auth()->user();
        
        // Update main order data
        $order->update([
            'customer_id' => $data['customer_id'],
            'deadline' => $data['deadline'],
        ]);

        // Update or create order items
        if (isset($data['commodity_id']) && is_array($data['commodity_id'])) {
            // Remove existing items
            $order->orderItems()->delete();

            // Create new items
            foreach ($data['commodity_id'] as $index => $commodityId) {
                // Validate unit for this commodity
                $unitId = $data['unit_id'][$index] ?? null;
                if ($unitId) {
                    $commodity = Commodity::find($commodityId);
                    if ($commodity) {
                        $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
                        if (!$selectableUnits->contains('id', $unitId)) {
                            throw new \Exception("واحد انتخاب شده برای کالای {$commodity->title} معتبر نیست.");
                        }
                    }
                }
                
                // Determine price: prefer provided price; fallback to commodity sales_price
                $providedPrice = $data['price'][$index] ?? null;
                $fallbackPrice = optional(Commodity::find($commodityId))->sales_price ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'commodity_id' => $commodityId,
                    'commodity_amount' => $data['commodity_amount'][$index] ?? 0,
                    'unit_id' => $unitId,
                    'price' => ($providedPrice !== null && $providedPrice !== '') ? $providedPrice : $fallbackPrice,
                ]);
            }
        }

        // Add comment if provided
        if (isset($data['comment'])) {
            $order->comments()->create([
                'user_id' => $user->id,
                'body' => $data['comment'],
            ]);
        }

        // Upload file if provided
        if (isset($data['file']) && !empty($data['file'])) {
            $this->uploadFile($data['file'], 'order', $order);
        }

        return $order;
    }

    /**
     * Delete an order
     */
    public function delete(Order $order)
    {
        $order->orderItems()->delete();
        $order->delete();
        return true;
    }

    /**
     * Update order status to done
     */
    public function updateStatus(Order $order)
    {
        $order->update([
            'status' => 'done',
        ]);
    }


    /**
     * Validate second layer data
     */
    public function validationSecondLayer($data)
    {
        $commodities = $data['commodity_id'];
        $unitIds = $data['unit_id'];
        $amounts = $data['commodity_amount'];
        $array_counts = [
            count($commodities),
            count($unitIds),
            count($amounts),
        ];
        $array_keys = array_merge(array_keys($commodities), array_keys($unitIds), array_keys($amounts));
        if (count(array_unique($array_counts)) != 1 || count(array_unique($array_keys)) != count($commodities)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'commodity_id' => ['اطلاعات کالا و مقدار آن باید متناظر باشند.'],
            ]);
        }

        // Validate units for each commodity
        foreach ($commodities as $index => $commodityId) {
            if (!isset($unitIds[$index])) {
                continue;
            }
            
            $commodity = Commodity::find($commodityId);
            $unitId = $unitIds[$index];
            
            if (!$commodity) {
                continue;
            }
            
            // Check if the unit is valid for this commodity
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $isValidUnit = $selectableUnits->contains('id', $unitId);
            
            if (!$isValidUnit) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "unit_id.{$index}" => ['واحد انتخاب شده برای این کالا معتبر نیست.'],
                ]);
            }
        }
    }
}
