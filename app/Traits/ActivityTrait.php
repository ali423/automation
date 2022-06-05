<?php

namespace App\Traits;

use App\Models\Activity;
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
        return $this->activities()->where('action', 'create')->first()->user ?? null;
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
                    $data = [
                        'previous_activity_id' => end($previous_activities)['id'],
                        'user_id' => auth()->user()->id,
                        'relation_name' => end($previous_activities)['relation_name'],
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
                    $data = [
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'previous_activity_id' => end($previous_activities)['id'],
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
                $previous_activities=$item->activities()->get()->toArray();

                if (auth()->check()) {
                    $data = [
                        'previous_activity_id' => end($previous_activities)['id'],
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'relation_model'=>get_class($item->$relationName->first()),
                        'user_id' => auth()->user()->id,
                        'relation_name' => $relationName,
                        'action' => 'sync',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivotIdsAttributes),
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
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $data = [
                        'previous_activity_id' => end($previous_activities)['id'],
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'relation_model'=>get_class($item->$relationName->first()),
                        'user_id' => auth()->user()->id,
                        'relation_name' => $relationName,
                        'action' => 'attach',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivotIdsAttributes),
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
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $data = [
                        'previous_activity_id' => end($previous_activities)['id'],
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'relation_model'=>get_class($item->$relationName->first()),
                        'user_id' => auth()->user()->id,
                        'relation_name' => $relationName,
                        'action' => 'detach',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivotIdsAttributes),
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
                $previous_activities=$item->activities()->get()->toArray();
                if (auth()->check()) {
                    $data = [
                        'previous_activity_id' => end($previous_activities)['id'],
                        'record_change_id' => $item->id,
                        'record_change_type' => get_class($item),
                        'user_id' => auth()->user()->id,
                        'relation_model'=>get_class($item->$relationName->first()),
                        'relation_name' => $relationName,
                        'action' => 'pivot_update',
                        'data'=>json_encode($item->toArray()),
                        'pivot_data' => json_encode($pivotIdsAttributes),
                    ];
                    Activity::query()->insert($data);
                }
                DB::commit();
            });
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
