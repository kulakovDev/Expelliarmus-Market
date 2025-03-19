<?php

namespace Modules\ContentManagement\Http\Requests\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ContentManagement\Rules\OnlySpecificDomainRule;
use Modules\ContentManagement\Rules\OnlySpecificStorageUrlRule;
use Modules\ContentManagement\Rules\SlidesExistsRule;

class UploadSliderContentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', new SlidesExistsRule()],
            'images.*.order' => ['required', 'integer'],
            'images.*.image' => ['nullable', 'image', 'required_without:images.*.image_url'],
            'images.*.image_url' => ['nullable', 'url', new OnlySpecificStorageUrlRule(url('/storage/content/slider'))],
            'images.*.content_url' => ['required', 'url', new OnlySpecificDomainRule(config('app.frontend_name'))]
        ];
    }

    public function attributes(): array
    {
        return [
            'image.*.order' => 'image order',
            'images.*.image' => 'image',
            'images.*.image_url' => 'image url',
            'images.*.content_url' => 'content url'
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Images is required',
            'images.*.order.required' => 'In each image set, order is required',
            'images.*.image.required' => 'In each image set, image is required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
