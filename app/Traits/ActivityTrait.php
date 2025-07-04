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
        return config('enums.models')[get_class($this)];
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
            static::pivotSynced(function ($item, $model, $relationName, $pivotIdsAttributes) {
                $pivot_res['attached']=self::getRelatedData($item,$relationName,$pivotIdsAttributes['attached']);
                $pivot_res['detached']=self::getRelatedData($item,$relationName,$pivotIdsAttributes['detached']);
                $pivot_res['updated']=self::getRelatedData($item,$relationName,$pivotIdsAttributes['updated']);
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                    $data = [
                        'previous_activity_id' => $last_activity['id'] ?? null,
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'relation_model'=>get_class($item->$relationName()->first()),
                        'user_id' => auth()->user()->id,
                        'relation_name' => $relationName,
                        'action' => 'sync',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivot_res),
                    ];
                    Activity::query()->insert($data);
                }
                DB::commit();
            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    protected static function pivotAttachActivity()
    {
        try {
            static::pivotAttaching(function () {
                DB::beginTransaction();
            });
            static::pivotAttached(function ($item, $model, $relationName, $pivotIdsAttributes) {
                 $pivot_res=self::getRelatedData($item,$relationName,$pivotIdsAttributes);
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                    $data = [
                        'previous_activity_id' => $last_activity['id'] ?? null,
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'relation_model'=>get_class($item->$relationName()->first()),
                        'user_id' => auth()->user()->id,
                        'relation_name' => $relationName,
                        'action' => 'attach',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivot_res),
                    ];
                    Activity::query()->insert($data);
                }
                DB::commit();
            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    protected static function pivotDetachActivity()
    {
        try {
            static::pivotDetaching(function ($item, $model, $relationName, $pivotIdsAttributes) {
                DB::beginTransaction();
                $pivot_res=self::getRelatedData($item,$relationName,$pivotIdsAttributes);
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                    $data = [
                        'previous_activity_id' => $last_activity['id'] ?? null,
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'relation_model'=>get_class($item->$relationName()->first()),
                        'user_id' => auth()->user()->id,
                        'relation_name' => $relationName,
                        'action' => 'detach',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivot_res),
                    ];
                    Activity::query()->insert($data);
                }
                DB::commit();
            });

            static::pivotDetached(function ($item, $model, $relationName, $pivotIdsAttributes) {

            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    protected static function pivotUpdateActivity()
    {
        try {
            static::pivotUpdating(function () {
                DB::beginTransaction();
            });
            static::pivotUpdated(function ($item, $model, $relationName, $pivotIdsAttributes) {
                $pivot_res=self::getRelatedData($item,$relationName,$pivotIdsAttributes);
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $last_activity = !empty($previous_activities) ? end($previous_activities) : null;
                    $data = [
                        'previous_activity_id' => $last_activity['id'] ?? null,
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'user_id' => auth()->user()->id,
                        'relation_model'=>get_class($item->$relationName()->first()),
                        'relation_name' => $relationName,
                        'action' => 'pivot_update',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivot_res),
                    ];
                    Activity::query()->insert($data);
                }
                DB::commit();
            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    static function getRelatedData($item,$relationName,$pivotIdsAttributes){
        $changed_relations=array_column($item->$relationName()->whereIn('id',$pivotIdsAttributes)->get()->toArray(),'pivot');
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
    }
}
