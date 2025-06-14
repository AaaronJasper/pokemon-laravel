<?php

namespace App\Http\Controllers;

use App\Events\UserRegister;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendVerificationEmailJob;

class UserController extends BaseController
{
    /**
     * Register a new user
     * @response{
     * "code": 201,
     * "data": "5|DZTNk2mBYpPkew2cxuHJFYmmOXF5nE7yjNyIfjKL",
     * "message": "Register success"
     * }
     */
    public function register(RegisterRequest $request)
    {
        //創建用戶
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => password_hash($request->input("password"), PASSWORD_DEFAULT),
            "created_at" => Carbon::now(),
        ]);
        //生成登入token
        $token = $user->createToken("myapptoken")->plainTextToken;
        //發送驗證信
        //event(new UserRegister($request->input("email")));
        // use queue and jobs instead
        SendVerificationEmailJob::dispatch($request->input("email"));
        //回傳資料
        //$data = new UserResource($user);
        //$data[] = ["token" => $token];
        return $this->res(201, ['id' => $user->id, 'user' => $user->name, 'token' => $token], "Register success");
    }
    /**
     * Login 
     * @response{
     * "code": 200,
     * "data": "2|JgAcW87DjmPYIX2uyFhdATblRGWYnqODmEGLGe5q",
     * "message": "Login success"
     * }
     */
    public function login(LoginRequest $request)
    {
        //找用戶郵箱
        $user = User::where("email", $request->email)->first();
        //確認是否是黑單
        //if($user->is_locked==1){
        //$data = new UserResource($user);
        //return $this->re_response($data, "Login failed", 401);
        //}
        //確認密碼
        if (password_verify($request->password, $user->password)) {
            //建立登入token
            $token = $user->createToken("myapptoken")->plainTextToken;
            return $this->res(200, ['id' => $user->id, 'user' => $user->name, 'token' => $token, 'isVerify' => $user->email_verified_at !== null], "Login success");
        } else {
            $data = new UserResource($user);
            return $this->res(401, $data, "Login failed");
        }
    }
    /**
     * Logout
     * @response{
     * "code": 200,
     * "data": {
     *     "name": "rick",
     *     "email": "a@a.com"
     * },
     * "message": "Logout success"
     * }
     */
    public function logout()
    {
        $userId = Auth::id();
        // 根據 ID 取得使用者
        $user = User::find($userId);
        $data = new UserResource($user);
        // 刪除特定的 token
        $user->tokens()->delete();
        return $this->res(200, $data, "Logout success");
    }
    /**
     * Get user information
     */
    public function exchange_token(Request $request)
    {
        $code = $request->input('code');

        $userId = Cache::pull("oauth_code:{$code}"); // 用 pull 取完就刪
        if (!$userId) {
            return response()->json(['message' => 'Invalid or expired code'], 400);
        }
        $user = User::find($userId);
        $token = $user->createToken("myapptoken")->plainTextToken;

        return $this->res(200, ['id' => $user->id, 'user' => $user->name, 'token' => $token, 'isVerify' => $user->email_verified_at !== null], "Login success");
    }
    /**
     * Resend verification email
     * @response{
     * "code": 200,
     * "data": {
     *     "name": "rick",
     *     "email": "b@b.com"
     * },
     * "message": "Email send successfully"
     * }
     */
    public function send_verify()
    {
        $user = User::find(Auth::id());
        $data = new UserResource($user);
        if ($user->email_verified_at != null) {
            return $this->res(200, $data, "Already verify email");
        }
        //event(new UserRegister($user->email));
        // use queue and jobs instead
        SendVerificationEmailJob::dispatch($user->email);
        return $this->res(200, $data, "Email send successfully");
    }
    /**
     * Execute verification
     * @response{
     * "code": 200,
     * "data": [],
     * "message": "Verify success"
     * }
     */
    public function verify($token)
    {
        $registerUser = DB::table("register_token")->where([
            "token" => $token,
        ])->first();
        if (!$registerUser) {
            return $this->re_response([], "Already verify", 200);
        }
        //用戶加上驗證時間
        $select_user = User::where("email", $registerUser->email)->first();
        $select_user->email_verified_at = Carbon::now();
        $select_user->save();
        //刪除token
        DB::table("register_token")->where("token", $token)->delete();
        return $this->res(200, [], "Verify success");
    }
    /**
     * Send password reset email 
     * @response{
     * "code": 200,
     * "data": [],
     * "message": "Email send successfully"
     * }
     */
    public function forget_password(Request $request)
    {
        //驗證email
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);
        //使用輔助函數生成token
        $token = Str::random(64);
        //寫入資料庫
        DB::table("password_reset_tokens")->updateOrInsert(
            ['email' => $request->email],
            [
                "token" => $token,
                "created_at" => Carbon::now()
            ]
        );
        //寄送email
        Mail::to($request->email)->send(new ForgetPasswordMail($token));
        return $this->res(200, [], "Email send successfully");
    }
    /**
     * Reset password
     * @response{
     * "code": 200,
     * "data": [],
     * "message": "Update password success"
     * }
     */
    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:4|max:16|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        ]);
        //確認是否有此帳號及token(原本在前端hidden傳入token)
        $updateUser = DB::table("password_reset_tokens")->where([
            "email" => $request->email,
            "token" => $request->token,
        ])->first();
        if (!$updateUser) {
            return $this->res(401, [], "Login failed");
        }
        //更新用戶數據
        User::where('email', $request->email)->update(["password" => password_hash($request->input("password"), PASSWORD_DEFAULT)]);
        //刪除忘記密碼中的token
        DB::table("password_reset_tokens")->where("email", $request->email)->delete();
        return $this->res(200, [], "Update password success");
    }
    /**
     * Unauthenticated (handled by middleware redirect)
     * @response{
     * "code": 401,
     * "data": [],
     * "message": "Not logged in"
     * }
     */
    public function notLogin()
    {
        return $this->res(401, [], "Not logged in");
    }
}
