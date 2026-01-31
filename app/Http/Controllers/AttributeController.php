<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Services\AttributeService;
use App\Http\Requests\AttributeRequest;
use App\Http\Requests\AttributeUpdateRequest;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->authorizeResource(Attribute::class);
        $this->shareView();
        $this->attributeService = $attributeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Attribute::query()->with('creator');
        
        // Search functionality
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Order by ID descending
        $query->orderBy('id', 'DESC');
        
        // Pagination
        $perPage = request('per_page', 10);
        $attributes = $query->paginate($perPage)->appends(request()->query());
        
        return view('dashboard.attribute.index', [
            'attributes' => $attributes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.attribute.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $this->attributeService->create($data);
            return redirect()->route('attribute.index')
                ->with('success', 'ویژگی با موفقیت ایجاد شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در ایجاد ویژگی: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        return view('dashboard.attribute.show', [
            'attribute' => $attribute
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        return view('dashboard.attribute.edit', [
            'attribute' => $attribute
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeUpdateRequest $request, Attribute $attribute)
    {
        try {
            $this->attributeService->update($attribute, $request->validated());
            return redirect()->route('attribute.index')
                ->with('success', 'ویژگی با موفقیت ویرایش شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در ویرایش ویژگی: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        try {
            $this->attributeService->delete($attribute);
            return redirect()->route('attribute.index')
                ->with('success', 'ویژگی با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در حذف ویژگی: ' . $e->getMessage());
        }
    }
}
