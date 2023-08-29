<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PokemonReuqest extends BaseRequest
{

    /**
     * 寶可夢新增輸入限制
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "name"=>"required|min:1|max:8",
            "level"=>"required|numeric|min:1|max:100",
            "ability" => "required|exists:abilities,name", // 檢查 ability 是否存在於 abilities 表中的 name 欄位
            "nature" => "required|exists:natures,name", // 檢查 nature 是否存在於 natures 表中的 name 欄位
        ];
    }
}
