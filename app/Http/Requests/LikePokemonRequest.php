<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LikePokemonRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'pokemon_id' => 'required|exists:pokemon,id',
        ];
    }
}
