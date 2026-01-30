<?php

namespace App\Services;

use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class AttributeService extends BaseService
{
    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            return Attribute::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_by' => $data['created_by'],
            ]);
        });
    }

    public function update(Attribute $attribute, $data)
    {
        return DB::transaction(function () use ($attribute, $data) {
            return $attribute->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    public function delete(Attribute $attribute)
    {
        return DB::transaction(function () use ($attribute) {
            return $attribute->delete();
        });
    }
}
