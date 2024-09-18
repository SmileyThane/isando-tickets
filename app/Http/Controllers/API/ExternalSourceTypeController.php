<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalSourceType\CreateExternalSourceTypeRequest;
use App\Http\Requests\ExternalSourceType\UpdateExternalSourceTypeRequest;
use App\Repositories\ExternalSourceTypeRepository;
use Illuminate\Http\JsonResponse;

class ExternalSourceTypeController extends Controller
{
    public function __construct(protected readonly ExternalSourceTypeRepository $repository)
    {
    }

    public function all(): JsonResponse {
        return self::showResponse(
            success: true,
            data: $this->repository->index(),
        );
    }

    public function create(CreateExternalSourceTypeRequest $request): JsonResponse {
        return self::showResponse(
            success: true,
            data: $this->repository->create($request->validated()),
        );
    }

    public function delete(int $id): JsonResponse {
        return self::showResponse(
            $this->repository->delete($id),
        );
    }

    public function update(int $id, UpdateExternalSourceTypeRequest $request): JsonResponse {
        return self::showResponse(
            ...$this->repository->update($id, $request->validated())
        );
    }

    public function show(int $id): JsonResponse {
        if ($type = $this->repository->getById($id)) {

            return self::showResponse(
                success: true,
                data: $type,
            );
        }

        return self::showResponse(false);
    }
}
