<?php

declare(strict_types=1);

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Model;

final class PublishStatusService
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function applyPublishTimestamp(Model $model, array $attributes): array
    {
        if (($attributes['status'] ?? null) !== 'published') {
            return $attributes;
        }

        if ($model->exists && $model->getAttribute('published_at') !== null) {
            return $attributes;
        }

        $attributes['published_at'] = now();

        return $attributes;
    }
}
