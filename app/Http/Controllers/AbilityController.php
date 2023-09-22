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
     * @response {
     * "code": 201,
     * "data": {
     *     "name": "好滑",
     *     "updated_at": "2023-09-20T09:43:34.000000Z",
     *     "created_at": "2023-09-20T09:43:34.000000Z",
     *     "id": 270
     * },
     * "message": "Created successfully"
     * }
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
     * @response{
     * "code": 200,
     * "data": {
     *     "id": 270,
     *     "name": "好滑好嫩",
     *     "created_at": "2023-09-20T09:43:34.000000Z",
     *     "updated_at": "2023-09-20T09:45:19.000000Z"
     * },
     * "message": "Updated successfully"
     * }
     */
    public function update(AbilityRequest $request, string $id)
    {
        $ability= $request->ability;
        $newAbility = Ability::find($id);
        if($newAbility == null){
            return $this->res(404, [], "Ability not exist");
        }
        $newAbility->name=$ability;
        $newAbility->save();
        return $this->res(200, $newAbility, "Updated successfully");
    }
}
