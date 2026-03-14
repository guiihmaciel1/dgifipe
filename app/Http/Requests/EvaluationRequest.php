<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvaluationRequest extends FormRequest
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
            'battery_health' => ['required', 'integer', 'min:0', 'max:100'],
            'conditions' => ['nullable', 'array'],
            'conditions.*' => ['string', Rule::in(['no_box', 'no_cable', 'screen_replaced', 'face_id_issue'])],
        ];
    }
}
