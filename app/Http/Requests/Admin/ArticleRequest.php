<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Article;
use App\Support\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class ArticleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $source = Locale::slugSource($this->input('title'));
        $slug = $this->input('slug');
        if ($source !== '' && (! is_string($slug) || $slug === '')) { $this->merge(['slug' => Str::slug($source)]); }
    }

    public function rules(): array
    {
        $article = $this->route('article');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        return [
            'categoryId' => ['sometimes','nullable',Rule::exists('categories','id')->where(fn ($query) => $query->where('type','article'))],
            'title' => [$required,'array'], 'title.en' => [$required,'string','max:255'], 'title.ar' => ['sometimes','nullable','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255',Rule::unique('articles','slug')->ignore($article instanceof Article ? $article->id : null)],
            'excerpt' => ['sometimes','nullable','array'], 'excerpt.en' => ['sometimes','nullable','string','max:1000'], 'excerpt.ar' => ['sometimes','nullable','string','max:1000'],
            'body' => ['required_if:status,published','nullable','array'], 'body.en' => ['sometimes','nullable','string'], 'body.ar' => ['sometimes','nullable','string'],
            'status' => [$required,'string','in:draft,published,hidden'], 'articleImage' => ['sometimes','nullable','file','mimes:jpg,jpeg,png,webp','max:4096'],
        ];
    }
}
