<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => 'default',
            "email" => 'default@default.com',
            "password" => password_hash('default', PASSWORD_DEFAULT),
            "created_at" => Carbon::now(),
        ]);
    }
}
