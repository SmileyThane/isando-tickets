<?php

namespace App\Http\Controllers\API;

use App\Activity;
use App\ActivityType;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        $request['datetime'] = Carbon::parse($request->date . ' ' . $request->time);
        $activity = Activity::query()->create($request->all());

        return self::showResponse(true, $activity);
    }

    public function update(Request $request, $id)
    {
        $request['datetime'] = Carbon::parse($request->date . ' ' . $request->time);

        $activity = Activity::query()->find($id)->update($request->all());

        return self::showResponse(true, $activity);
    }

    public function destroy($id)
    {
        $activity = Activity::query()->find($id);
        if ($activity){
            $activity->delete();
        }

        return self::showResponse(true);
    }

    public function getTypes()
    {
        return self::showResponse(true, ActivityType::all());
    }

    public function storeType(Request $request)
    {
        $activityType = ActivityType::query()->create($request->all());

        return self::showResponse(true, $activityType);
    }

    public function updateType(Request $request, $id)
    {
        $activityType = ActivityType::query()->find($id)->update($request->all());

        return self::showResponse(true, $activityType);
    }

    public function destroyType($id): JsonResponse
    {
        $activityType = ActivityType::query()->find($id);
        if ($activityType){
            $activityType->delete();
        }

        return self::showResponse(true);
    }
}
