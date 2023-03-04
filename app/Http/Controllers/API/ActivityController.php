<?php

namespace App\Http\Controllers\API;

use App\Activity;
use App\ActivityType;
use App\Http\Controllers\Controller;
use App\Repositories\ActivityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function __construct(
        protected ActivityRepository $activityRepository,
    )
    {
    }

    public function store(Request $request): JsonResponse
    {
        return self::showResponse(true, $this->activityRepository->create($request->all()));
    }

    public function update(Request $request, $id)
    {
        return self::showResponse(true, $this->activityRepository->update($request->all(), $id));
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
        $types = ActivityType::query()
            ->where('company_id', '=', Auth::user()->employee->company_id)
            ->get();

        return self::showResponse(true, $types);
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
