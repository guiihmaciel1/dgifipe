<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManualListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model' => ['required', 'string', Rule::in(array_keys(config('dgifipe.models')))],
            'storage' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:100', 'max:20000'],
            'city' => ['required', 'string', Rule::in(config('dgifipe.cities'))],
            'title' => ['nullable', 'string', 'max:255'],
            'screenshot' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
