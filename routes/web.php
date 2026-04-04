<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminAuthController;

//傳統方法呼叫視圖
use App\Http\Controllers\TestController;

// 雙方法 / 呼叫view   (http://127.0.0.1/test/123)
Route::match(['get','post'], '/test/{uri?}', function ($uri=null) {
    return view('testView', [
        't01' => 'this msg is from router',
        'uri' => $uri,
    ]);
})->where(['uri' => '[0-9]+']); //指派正規則

// 群組middleware / 呼叫controller   (http://127.0.0.1/test2/5566)
Route::middleware(['test'])->group(function () {
    Route::get('/test2/{uri?}', [TestController::class, 'test'])
        ->where(['uri' => '[0-9]+']);
});

// 個體middleware / 呼叫controller   (http://127.0.0.1/test3/5566)
Route::get('/test3/{uri?}', [TestController::class, 'test'])
    ->middleware('test')
    ->where(['uri' => '[0-9]+']);


// 直接轉址
Route::redirect('/redirect1', '/');  //預設 302 暫時轉址
Route::permanentRedirect('/redirect2', '/');  //永久轉址（對 SEO 友善，回傳 301）
Route::redirect('/redirect3', '/', 301);  // 手動指定狀態碼
// 函數轉址
Route::get('/redirect4', function(){
    return redirect('/');  //return 轉址路徑
    //return redirect()->route('dashboard');  //也可以轉跳到具名路由
});


//漸進式方法 Inertia 呼叫視圖
Route::get('/test4/{uri?}', function ($uri=null) {
    return Inertia::render('testView', [
        't01' => 'this msg is from router (via Inertia)',
        'uri' => $uri,
    ]);
});

Route::get('/test5/{uri?}', [TestController::class, 'testInertia']);


//使用服務測試
Route::get('/testMakeService', [TestController::class, 'testMakeService']);
Route::get('/testMakeService2', function(){
    $testService = App::make(App\Services\TestService::class); //路由使用(不建議)
    print_r($testService->getTestById(1)->toArray());
    exit;
});


//表單更新測試
Route::get('/testEditPhone', function () {
    return Inertia::render('testEditPhone');
})->name('testEditPhone');

//控制器接收資料測試
Route::get('/testQueryVar', [TestController::class, 'testQueryVar']);


//存取測試
Route::get('/testAccess', [TestController::class, 'testAccess']);


// 自訂 Guard
//use App\Http\Controllers\AdminAuthController;
Route::get('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/admin/logout', [AdminAuthController::class, 'logout']);
Route::get('/admin/check', [AdminAuthController::class, 'check']);
Route::get('/admin/dashboard', function () {
    echo "登入囉";
})->middleware(  
    'auth:admin',  //Guard， 路由gate的user將依據 auth:admin 自動注入 (無法指定)
    'can:gate-admin-name',  //Gate
    'can:gate-admin-name,"allen"',  //Gate 攜帶參數
    'can:gate-policy-vac',  //Policy => Gate類別方法引入
);  


// 在 routes/web.php 隨手寫一個測試路徑
Route::get('/testMail', function () {
    return new App\Mail\TestReport();
});







Route::get('/', function () {
    return Inertia::render('Welcome', [   // resources/js/Pages/Welcome.vue
        'canLogin' => Route::has('login'),  // 只要有名為 login 的路由，就回傳 true
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});







Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';  // auth 引入 login / register 等路由
