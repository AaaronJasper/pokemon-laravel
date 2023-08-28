<?php

namespace App\Console\Commands;

use App\Models\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportAbility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ability {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import JSON data from a ability text file';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filename = $this->argument('filename');

        $filePath = storage_path('app/' . $filename);

        // 檢查文件是否存在
        if (!File::exists($filePath)) {
            $this->error('File not found: ' . $filename);
            return;
        }

        // 讀取文本文件內容
        $fileContents = File::get($filePath);

        // 解析 JSON 數據
        $jsonData = json_decode($fileContents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to parse JSON data.');
            return;
        }

        // 檢查資料庫是否已存在相同的數據
        if (Ability::find(1)!=null) {
            $this->error('Data already exists in the database. Command skipped.');
            return;
        }

        //執行邏輯
        $abilities = $jsonData['data']['pokemon_v2_ability'];
        foreach ($abilities as $abilityData) {
            $abilityName = $abilityData['pokemon_v2_abilitynames'][0]['name'];
        
            // 創建 Ability 模型的新實例
            $ability = new Ability();
            $ability->name = $abilityName;
        
            // 將數據存入資料庫
            $ability->save();
        }
        $this->info('JSON data from ' . $filename . ' imported successfully.');

    }
}