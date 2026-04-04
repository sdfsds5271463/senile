<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    // 指定資料表名稱（如果你的類別名是 Test，通常 Laravel 會自動對應到 tests)
    protected $table = 'test';

    // 預設主鍵就是 id，且預設就是遞增，所以下面這兩行在 Laravel 中可以省略
    // protected $primaryKey = 'id';
    // public $incrementing = true;

    // 設定可以被「批量寫入」的欄位（這在寫 Controller 時非常重要）
    protected $fillable = [
        'name',
        'phone',
        'num',
        'note',
    ];
}
