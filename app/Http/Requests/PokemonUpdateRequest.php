<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PokemonUpdateRequest extends BaseRequest
{

    /**
     * 寶可夢更新輸入限制
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "name"=>"min:1|max:8",
            "level"=>"numeric|min:1|max:100",
            "ability" => "exists:abilities,name", // 檢查 ability 是否存在於 abilities 表中的 name 欄位
            "nature" => "exists:natures,name", // 檢查 nature 是否存在於 natures 表中的 name 欄位
        ];
    }
}
