<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class FeatureBaseTestCase extends TestCase
{
        /**
         * （trait）
         * テスト終了時にテーブルとレコードを削除する
         */
        // use DatabaseMigrations;
    
        public function setUp(): void
        {
            parent::setUp();
            
            // Artisan::call('migrate:fresh --seed');
            Artisan::call('migrate:fresh');
        }
}
