<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class);
        $this->shareView();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(ActivityRequest $request)
    {
        $models=$request->get('action');
        $object_id=$request->get('object_id');
        $object_type=$request->get('object_type');
        $query=Activity::query()->orderBy('id', 'DESC');
        $name_space = substr("App\Models\Role", 0,11);
        if (!empty($object_id) && !empty($object_type) ){
            $query=$query->Where('record_change_type',$name_space.$object_type)->where('record_change_id',$object_id);
        }elseif (!empty($models)){
            foreach ($models as $model){
               $query=$query->orWhere('record_change_type',$name_space.$model);
            }
        }
        return view('dashboard.activity.index',
            [
                'activities'=>$query->get(),
            ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return view('dashboard.activity.show',
            [
                'activity'=>$activity,
            ]);
    }

}
