<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait PaginationTrait
{
    /**
     * Get paginated results with common pagination logic
     *
     * @param Builder $query
     * @param Request $request
     * @param int $defaultPerPage
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected function getPaginatedResults(Builder $query, Request $request, int $defaultPerPage = 25, array $options = [])
    {

        $options = array_merge($this->getDefaultPaginationOptions(), $options);

        $this->validatePaginationParameters($request, $options);

        // Get pagination parameters from request
        $perPage = $request->get('per_page', $defaultPerPage);
        $page = $request->get('page', 1);
        
        // Validate per_page to prevent abuse
        $perPage = min(max($perPage, 1), 100);
        
        // Apply search if provided
        if ($request->filled('search')) {
            $search = $request->get('search');
            $this->applySearch($query, $search, $options['searchable_fields'] ?? []);
        }
        
        // Apply filters if provided
        if ($request->filled('filters')) {
            $filters = $request->get('filters');
            if (is_string($filters)) {
                $filters = json_decode($filters, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $filters = [];
                }
            }
            if (is_array($filters)) {
                $this->applyFilters($query, $filters, $options['filterable_fields'] ?? []);
            }
        }
        
        // Apply sorting
        $sortField = $request->get('sort_by', $options['default_sort_field'] ?? 'id');
        $sortDirection = $request->get('sort_direction', $options['default_sort_direction'] ?? 'desc');
        
        if (in_array($sortField, $options['sortable_fields'] ?? ['id'])) {
            $query->orderBy($sortField, $sortDirection);
            // Add secondary sort by id to ensure deterministic ordering
            // This prevents non-deterministic pagination when multiple records have the same value for the primary sort field
            if ($sortField !== 'id') {
                $query->orderBy('id', $sortDirection);
            }
        }
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

     /**
     * Validate pagination parameters
     *
     * @param Request $request
     * @param array $options
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatePaginationParameters(Request $request, array $options)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:' . ($options['max_per_page'] ?? 100),
            'sort_by' => 'sometimes|string|in:' . implode(',', $options['sortable_fields'] ?? ['id']),
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'search' => 'sometimes|string|max:255',
            'filters' => 'sometimes|json',
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    
    /**
     * Apply search to query
     *
     * @param Builder $query
     * @param string $search
     * @param array $searchableFields
     */
    protected function applySearch(Builder $query, string $search, array $searchableFields)
    {
        if (empty($searchableFields)) {
            return;
        }
        
        $query->where(function ($q) use ($search, $searchableFields) {
            foreach ($searchableFields as $field) {
                $this->applyFieldCondition($q, $field, $search, 'LIKE', "%{$search}%", 'orWhere');
            }
        });
    }
    
    /**
     * Apply filters to query
     *
     * @param Builder $query
     * @param array $filters
     * @param array $filterableFields
     */
    protected function applyFilters(Builder $query, array $filters, array $filterableFields)
    {
        foreach ($filters as $field => $value) {
            if (in_array($field, $filterableFields) && !empty($value)) {
                // Special handling for status field to support both 'approvaled' and 'approved'
                if ($field === 'status' && $value === 'approved') {
                    $query->where(function ($q) {
                        $q->where('status', 'approved')
                          ->orWhere('status', 'approvaled');
                    });
                } else {
                    $this->applyFieldCondition($query, $field, $value, '=', $value, 'where');
                }
            }
        }
    }
    
    /**
     * Apply field condition to query (handles both direct and relationship fields)
     *
     * @param Builder $query
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @param mixed $searchValue
     * @param string $method
     */
    protected function applyFieldCondition(Builder $query, string $field, $value, string $operator, $searchValue, string $method)
    {
        if (str_contains($field, '.')) {
            // Handle relationship fields
            [$relation, $fieldName] = explode('.', $field, 2);
            $query->$method(function ($q) use ($relation, $fieldName, $searchValue, $operator) {
                $q->whereHas($relation, function ($subQ) use ($fieldName, $searchValue, $operator) {
                    $subQ->where($fieldName, $operator, $searchValue);
                });
            });
        } else {
            // Handle direct fields
            $query->$method($field, $operator, $searchValue);
        }
    }
    
    /**
     * Get pagination metadata for API responses
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    protected function getPaginationMetadata(LengthAwarePaginator $paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }
    
    /**
     * Get default pagination options
     *
     * @return array
     */
    protected function getDefaultPaginationOptions(): array
    {
        return [
            'default_per_page' => 25,
            'max_per_page' => 100,
            'searchable_fields' => [],
            'filterable_fields' => [],
            'sortable_fields' => ['id', 'created_at', 'updated_at'],
            'default_sort_field' => 'id',
            'default_sort_direction' => 'desc',
        ];
    }
}
