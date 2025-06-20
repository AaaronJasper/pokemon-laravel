<?php

namespace App\Http\Controllers;

use App\Http\Requests\NatureRequest;
use App\Models\Nature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NatureController extends BaseController
{

    /**
     * Create a nature
     * @response{
     * "code": 201,
     * "data": {
     *     "name": "creative",
     *     "updated_at": "2023-09-20T09:46:22.000000Z",
     *     "created_at": "2023-09-20T09:46:22.000000Z",
     *     "id": 28
     * },
     * "message": "Created successfully"
     * }
     */
    public function store(NatureRequest $request)
    {
        $nature = $request->nature;
        $newNature = Nature::create([
            "name" => $nature
        ]);
        return $this->res(201, $newNature, "Created successfully");
    }

    /**
     * Update a nature
     * @response{
     * "code": 200,
     * "data": {
     *     "id": 28,
     *     "name": "creative",
     *     "created_at": "2023-09-20T09:46:22.000000Z",
     *     "updated_at": "2023-09-20T09:47:23.000000Z"
     * },
     * "message": "Updated successfully"
     * }
     */
    public function update(NatureRequest $request, string $id)
    {
        $nature = $request->nature;
        $newNature = Nature::find($id);
        if($newNature == null){
            return $this->res(404, [], "Nature not exist");
        }
        $newNature->name=$nature;
        $newNature->save();
        return $this->res(200, $newNature, "Updated successfully");
    }
}
