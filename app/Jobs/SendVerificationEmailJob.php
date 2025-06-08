<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\UserRegisterMail;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    /**
     * Create a new job instance.
     */
    public function __construct($email)
    {
    /**
     * Create a new event instance.
     */

        $this->email = $email;
    
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //使用輔助函數生成token
        $token = Str::random(64);
        //寫入資料庫
        DB::table("register_token")->updateOrInsert(
            ['email' => $this->email],
            [
                "token" => $token,
                "created_at" => Carbon::now()
            ]
        );
        Mail::to($this->email)->send(new UserRegisterMail($token));
    }
}
