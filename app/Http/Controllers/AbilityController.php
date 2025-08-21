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
     * 
     * Allows an authenticated user to create a new ability for Pokémon.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @bodyParam ability string required Example: creative
     * 
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
     * Update an ability
     * 
     * Allows an authenticated user to update the name of an existing Pokémon ability.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @urlParam id int required The ID of the ability. Example: 1
     * @bodyParam ability string required Example: patient
     * 
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
