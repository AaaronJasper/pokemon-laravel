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
     */
    public function update(NatureRequest $request, string $id)
    {
        $nature = $request->nature;
        $newNature = Nature::find($id);
        $newNature->name=$nature;
        $newNature->save();
        return $this->res(200, $newNature, "Updated successfully");
    }
}
