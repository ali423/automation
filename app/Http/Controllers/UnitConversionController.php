<?php

namespace App\Http\Controllers;

use App\Models\UnitConversion;
use App\Models\Commodity;
use App\Models\Unit;
use App\Services\UnitConversionService;
use App\Http\Requests\UnitConversionRequest;
use App\Http\Requests\UnitConversionUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitConversionController extends Controller
{
    protected $service;

    public function __construct(UnitConversionService $service)
    {
        $this->service = $service;
        $this->authorizeResource(UnitConversion::class);
        $this->shareView();
    }

    /**
     * Display a listing of unit conversions for a commodity.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $commodityId = $request->get('commodity_id');
        $commodities = Commodity::orderBy('id', 'DESC')->get();
        $units = Unit::all();

        $query = $this->service->queryWithRelations();
        if ($commodityId) {
            $query->where('commodity_id', $commodityId);
        }
        $conversions = $query->orderBy('created_at', 'DESC')->get();

        return view('dashboard.unit-conversion.index', [
            'conversions' => $conversions,
            'commodities' => $commodities,
            'selectedCommodityId' => $commodityId,
            'units' => $units,
        ]);
    }

    /**
     * Show the form for creating a new unit conversion.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $commodities = Commodity::orderBy('id', 'DESC')->get();
        $units = Unit::all();
        return view('dashboard.unit-conversion.create', [
            'commodities' => $commodities,
            'units' => $units,
            'defaultFromUnitId' => null,
        ]);
    }

    /**
     * Store a newly created unit conversion.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(UnitConversionRequest $request)
    {
        $this->service->addConversion(
            $request->commodity_id,
            $request->from_unit_id,
            $request->to_unit_id,
            $request->conversion_rate
        );
        return redirect(route('unit-conversion.index'))
            ->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified unit conversion.
     *
     * @param \App\Models\UnitConversion $unitConversion
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(UnitConversion $unitConversion)
    {
        return view('dashboard.unit-conversion.show', [
            'unitConversion' => $unitConversion,
        ]);
    }

    /**
     * Show the form for editing the specified unit conversion.
     *
     * @param \App\Models\UnitConversion $unitConversion
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(UnitConversion $unitConversion)
    {
        $commodities = Commodity::orderBy('id', 'DESC')->get();
        $units = Unit::all();
        return view('dashboard.unit-conversion.edit', [
            'unitConversion' => $unitConversion,
            'commodities' => $commodities,
            'units' => $units,
        ]);
    }

    /**
     * Update the specified unit conversion.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\UnitConversion $unitConversion
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(UnitConversionUpdateRequest $request, UnitConversion $unitConversion)
    {
        $this->service->update($unitConversion, $request->validationData());
        return redirect(route('unit-conversion.index', ['commodity_id' => $unitConversion->commodity_id]))
            ->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified unit conversion.
     *
     * @param \App\Models\UnitConversion $unitConversion
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(UnitConversion $unitConversion)
    {
        $commodityId = $unitConversion->commodity_id;
        $unitConversion->delete();
        
        return redirect(route('unit-conversion.index', ['commodity_id' => $commodityId]))
            ->with('successful', 'اطلاعات حذف شدند.');
    }

    /**
     * Convert units via AJAX.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'from_unit_id' => 'required|exists:units,id',
            'to_unit_id' => 'required|exists:units,id',
            'commodity_id' => 'required|exists:commodities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $convertedAmount = $this->service->convert(
            $request->amount,
            $request->from_unit_id,
            $request->to_unit_id,
            $request->commodity_id
        );

        if ($convertedAmount === null) {
            return response()->json([
                'success' => false,
                'message' => 'No conversion rate found for the specified units and commodity.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'converted_amount' => $convertedAmount,
            'message' => 'Conversion completed successfully.'
        ]);
    }
} 