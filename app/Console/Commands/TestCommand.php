<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    //php artisan testcommand 123 --isgood
    protected $signature = 'testcommand {num} {--count=10} {--isgood}';
        // num 為必帶； --count=10 為可選值； --isgood 為可選布林
    protected $description = '測試命令';

    public function handle()
    {
        //這邊可以引入 Service，下面僅直接測試功能

        //普通參數
        $arg = $this->argument();  //取得必帶 Array([command] => testcommand  [num] => 123)
        $count = $this->option('count');  //--可選值
        $isgood = $this->option('isgood')?"true":"false";  //--可選布林
        echo "測試命令執行了: {$arg['num']} $count $isgood\n";

        //問答參數
        $symbol = $this->ask('請問你要抓哪支股票？', '2330');
        $type = $this->choice('要抓什麼資料？', ['技術面', '籌碼面'], 0);
        echo "$symbol $type\n";
    }
}
