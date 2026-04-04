<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\TestAlert;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


//php artisan testcommand_direct 123
//command 直接執行 
Artisan::command('testcommand_direct {num}', function () {
    $arg = $this->argument();
    echo "一個 direct 命令被執行 {$arg['num']}";
    \Log::info("一個 direct 命令被執行 {$arg['num']}");
})->purpose('');


//Schedule 匿名函數
Schedule::call(function () {
    \Log::info('排程正在運行中');
})->everyTwoMinutes();

//Schedule 呼叫命令
Schedule::command('testcommand_direct')->dailyAt('09:00');
/*
    ->everyMinute(); 每分鐘
    ->everyFiveMinutes();
    ->hourly(); 每小時
    ->daily(); 每天凌晨 00:00
    ->dailyAt('09:00'); 每天時段
    ->at('13:00'); 指定時間
    ->twiceDaily(1, 13); 每天兩次 (1點與13點)
    ->mondays(); 每週
    ->weekdays(); 僅周間
*/

//php artisan testevent
//command 測試事件與監聽
Artisan::command('testevent', function () {
    echo "觸發一個事件，去看 laravel.log 檢查監聽";
    App\Events\TestEvent::dispatch(123);
})->purpose('');

 
//php artisan testjob
//command 測試工作佇列
Artisan::command('testjob', function () {
    echo "觸發工作佇列，去看 laravel.log 做得怎樣";
    App\Jobs\TestJob::dispatch();  //直接跑
    App\Jobs\TestJob::dispatch()->delay(now()->addMinutes(10));  //10分鐘後再跑
})->purpose('');


//php artisan testlog
//command 測試log
Artisan::command('testlog', function () {
    echo "觸發測試log，去看所有的log吧";
    //從設定檔中 log
    \Log::info('測試log>這行進laravel.log');
    \Log::channel('test_log')->info('測試log>這行進test.log');
    \Log::channel('test_log')->info('測試log>第二參數將自動json', ["allen"=>123, "disney"=>456]);
    //自訂臨時 log
    $myLogger = new Logger('test2_log');
    $myLogger->pushHandler(new StreamHandler(storage_path('logs/test2.log'), Logger::DEBUG));
    $myLogger->info('測試log>這行進test2.log');
})->purpose('');


//php artisan testmail
//command 測試email
Artisan::command('testmail', function () {
    echo "觸發測試mail，去收信看看";
    Mail::to('sdfsds5271463@gmail.com')->send(new App\Mail\TestReport());
})->purpose('');


//php artisan testnotification
//command 測試notification
Artisan::command('testnotification', function () {
    echo "觸發測試testnotification，去收信 + DB看看";
    //單人發送
    User::where('email','=','sdfsds5271463@gmail.com')->first()->notify(new TestAlert('測試訊息(單人發送)'));
    //批次發送
    $users = User::where('email','=','sdfsds5271463@gmail.com')->get();
    Notification::send($users, new TestAlert('測試訊息(批次發送)'));
})->purpose('');

//php artisan testnotification2
//command 測試testnotification2 取得會員未讀的信
Artisan::command('testnotification2', function () {
    echo "觸發測試testnotification 當前會員未讀的信件";
    $user = User::where('email','=','sdfsds5271463@gmail.com')->first();
    //$user->unreadNotifications->markAsRead(); //將所有信件標記為已讀(具有 read_at 欄位)
    print_r($user->unreadNotifications->toArray()); //印出所有未讀信件
})->purpose('');