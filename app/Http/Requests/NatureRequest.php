<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NatureRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "nature" => "required|min:2|max:8|unique:natures,name"
        ];
    }
}
