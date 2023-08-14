<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class TestController extends BaseController
{
    //測試緩存
    public function index(Request $request)
    {
        //檢查緩存
        $value = Cache::get("key");
        if ($value != null) {
            return "已有緩存";
        }
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

        Cache::put('key', $users, $seconds = 30);
        return $this->same([$users], "查詢成功");
    }
}
