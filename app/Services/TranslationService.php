<?php

namespace App\Services;

use App\Repositories\TranslationRepository;
use Illuminate\Support\Collection;

class TranslationService
{
    public function __construct(
        private TranslationRepository $repository
    ) {
    }

    public function getTranslation(int $id): ?array
    {
        $translation = $this->repository->find($id);
        return $translation ? $translation->toArray() : null;
    }

    public function createTranslation(array $data): array
    {
        return $this->repository->create($data)->toArray();
    }

    public function updateTranslation(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteTranslation(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function searchTranslations(?string $query = null, ?string $tag = null, ?string $locale = null): Collection
    {
        return $this->repository->search($query, $tag, $locale);
    }

    public function exportTranslations(?string $tag = null, ?string $locale = null): array
    {
        return $this->repository->getForExport($tag, $locale);
    }
}
