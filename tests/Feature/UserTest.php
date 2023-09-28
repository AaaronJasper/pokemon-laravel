<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * 測試用戶登入
     */
    public function test_login()
    {
        $response = $this->post('/api/user/login', [
            "email" => "a@a.com",
            "password" => "rickrick",
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試用戶註冊
     */
    public function test_register()
    {
        $response = $this->post('/api/user/register', [
            "name" => "rick",
            "email" => "z@z.com",
            "password" => "rickrick",
            "password_confirmation" => "rickrick",
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試用戶登出
     */
    public function test_logout()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->delete('/api/user/logout');
        $response->assertStatus(200);
    }
    /**
     * 測試再次寄送驗證信
     */
    public function test_send_verify()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/api/send_verify');
        $response->assertStatus(200);
    }
    /**
     * 測試驗證信箱
     */
    public function test_verify_mail()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 創建一個測試用的註冊 token
        $token = Str::random(64);
        // 將測試 token 插入到 'register_token' 資料表中
        DB::table("register_token")->updateOrInsert(
            ['email' => $user->email],
            [
                "token" => $token,
                "created_at" => Carbon::now()
            ]
        );
        // 執行驗證路由
        $response = $this->get('/api/verify/' . $token);
        // 確保用戶已經被標記為已驗證
        $this->assertDatabaseHas('users', [
            'email' => $user->email, // 這裡要和測試 token 中的 email 一致
            'email_verified_at' => now(),
        ]);
        // 確保收到 'Verify success' 的回應
        $response->assertStatus(200);
    }
    /**
     * 測試寄送忘記密碼信
     */
    public function test_forget_password()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $response = $this->post('/api/forget_password', [
            "email" => $user->email
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試重設密碼
     */
    public function test_reset_password()
    {
        // 創建一個用戶並插入到資料庫
        $user = User::factory()->create();
        // 創建一個忘記密碼的 token
        $token = Str::random(64);
        // 插入忘記密碼 token 到 'password_reset_tokens' 資料表
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
        ]);
        // 執行重置密碼路由
        $response = $this->post('/api/reset_password/' . $token, [
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'token' => $token,
        ]);
        // 確保收到 'Update password success' 的回應
        $response->assertStatus(200);
    }
    /**
     * 測試未登入轉址
     */
    public function test_notLogin()
    {
        $response = $this->get('/api/notLogin');
        $response->assertStatus(200);
    }
    /**
     * 測試進入google頁面
     */
    public function test_auth_google()
    {
        // 訪問 googleLogin 路由
        $response = $this->get('/api/auth/google');
        // 斷言應該被重新導向到 Google 登入頁面
        $response->assertRedirect();
        // 斷言被重新導向到 Google 登入 URL
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }
}
