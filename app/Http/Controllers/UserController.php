<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //獲取搜尋的參數
        $name = $request->input('name');
        $email = $request->input('email');
        //執行查詢
        $users = User::when($name, function ($query, $name) {
            $query->where('name', 'like', "%$name%");
        })
            ->when($email, function ($query, $email) {
                $query->where('email', $email);
            })->get();

        return $this->same([$users], "查詢成功");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return $this->same([$user], "查詢成功");
    }
}
