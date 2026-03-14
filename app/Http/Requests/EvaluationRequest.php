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
            'device_state' => ['required', 'string', Rule::in(['original', 'repaired'])],
            'no_box' => ['boolean'],
            'no_cable' => ['boolean'],
        ];
    }
}
