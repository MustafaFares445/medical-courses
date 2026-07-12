<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LessonVideoController extends Controller
{
    public function show(Request $request, Lesson $lesson): StreamedResponse
    {
        $media = $lesson->getFirstMedia('lesson-video');

        abort_if($media === null, 404);

        return $media->toInlineResponse($request);
    }
}
