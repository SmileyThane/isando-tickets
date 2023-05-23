<?php

namespace App\Repositories;

use App\Activity;
use Carbon\Carbon;

class ActivityRepository
{
    protected $fileRepository;

    /**
     * @param FileRepository $fileRepository
     */
    public function __construct(
        FileRepository $fileRepository,
    )
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Create Activity
     *
     * @param array $data
     * @return Activity
     */
    public function create(array $data): Activity
    {
        $data['datetime'] = Carbon::parse(($data['date'] ?? null) . ' ' . ($data['time'] ?? null));
        $activity = Activity::create($data);

        $this->saveAttachments($data['files'] ?? [], $activity->id);

        return $activity->load('attachments');
    }

    /**
     * Update Activity
     *
     * @param array $data
     * @param int $id
     * @return Activity
     */
    public function update(array $data, int $id): Activity
    {
        $data['datetime'] = Carbon::parse(($data['date'] ?? null) . ' ' . ($data['time'] ?? null));
        $activity = Activity::query()->find($id);
        if ($activity) {
            $activity->update($data);
            $this->saveAttachments($data['files'] ?? [], $activity->id);
        }


        return $activity->load('attachments');
    }

    /**
     * Save attachments of Activity
     *
     * @param array $attachments
     * @param int $activityId
     * @return void
     */
    private function saveAttachments(array $attachments, int $activityId): void
    {
        foreach ($attachments as $file) {
            $this->fileRepository->store($file, $activityId, Activity::class);
        }
    }
}
