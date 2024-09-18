<?php

declare(strict_types=1);

namespace App\Rules;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyUser;
use Illuminate\Contracts\Validation\Rule;

readonly class EntityExistsRule implements Rule
{
    public function __construct(protected ?string $entityType)
    {
    }

    public function passes($attribute, $value): bool {
        if (is_null($this->entityType) || is_null($value)) {
            return true;
        }

        if (is_null($entityClass = $this->resolveEntityClass())) {
            return false;
        }

        return (new $entityClass)->where('id', $value)->exists();
    }

    public function message(): string {
        return 'The selected :attribute is invalid for the given entity type.';
    }

    private function resolveEntityClass(): ?string {
        return match ($this->entityType) {
            class_basename(Client::class) => Client::class,
            class_basename(Company::class) => Company::class,
            class_basename(CompanyUser::class) => CompanyUser::class,
            class_basename(ClientCompanyUser::class) => ClientCompanyUser::class,
            default => null,
        };
    }
}
