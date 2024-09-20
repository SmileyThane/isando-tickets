<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use App\ExternalSource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ExternalSourceRepository
{
    public function index(int $typeId = null): LengthAwarePaginator {
        $externalSource = ExternalSource::query();
        if ($typeId !== null) {
            $externalSource->where('external_source_type_id', '=', $typeId);
        }
        return $externalSource->with('externalSourceType')
            ->paginate(
                perPage: request()->input('perPage', 15),
                page: request()->input('page', 1),
            );
    }

    public function create(array $data): ExternalSource {
        return ExternalSource::create(self::prepareDataForSave($data));
    }

    public function update(int $id, array $data): array {
        if ($source = $this->getById($id)) {
            $source->update(self::prepareDataForSave($data));

            return [true, $source->refresh()];
        }

        return [false, null];
    }

    public function delete(int $id): bool {
        if ($source = $this->getById($id)) {
            $source->delete();

            return true;
        }

        return false;
    }

    public function getById(int $id): ?ExternalSource {
        return ExternalSource::find($id);
    }

    private static function prepareDataForSave(array $data): array {
        $entity = match (Arr::get($data, 'entityType')) {
            class_basename(Client::class) => Client::class,
            class_basename(Company::class) => Company::class,
            class_basename(CompanyUser::class) => CompanyUser::class,
            class_basename(ClientCompanyUser::class) => ClientCompanyUser::class,
            default => null,
        };

        if (is_null($entity) || !isset($data['entityId'])) {
            unset($data['entityType'], $data['entityId']);
        }

        return $data;
    }
}
