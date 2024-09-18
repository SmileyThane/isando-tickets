<?php

declare(strict_types=1);

namespace App\Repositories;

use App\ExternalSource;
use App\ExternalSourceType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ExternalSourceTypeRepository
{
    public function index(): Collection {
        return ExternalSourceType::all();
    }

    public function create(array $data): ExternalSourceType {
        return ExternalSourceType::firstOrCreate(
            ['name' => $data['name']],
            ['description' => $data['description']],
        );
    }

    public function update(int $id, array $data): array {
        if ($type = $this->getById($id)) {
            $type->update([
                'name' => Arr::get($data, 'name', $type->name),
                'description' => Arr::get($data, 'description', $type->description),
            ]);

            return [true, $type->refresh()];
        }

        return [false, null];
    }

    public function delete(int $id): bool {
        if ($type = $this->getById($id)) {
            $type->delete();

            return true;
        }

        return false;
    }

    public function getById(int $id): ?ExternalSource {
        return ExternalSourceType::find($id);
    }
}
