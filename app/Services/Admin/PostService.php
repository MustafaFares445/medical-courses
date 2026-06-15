<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Data\Admin\PostData;
use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PostService
{
    public function __construct(
        private readonly PublishStatusService $publishStatusService,
        private readonly MediaReplacementService $mediaReplacementService,
    ) {}

    public function create(PostData $data, ?UploadedFile $image = null): Article
    {
        return DB::transaction(function () use ($data, $image): Article {
            $article = new Article();
            $attributes = $this->publishStatusService->applyPublishTimestamp($article, $this->attributes($data));
            $article->forceFill($attributes)->save();

            if ($image instanceof UploadedFile) {
                $this->mediaReplacementService->replaceSingleFile($article, $image, 'article-image');
            }

            return $article->refresh();
        });
    }

    public function update(Article $article, PostData $data, ?UploadedFile $image = null): Article
    {
        return DB::transaction(function () use ($article, $data, $image): Article {
            $attributes = $this->publishStatusService->applyPublishTimestamp($article, $this->attributes($data, $article));
            $article->forceFill($attributes)->save();

            if ($image instanceof UploadedFile) {
                $this->mediaReplacementService->replaceSingleFile($article, $image, 'article-image');
            }

            return $article->refresh();
        });
    }

    public function delete(Article $article): void
    {
        DB::transaction(fn (): ?bool => $article->delete());
    }

    /** @return array<string, mixed> */
    private function attributes(PostData $data, ?Article $article = null): array
    {
        $attributes = [];

        if ($this->hasField($data, 'categoryId')) {
            $attributes['category_id'] = $data->categoryId;
        }
        if ($this->hasField($data, 'title')) {
            $attributes['title'] = $data->title;
        }
        if ($this->hasField($data, 'slug')) {
            $attributes['slug'] = $data->slug !== null && $data->slug !== '' ? Str::slug($data->slug) : Str::slug((string) $data->title);
        } elseif (! $article instanceof Article && $data->title !== null) {
            $attributes['slug'] = Str::slug($data->title);
        }
        if ($this->hasField($data, 'excerpt')) {
            $attributes['excerpt'] = $data->excerpt;
        }
        if ($this->hasField($data, 'body')) {
            $attributes['body'] = $data->body;
        }
        if ($this->hasField($data, 'status')) {
            $attributes['status'] = $data->status;
        }

        return $attributes;
    }

    private function hasField(PostData $data, string $field): bool
    {
        return in_array($field, $data->fields, true);
    }
}
