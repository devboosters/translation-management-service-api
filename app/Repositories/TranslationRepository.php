<?php

namespace App\Repositories;

use App\Models\Translation;
use Illuminate\Support\Collection;

class TranslationRepository
{
    public function find(int $id): ?Translation
    {
        return Translation::find($id);
    }

    public function create(array $data): Translation
    {
        return Translation::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Translation::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Translation::where('id', $id)->delete();
    }

    public function search(?string $query = null, ?string $tag = null, ?string $locale = null): Collection
    {
        return Translation::when($query, function ($q) use ($query) {
            $q->where(function ($q) use ($query) {
                $q->where('key', 'like', "%{$query}%")
                    ->orWhere('value', 'like', "%{$query}%");
            });
        })
            ->when($tag, function ($q) use ($tag) {
                $q->where('tag', $tag);
            })
            ->when($locale, function ($q) use ($locale) {
                $q->where('locale', $locale);
            })
            ->get();
    }

    public function getForExport(?string $tag = null, ?string $locale = null): array
    {
        $query = Translation::query();

        if ($tag) {
            $query->where('tag', $tag);
        }

        if ($locale) {
            $query->where('locale', $locale);
        }

        return $query->get()
            ->groupBy(['locale', 'group'])
            ->map(function ($locales) {
                return $locales->map(function ($groups) {
                    return $groups->pluck('value', 'key');
                });
            })
            ->toArray();
    }

    /**
     * @TODO: Uncomment the following function to endable the cached oriented functionality and commenting the
     * above function after enabling the following function
     *
     */
    /*public function getForExport(?string $tag = null, ?string $locale = null): array
    {
        $cacheKey = "translations.export." . md5(serialize([$tag, $locale]));

        return cache()->remember($cacheKey, now()->addHour(), function() use ($tag, $locale) {
            $query = Translation::query();

            if ($tag) {
                $query->where('tag', $tag);
            }

            if ($locale) {
                $query->where('locale', $locale);
            }

            return $query->get()
                ->groupBy(['locale', 'group'])
                ->map(function ($locales) {
                    return $locales->map(function ($groups) {
                        return $groups->pluck('value', 'key');
                    });
                })
                ->toArray();
        });
    }*/
}
