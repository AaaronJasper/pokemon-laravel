<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbilityRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "ability" => "required|min:2|max:8|unique:abilities,name"
        ];
    }
}
