<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'sender_pokemon_id' => 'required|exists:pokemon,id',
            'receiver_pokemon_id' => 'required|exists:pokemon,id',
        ];
    }
}
