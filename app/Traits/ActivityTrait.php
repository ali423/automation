<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\Commodity;
use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;
use Illuminate\Support\Facades\DB;

trait ActivityTrait
{
    use PivotEventTrait;

    public function activities()
    {
        return $this->morphMany(Activity::class, 'record_change');
    }

    public function getModelDetailAttribute()
    {
        return config('enums.models')[get_class($this)] ?? [
            'fa_name' => class_basename($this),
            'url' => null,
            'relations' => [],
        ];
    }

    public function getCreatorUserAttribute()
    {
        return $this->activities->where('action', 'create')->first()->user ?? null;
    }

    public static function bootActivityTrait()
    {
        self::createActivity();
        self::updateActivity();
        self::deleteActivity();
        self::pivotActivity();
    }

    protected static function createActivity()
    {
        try {
            static::creating(function () {
                DB::beginTransaction();
            });
            static::created(function ($item) {
                if (auth()->check()) {
                    $item->activities()->create([
                        'user_id' => auth()->user()->id,
                        'action' => 'create',
                        'data' => json_encode($item->toArray()),
                    ]);
                }
                DB::commit();
            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    protected static function updateActivity()
    {

        try {
            static::updating(function () {
                DB::beginTransaction();
            });
            static::updated(function ($item) {
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                    $data = [
                        'previous_activity_id' => $last_activity['id'] ?? null,
                        'user_id' => auth()->user()->id,
                        'relation_name' => $last_activity['relation_name'] ?? null,
                        'action' => 'update',
                        'data' => json_encode($item->toArray()),
                    ];
                    $item->activities()->create($data);
                }
                DB::commit();
            });
        } catch (\Exception $e) {
            return $e->getMessage();
            DB::rollback();
        }
    }

    protected static function deleteActivity()
    {
        try {
            static::deleting(function () {
                DB::beginTransaction();
            });
            static::deleted(function ($item) {
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                    $data = [
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'previous_activity_id' => $last_activity['id'] ?? null,
                        'user_id' => auth()->user()->id,
                        'action' => 'delete',
                        'data' => json_encode($item->toArray()),
                    ];
                    Activity::query()->create($data);
                }

                DB::commit();
            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    protected static function pivotActivity()
    {
        self::pivotSyncActivity();
        self::pivotAttachActivity();
        self::pivotDetachActivity();
        self::pivotUpdateActivity();
    }

    protected static function pivotSyncActivity()
    {
        try {
            static::pivotSyncing(function () {
                DB::beginTransaction();
            });
            static::pivotSynced(function ($item, $second, $third, $fourth = null) {
                try {
                    $parsed = static::parsePivotSyncedArgs($item, $second, $third, $fourth);
                    if ($parsed === null) {
                        \Log::warning('ActivityTrait pivotSynced: Could not parse pivot event arguments', [
                            'item_class' => is_object($item) ? get_class($item) : gettype($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    [$item, $relationName, $changes] = $parsed;

                    if (!static::validatePivotRelation($item, $relationName)) {
                        \Log::warning('ActivityTrait pivotSynced: Invalid relationName', [
                            'relationName' => $relationName,
                            'item_class' => get_class($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    $pivot_res['attached'] = static::getRelatedData($item, $relationName, $changes['attached'] ?? []);
                    $pivot_res['detached'] = static::getRelatedData($item, $relationName, $changes['detached'] ?? []);
                    $pivot_res['updated'] = static::getRelatedData($item, $relationName, $changes['updated'] ?? []);
                    $previous_activities = $item->activities()->get()->toArray();
                    if (auth()->check()) {
                        $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                        $data = [
                            'previous_activity_id' => $last_activity['id'] ?? null,
                            'record_change_id' => $item->id,
                            'record_change_type' => get_class($item),
                            'relation_model' => static::getRelationModel($item, $relationName),
                            'user_id' => auth()->user()->id,
                            'relation_name' => $relationName,
                            'action' => 'sync',
                            'data' => json_encode($item->toArray()),
                            'pivot_data' => json_encode($pivot_res),
                        ];
                        Activity::query()->insert($data);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    \Log::error('ActivityTrait pivotSynced activity failed: ' . $e->getMessage(), [
                        'model' => is_object($item) ? get_class($item) : 'unknown',
                        'action' => 'sync',
                        'user_id' => auth()->user()->id ?? null,
                        'trace' => $e->getTraceAsString(),
                    ]);
                    DB::rollback();
                }
            });
        } catch (\Exception $e) {
            \Log::error('ActivityTrait pivotSync error: ' . $e->getMessage());
            DB::rollback();
        }
    }

    protected static function pivotAttachActivity()
    {
        try {
            static::pivotAttaching(function () {
                DB::beginTransaction();
            });
            static::pivotAttached(function ($item, $second, $third, $fourth = null) {
                try {
                    $parsed = static::parsePivotAttachArgs($item, $second, $third, $fourth);
                    if ($parsed === null) {
                        \Log::warning('ActivityTrait pivotAttached: Could not parse pivot event arguments', [
                            'item_class' => is_object($item) ? get_class($item) : gettype($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    [$item, $relationName, $pivotIds, $pivotIdsAttributes] = $parsed;

                    if (!static::validatePivotRelation($item, $relationName)) {
                        \Log::warning('ActivityTrait pivotAttached: Invalid relationName', [
                            'relationName' => $relationName,
                            'item_class' => get_class($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    $pivotData = !empty($pivotIdsAttributes) ? $pivotIdsAttributes : $pivotIds;
                    $pivot_res = static::getRelatedData($item, $relationName, $pivotData);
                    $previous_activities = $item->activities()->get()->toArray();
                    if (auth()->check()) {
                        $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                        $data = [
                            'previous_activity_id' => $last_activity['id'] ?? null,
                            'record_change_id' => $item->id,
                            'record_change_type' => get_class($item),
                            'relation_model' => static::getRelationModel($item, $relationName),
                            'user_id' => auth()->user()->id,
                            'relation_name' => $relationName,
                            'action' => 'attach',
                            'data' => json_encode($item->toArray()),
                            'pivot_data' => json_encode($pivot_res),
                        ];
                        Activity::query()->insert($data);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    \Log::error('ActivityTrait pivotAttached activity failed: ' . $e->getMessage(), [
                        'model' => get_class($item),
                        'relation' => $relationName ?? 'unknown',
                        'action' => 'attach',
                        'user_id' => auth()->user()->id ?? null,
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Don't throw the error, just log it and continue
                    DB::rollback();
                    // Try to commit without activity tracking
                    try {
                        DB::commit();
                    } catch (\Exception $commitError) {
                        \Log::error('Failed to commit transaction after activity error: ' . $commitError->getMessage());
                        DB::rollback();
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('ActivityTrait pivotAttached error: ' . $e->getMessage());
            DB::rollback();
        }
    }

    protected static function pivotDetachActivity()
    {
        try {
            static::pivotDetaching(function ($item, $second, $third, $fourth = null) {
                try {
                    DB::beginTransaction();

                    $parsed = static::parsePivotDetachArgs($item, $second, $third, $fourth);
                    if ($parsed === null) {
                        \Log::warning('ActivityTrait pivotDetaching: Could not parse pivot event arguments', [
                            'item_class' => is_object($item) ? get_class($item) : gettype($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    [$item, $relationName, $pivotIds] = $parsed;

                    if (!static::validatePivotRelation($item, $relationName)) {
                        \Log::warning('ActivityTrait pivotDetaching: Invalid relationName', [
                            'relationName' => $relationName,
                            'item_class' => get_class($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    $pivot_res = static::getRelatedData($item, $relationName, $pivotIds);
                    $previous_activities = $item->activities()->get()->toArray();
                    if (auth()->check()) {
                        $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                        $data = [
                            'previous_activity_id' => $last_activity['id'] ?? null,
                            'record_change_id' => $item->id,
                            'record_change_type' => get_class($item),
                            'relation_model' => static::getRelationModel($item, $relationName),
                            'user_id' => auth()->user()->id,
                            'relation_name' => $relationName,
                            'action' => 'detach',
                            'data' => json_encode($item->toArray()),
                            'pivot_data' => json_encode($pivot_res),
                        ];
                        Activity::query()->insert($data);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    \Log::error('ActivityTrait pivotDetaching activity failed: ' . $e->getMessage(), [
                        'model' => get_class($item),
                        'relation' => $relationName ?? 'unknown',
                        'action' => 'detach',
                        'user_id' => auth()->user()->id ?? null,
                        'trace' => $e->getTraceAsString()
                    ]);
                    DB::rollback();
                    try {
                        DB::commit();
                    } catch (\Exception $commitError) {
                        \Log::error('Failed to commit transaction after detach activity error: ' . $commitError->getMessage());
                        DB::rollback();
                    }
                }
            });

            static::pivotDetached(function ($item, $second, $third, $fourth = null) {

            });
        } catch (\Exception $e) {
            \Log::error('ActivityTrait pivotDetach error: ' . $e->getMessage());
            DB::rollback();
        }
    }

    protected static function pivotUpdateActivity()
    {
        try {
            static::pivotUpdating(function () {
                DB::beginTransaction();
            });
            static::pivotUpdated(function ($item, $second, $third, $fourth = null) {
                try {
                    $parsed = static::parsePivotAttachArgs($item, $second, $third, $fourth);
                    if ($parsed === null) {
                        \Log::warning('ActivityTrait pivotUpdated: Could not parse pivot event arguments', [
                            'item_class' => is_object($item) ? get_class($item) : gettype($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    [$item, $relationName, $pivotIds, $pivotIdsAttributes] = $parsed;

                    if (!static::validatePivotRelation($item, $relationName)) {
                        \Log::warning('ActivityTrait pivotUpdated: Invalid relationName', [
                            'relationName' => $relationName,
                            'item_class' => get_class($item),
                        ]);
                        DB::rollback();
                        return;
                    }

                    $pivotData = !empty($pivotIdsAttributes) ? $pivotIdsAttributes : $pivotIds;
                    $pivot_res = static::getRelatedData($item, $relationName, $pivotData);
                    $previous_activities = $item->activities()->get()->toArray();
                    if (auth()->check()) {
                        $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                        $data = [
                            'previous_activity_id' => $last_activity['id'] ?? null,
                            'record_change_id' => $item->id,
                            'record_change_type' => get_class($item),
                            'user_id' => auth()->user()->id,
                            'relation_model' => static::getRelationModel($item, $relationName),
                            'relation_name' => $relationName,
                            'action' => 'pivot_update',
                            'data' => json_encode($item->toArray()),
                            'pivot_data' => json_encode($pivot_res),
                        ];
                        Activity::query()->insert($data);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    \Log::error('ActivityTrait pivotUpdated activity failed: ' . $e->getMessage(), [
                        'model' => get_class($item),
                        'relation' => $relationName ?? 'unknown',
                        'action' => 'pivot_update',
                        'user_id' => auth()->user()->id ?? null,
                        'trace' => $e->getTraceAsString()
                    ]);
                    DB::rollback();
                    try {
                        DB::commit();
                    } catch (\Exception $commitError) {
                        \Log::error('Failed to commit transaction after pivot update activity error: ' . $commitError->getMessage());
                        DB::rollback();
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('ActivityTrait pivotUpdate error: ' . $e->getMessage());
            DB::rollback();
        }
    }

    protected static function validatePivotRelation($item, string $relationName): bool
    {
        return method_exists($item, $relationName);
    }

    /**
     * @return array{0: object, 1: string, 2: array}|null
     */
    protected static function parsePivotSyncedArgs($item, $second, $third, $fourth = null): ?array
    {
        if (is_string($second) && is_array($third) && static::isSyncChangesArray($third)) {
            return [$item, $second, $third];
        }

        if (is_object($second) && is_string($third) && is_array($fourth) && static::isSyncChangesArray($fourth)) {
            return [$item, $third, $fourth];
        }

        return null;
    }

    protected static function isSyncChangesArray(array $value): bool
    {
        return array_key_exists('attached', $value)
            || array_key_exists('detached', $value)
            || array_key_exists('updated', $value);
    }

    /**
     * @return array{0: object, 1: string, 2: array, 3: array}|null
     */
    protected static function parsePivotAttachArgs($item, $second, $third, $fourth = null): ?array
    {
        if (is_string($second) && is_array($third)) {
            return [$item, $second, $third, is_array($fourth) ? $fourth : []];
        }

        if (is_object($second) && is_string($third) && is_array($fourth)) {
            return [$item, $third, $fourth, []];
        }

        return null;
    }

    /**
     * @return array{0: object, 1: string, 2: array}|null
     */
    protected static function parsePivotDetachArgs($item, $second, $third, $fourth = null): ?array
    {
        if (is_string($second) && is_array($third)) {
            return [$item, $second, $third];
        }

        if (is_object($second) && is_string($third)) {
            return [$item, $third, is_array($fourth) ? $fourth : []];
        }

        return null;
    }

    /**
     * Safely get the relation model class
     */
    protected static function getRelationModel($item, $relationName)
    {
        try {
            // Ensure $relationName is a string
            if (!is_string($relationName)) {
                return null;
            }

            // Check if the relation method exists
            if (!method_exists($item, $relationName)) {
                return null;
            }

            // Get the first related model to determine the class
            $relation = $item->$relationName();
            if ($relation && method_exists($relation, 'first')) {
                $firstModel = $relation->first();
                return $firstModel ? get_class($firstModel) : null;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('ActivityTrait getRelationModel error: ' . $e->getMessage());
            return null;
        }
    }

    static function getRelatedData($item,$relationName,$pivotIdsAttributes){
        // Ensure $relationName is a string
        if (!is_string($relationName)) {
            \Log::warning('ActivityTrait getRelatedData: relationName is not a string: ' . var_export($relationName, true));
            return null;
        }

        // Check if the relation method exists
        if (!method_exists($item, $relationName)) {
            \Log::warning('ActivityTrait getRelatedData: method does not exist: ' . $relationName . ' on ' . get_class($item));
            return null;
        }

        try {
            // Handle case where $pivotIdsAttributes is an array of arrays (full pivot data)
            // instead of just an array of IDs
            $ids = $pivotIdsAttributes;
            if (is_array($pivotIdsAttributes) && !empty($pivotIdsAttributes) && is_array($pivotIdsAttributes[0])) {
                // If it's an array of arrays, extract the IDs from the pivot data
                $ids = array_keys($pivotIdsAttributes);
            }

            // Safely call the relation method
            $relation = $item->$relationName();
            if (!$relation) {
                \Log::warning('ActivityTrait getRelatedData: relation returned null for: ' . $relationName);
                return null;
            }

            $changed_relations=array_column($relation->whereIn('id',$ids)->get()->toArray(),'pivot');
            $relation_data=config('enums.models')[get_class($item)]['relations'][$relationName] ?? null;
            $pivot_exits=$relation_data['pivots'] ?? null;
            if (!empty($pivot_exits)){
                foreach ($changed_relations as $value){
                    foreach (array_keys($pivot_exits) as $pivot){
                        $pivot_res[$value[$relation_data['primary_key']]]['pivots'][$pivot]=$value[$pivot];
                    }
                }
        }else{
            $pivot_res=$pivotIdsAttributes;
        }
        return $pivot_res ?? null;
        } catch (\Exception $e) {
            // Log the error and return null to prevent crashes
            \Log::error('ActivityTrait getRelatedData error: ' . $e->getMessage(), [
                'relationName' => $relationName,
                'item_class' => get_class($item),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
