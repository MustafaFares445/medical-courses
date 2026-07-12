<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lesson;
use Illuminate\Support\Carbon;
use RuntimeException;
use Throwable;

final class LessonVideoAccessService
{
    public function temporaryUrlFor(Lesson $lesson): ?string
    {
        $media = $lesson->getFirstMedia('lesson-video');

        if ($media === null) {
            return null;
        }

        try {
            return $media->getTemporaryUrl(Carbon::now()->addMinutes(15));
        } catch (Throwable) {
            throw new RuntimeException('Lesson video is not available.');
        }
    }
}
