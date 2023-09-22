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
     * 新增性格
     * @response{
     * "code": 201,
     * "data": {
     *     "name": "好滑",
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
     * 修改性格
     * @response{
     * "code": 200,
     * "data": {
     *     "id": 28,
     *     "name": "好滑好嫩",
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
