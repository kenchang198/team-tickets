<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('exists_with_type', function ($attribute, $value, $parameters, $validator) {

            $table = $parameters[0];
            $column = $parameters[1];

            // 型チェックを含めてデータベース内での存在を確認
            $result = DB::table($table)
                        ->whereRaw("BINARY {$column} = ?", [$value])
                        ->exists();
            return $result;
        });
    }
}
