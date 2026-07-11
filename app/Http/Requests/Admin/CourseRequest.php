<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Course;
use App\Support\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class CourseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $title = $this->input('title');
        $slug = $this->input('slug');
        $source = Locale::slugSource($title);
        if ($source !== '' && (! is_string($slug) || $slug === '')) {
            $this->merge(['slug' => Str::slug($source)]);
        }
        $currency = $this->input('currency');
        if (is_string($currency)) { $this->merge(['currency' => strtoupper($currency)]); }
    }

    public function rules(): array
    {
        $course = $this->route('course');
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        $platformCurrency = strtoupper((string) config('services.stripe.currency', 'usd'));

        return [
            'categoryId' => ['sometimes','nullable',Rule::exists('categories','id')->where(fn ($query) => $query->where('type','course'))],
            'title' => [$required,'array'], 'title.en' => [$required,'string','max:255'], 'title.ar' => ['sometimes','nullable','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255',Rule::unique('courses','slug')->ignore($course instanceof Course ? $course->id : null)],
            'shortDescription' => ['sometimes','nullable','array'], 'shortDescription.en' => ['sometimes','nullable','string','max:1000'], 'shortDescription.ar' => ['sometimes','nullable','string','max:1000'],
            'description' => ['sometimes','nullable','array'], 'description.en' => ['sometimes','nullable','string'], 'description.ar' => ['sometimes','nullable','string'],
            'price' => [$required,'numeric','min:0'], 'currency' => [$required,'string','size:3',Rule::in([$platformCurrency])], 'status' => [$required,'string','in:draft,published,hidden'],
            'thumbnail' => ['sometimes','nullable','file','mimes:jpg,jpeg,png,webp','max:4096'],
        ];
    }
}
