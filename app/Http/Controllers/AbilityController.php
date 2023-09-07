<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbilityRequest;
use App\Models\Ability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AbilityController extends BaseController
{
    
    /**
     * 新增特性
     */
    public function store(AbilityRequest $request)
    {
        $ability = $request->ability;
        $newAbility = Ability::create([
            "name" => $ability
        ]);
        return $this->res(201, $newAbility, "Created successfully");
    }

    /**
     * 修改特性
     */
    public function update(AbilityRequest $request, string $id)
    {
        $ability= $request->ability;
        $newAbility = Ability::find($id);
        $newAbility->name=$ability;
        $newAbility->save();
        return $this->res(200, $newAbility, "Updated successfully");
    }
}
