<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //使返回數據格式統一，包含狀態碼 數據 訊息
    protected function res($code, $data, $message)
    {
        return [
            "code" => $code,
            "data" => $data,
            "message" => $message
        ];
    }
}
