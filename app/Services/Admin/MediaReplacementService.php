<?php

declare(strict_types=1);

namespace App\Services\Admin;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class MediaReplacementService
{
    public function replaceSingleFile(HasMedia $model, UploadedFile $file, string $collection): Media
    {
        $model->clearMediaCollection($collection);

        return $model
            ->addMedia($file)
            ->toMediaCollection($collection);
    }

    public function removeCollection(HasMedia $model, string $collection): void
    {
        $model->clearMediaCollection($collection);
    }
}
