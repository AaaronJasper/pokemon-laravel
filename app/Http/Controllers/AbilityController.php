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
     * Create an ability
     * @response {
     * "code": 201,
     * "data": {
     *     "name": "patient",
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
     * update an ability
     * @response{
     * "code": 200,
     * "data": {
     *     "id": 270,
     *     "name": "patient",
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
