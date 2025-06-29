<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\UnitService;
use App\Http\Requests\UnitRequest;
use App\Http\Requests\UnitUpdateRequest;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->authorizeResource(Unit::class);
        $this->shareView();
        $this->unitService = $unitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.unit.index', [
            'units' => $units,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitRequest $request)
    {
        try {
            $this->unitService->create($request->validated());
            return redirect()->route('unit.index')
                ->with('success', 'واحد با موفقیت ایجاد شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در ایجاد واحد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return view('dashboard.unit.show', [
            'unit' => $unit
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('dashboard.unit.edit', [
            'unit' => $unit
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitUpdateRequest $request, Unit $unit)
    {
        try {
            $this->unitService->update($unit, $request->validated());
            return redirect()->route('unit.index')
                ->with('success', 'واحد با موفقیت ویرایش شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در ویرایش واحد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            $this->unitService->delete($unit);
            return redirect()->route('unit.index')
                ->with('success', 'واحد با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در حذف واحد: ' . $e->getMessage());
        }
    }
}
