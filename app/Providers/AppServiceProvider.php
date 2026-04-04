<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\TestPolicy; //Policy

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 單例回調函數綁定 (閉包)
        $this->app->singleton(\App\Services\TestService::class, function ($app) {
            return new \App\Services\TestService();
        });

        // 普通回調函數綁定 (通常用於介面綁定)
        $this->app->bind(\App\Services\TestServiceInterface::class, function ($app) {
            return new \App\Services\TestService();
        });

        //實例對象服務綁定 (常用於 Unit Test 時強行替換 mock 類別，與註冊外來既有類別)
        $this->app->instance('TestServiceInstance', new \App\Services\TestService() ); 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //[Gate] 上帝權限 (符合的使用者跳過所有 Gate)
        Gate::before(function (User $user) {
            if ($user->name === 'super_allen_ex') { return true; }
            return null; 
        });

        //[Gate] 標準 Gate 定義(匿名函數)
        Gate::define('gate-admin-name', function (User $user, String $name = 'allen') {  //必定要攜帶 user 參數
            return $user->name === $name;
        });

        //[Policy] 標準 policy 註冊
        Gate::policy(TestPolicy::class, TestPolicy::class);

        //[Policy+Gate] 標準 Gate 定義(類別方法 並使用policy類別)
        Gate::define('gate-policy-vac', [TestPolicy::class, 'viewAdminCheck']);  //也可引入類別方法

        
        Vite::prefetch(concurrency: 3);
    }
}
