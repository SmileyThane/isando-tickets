<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalSource\CreateExternalSourceRequest;
use App\Http\Requests\ExternalSource\UpdateExternalSourceRequest;
use App\Repositories\ExternalSourceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExternalSourceController extends Controller
{
    public function __construct(private readonly ExternalSourceRepository $repository)
    {
    }

    public function all(Request $request): JsonResponse {
        return self::showResponse(
            success: true,
            data: $this->repository->index($request->type_id),
        );
    }

    public function create(CreateExternalSourceRequest $request): JsonResponse {
        return self::showResponse(
            success: true,
            data: $this->repository->create($request->validated()),
        );
    }

    public function show(int $id): JsonResponse {
        if ($source = $this->repository->getById($id)) {

            return self::showResponse(
                success: true,
                data: $source,
            );
        }

        return self::showResponse(false);
    }

    public function update(UpdateExternalSourceRequest $request, int $id): JsonResponse {
        return self::showResponse(
            ...$this->repository->update($id, $request->validated())
        );
    }

    public function delete(int $id): JsonResponse {
        return self::showResponse(
            $this->repository->delete($id)
        );
    }

    public function getUri(int $id): Response|JsonResponse {
        if ($source = $this->repository->getById($id)) {
            $script = "
                <script>
                    (function() {
                        window.location.href = '$source->full_url';
                        setTimeout(function() {
                            window.close();
                        }, 100);
                    })();
                </script>
            ";

            return response($script, 200)
                ->header('Content-Type', 'application/javascript');
        };

        return self::showResponse(false);
    }

    public function getPassword(int $id): Response|JsonResponse {
        if($source = $this->repository->getById($id)) {
            $script = "
                <script>
                    (function() {
                        var textarea = document.createElement('textarea');
                        textarea.value = '$source->password';
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        window.close();
                    })();
                </script>
            ";

            return response($script, 200)
                ->header('Content-Type', 'application/javascript');
        };

        return self::showResponse(false);
    }
}
