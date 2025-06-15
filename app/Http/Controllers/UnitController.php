<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Unit::class);
        $this->shareView();
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'symbol' => 'required|string|max:10',
        ]);

        Unit::create($validated);

        return redirect()->route('unit.index')
            ->with('success', 'Unit created successfully.');
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
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'symbol' => 'required|string|max:10',
        ]);

        $unit->update($validated);

        return redirect()->route('unit.index')
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('unit.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
