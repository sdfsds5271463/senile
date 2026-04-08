<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\TestService;
use Illuminate\Support\Facades\App;
use App\Models\Test;
use Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class TestController extends Controller
{
    // RESTful API 服務
    protected $testService;

    // 透過建構子注入 Service
    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }


    // 視圖測試
    public function test(Request $request, $uri = null)
    {
        $data = [
            't01'=>'this msg is from controller',
            'uri'=>$uri,
        ];
        return view('testView', $data);  //回傳視圖
    }

    public function testInertia(Request $request, $uri = null)
    {
        $data = [
            't01'=>'this msg is from controller',
            'uri'=>$uri,
        ];
        return Inertia::render('testView', $data);  //回傳 Inertia 視圖
    }


    // API測試
    // GET /api/tests (列表)
    public function index()
    {
        return response()->json($this->testService->getAllTests());
    }

    // POST /api/tests (新增)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:10',
            'num' => 'nullable|integer|unique:test,num',
        ]);

        return response()->json($this->testService->createTest($validated), 201);
    }

    // GET /api/tests/{id} (單筆)
    public function show($id)
    {
        return response()->json($this->testService->getTestById($id));
    }

    // PUT /api/tests/{id} (更新)
    public function update(Request $request, $id)
    {
        return response()->json($this->testService->updateTest($id, $request->all()));
    }

    // DELETE /api/tests/{id} (刪除)
    public function destroy($id)
    {
        $this->testService->deleteTest($id);
        return response()->json(['message' => 'Deleted successfully']);
    }



    // 控制器使用服務方法
    public function testMakeService(
        Request $request, 
        TestService $testService1  //引入法1. 依賴注入(最正確做法)
    ){
        $testService2 = App::make(TestService::class); //引入法2. 透過Facades容器外觀解析
            //use Illuminate\Support\Facades\App;  //此方法必須先引入    
        $testService3 = app(TestService::class); //引入法3. 全域函數app解析
            //若 AppServiceProvider 有綁定單例，則引入法 1 2 3 是同一個物件
        $testService4 = new TestService();  //引入法4. 直接實例化

        // 以下為 AppServiceProvider 特殊綁定
        $testService5 = app('TestServiceInstance'::class);  // instance 綁定
        $testService6 = app(\App\Services\TestServiceInterface::class);  // 介面綁定

        //以上 $testService1~6 都可以使用以下方法測試
        print_r($testService6->getTestById(1)->toArray());
    }



    //表單參數測試
    public function testQueryVar(Request $request){
        //表單內容資料
        $request->all();                  //全部資料(含post、get)
        $request->input();                //檔案類型以外全部資料(含post、get)
        $request->query();                //僅get全部資料
        $request->input('name');              //單一名稱資料
        $request->only('name1', 'name2');     //複數名稱資料
        $request->except('name1', 'name2');   //除此之外(複數)名稱資料
        //其他資料
        $request->method();     //方法
        $request->url();        //網址(不含#或get參數)
        $request->fullUrl();    //完整網址(含#或get參數)
        $request->path();       //網址僅路徑(不含網域、#或get參數)

        //session存儲方法
        $request->flash();                    //所有資料存儲
        $request->flashOnly('name1', 'name2');     //(複數)名稱資料存儲
        $request->flashExcept('name1', 'name2');     //除此之外(複數)名稱資料存儲
        //session獲取方法
        $request->old();        //所有資料獲取
        $request->old('name');     //單一資料獲取
            //暫時還不清楚怎麼用，但可以透過印出 $request->session()->all(); 觀察

        print_r( $request->method() );
        print_r( $request->all() );
        exit;
    }


    //存取測試
    public function testAccess(Request $request){
        echo "<pre>";

       //DB Eloquent ORM::  (這邊暫不討論 join 等高級語法)
        //查詢
        Test::query(); //新增的查詢調用入口，可串接任何後續查詢方法
        Test::all();
        Test::find(1);
        Test::where('id','=','1')->get();
        Test::whereRaw("(id > 0) and (id < 2)")->get();
            //可在中間加入   ->whereIn(陣列)
            //可在中間加入   ->whereNotIn(陣列)
            //可在中間加入   ->whereNull(欄)
            //可在中間加入   ->select('a', 'b')  ，將會只取a b兩欄的值
            //可在中間加入   ->distinct()  ，將會抓取不重複的值
            //可在中加加入   ->orderBy('name', 'desc')  ，或是asc進行排序
            //可在中間加入   ->groupBy('name')  ，將會排group
            //可在最後加入   ->take(數量)   ，限制提取之最大數量
            //可在最後加入   ->count()     ，將會回傳資料筆數
            //可在最後加入   ->skip(數量)  ，將會忽略資料
            //可在最後加入   ->first()     ，將回傳第一筆資料
            //可在最後加入    ->lists(V欄,K欄)，將會把K欄當鍵做組合陣列
        print_r( Test::find(1)->toArray() );  //記得取出加上 ->toArray()

        //插入數據
        $test = new Test;
        $test->name = 'bob';
        $test->save(); //儲存插入法
        Test::create(['name'=>'john']); //創造插入法
        $test = Test::firstOrCreate(['name' => 'John']); //創造插入法，若重複則不插入，回傳第一筆發現重複的資料

        //修改數據
        $test->touch(); //僅更新時間戳
        $test->name = 'john2';
        $test->save(); //儲存修改法
        Test::find(1)->update(['name' => 'allen']); //更新修改法 find
        Test::where('name','=','john2')->update(['name' => 'john3']); //更新修改法 where

        //刪除數據
        $test->delete(); //直接刪除
        Test::destroy(2, 3, 4, 5); //刪除id符合資料
        Test::where('name','=','bob')->delete();     //刪除條件數據

        /* 進階:: beginTransaction / lockForUpdate / commit / rollBack
            use Illuminate\Support\Facades\DB;
            ...
            DB::beginTransaction();
            try {
                $stock = Stock::where('id', $stockId)->lockForUpdate()->first();
                $stock->update(['status' => 'purchased','last_trade_at' => now(),]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        */

       //Session
        //use Session;
        //查詢
        Session::all();            //讀取所有值
        Session::get('_token');    //讀取單一值
        session('_token');         //讀取單一值
        Session::has('_token ');   //是否有此鍵

        //修改
        Session::put('name','小芳');    //依鍵的值新增或修改 (值)
        Session::push('stack', '123');  //依鍵創造或新增陣列 (陣列)
        Session::forget('stack');       //依鍵刪除
        $value = Session::pull('name', 'default');  //依鍵刪除，同時取出資料 (若無資料，回傳'default')
        Session::flush();          //清空Session
        Session::regenerate();     //重置整個Session ID (安全性考量)
        Session::save();           //若該程式會die或exit，則要手動儲存

        //非靜態方法
        //上面的 Session:: 可以全部換成 $request->session()->
        print_r( $request->session()->all() );  // 等於印 Session::all()


       //Redis
        //use Illuminate\Support\Facades\Redis;
        // (已從早期要手動安裝的 predis 變成自動解析引入的 phpredis)
        Redis::set('name', 'allen');
        Redis::get('name', 'allen');
        // Redis::connection('cache')->set('key', 'val');  //若有多個 redis server 用這連線

        //非靜態方法
        $Redis = App::make('redis');  //之後上面的 Redis:: 可以全部換成 $Redis->
        $Redis->set('name', 'allen');
        
    
       //Cache
        //use Illuminate\Support\Facades\Cache;
        //use Illuminate\Contracts\Cache\LockTimeoutException;
        //查詢
        Cache::has('key'); //檢查存在
        Cache::get('list'); //取出 (無資料返回 null)

        //創建
        Cache::put('list', [1,2,3], now()->addMinutes(10)); //指定過期時間
        Cache::put('num', 1, 600); //設定600秒後過期
        Cache::forever('name', 'allen'); //永久不過期

        //修改
        Cache::increment('num');  //+1
        Cache::increment('num', 10);  //+n
        Cache::decrement('num');  //-1
        Cache::decrement('num', 5);  //-n

        //刪除
        Cache::forget('num'); //直接刪除
        $value = Cache::pull('list'); //取出後刪除
        Cache::flush(); //全部清空

        //普通原子鎖
        $lock = Cache::lock('update_1', 10);  //鎖10秒
        if ($lock->get()) {  //直接檢查
            $lock->release();  //釋放
            //return response()->json(['message' => '更新成功']);
        } else {
            //return response()->json(['message' => '系統忙碌中'], 423); // 423=請求頻繁
        }

        //等候原子鎖
        $lock = Cache::lock('update_2', 10);  //鎖10秒
        try {
            $lock->block(5);  //最多等5秒
            //return response()->json(['message' => '更新成功']);
        } catch (LockTimeoutException $e) {
            //return response()->json(['message' => '系統忙碌中'], 423); // 423=請求頻繁
        } finally {
            $lock?->release();  //釋放
        }

        //閉包等候原子鎖 (不用手動 release)
        try {
            Cache::lock('update_3', 10)->block(5, function () {  //鎖10秒 等5秒
                //return response()->json(['message' => '更新成功']);
            });
        } catch (LockTimeoutException $e) {
            //return response()->json(['message' => '系統忙碌中'], 423); // 423=請求頻繁
        }

        //非靜態方法
        $Cache = App::make('cache');  //之後上面的 Cache:: 可以全部換成 $Cache->
        $Cache->has('key');
    }

    //測試發送 gemini api
    function geminiapi(Request $request){
        // 準備回應
        $ret = array(
            'status' => 'fail',
            'msg' => 'unknow error',
            'data' => [
                'question' => "",
                'answer' => "",
            ],
        );

        //準備參數
        $token = @env('GEMINI_TOKEN');
        if( $token == ""){
            $ret['msg'] = "token is null";
            return response()->json($ret, 404);
        }
        $model = "gemini-3.1-flash-lite-preview";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        //動態內容
        $question_arr = array("是什麼?", "哪比VM優勢?", "如何實現CI/CD?", "怎麼實現多node?"
            , "如何建構infra?", "如何LB平衡負載?", "的HPA有何用?", "好在哪裡?", "為何複雜度高?");
        $question = $question_arr[mt_rand()%count($question_arr)];
        $question = "請用200字以內，簡述 k8s {$question}";

        //發送請求
        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $token,
                'Accept' => 'application/json',
            ])
            ->timeout(15)
            ->post($url, [
                'contents' => [
                    'parts' => [
                        'text' => $question
                    ]
                ]
            ]);
        } catch (ConnectionException $e) {  //超時
            $ret['msg'] = $e->getMessage();
            return response()->json($ret, 500);
        } catch (RequestException $e) {  // 其他異常
            $ret['msg'] = $e->getMessage();
            return response()->json($ret, 500);
        }

        // 不正常狀態
        if (!$response->successful()) {
            $status = $response->status();
            $ret['msg'] = "http status fail: {$status}";
            return response()->json($ret, 500);
        }

        // 取得回應
        $data = $response->json();
        $answer = @$data['candidates'][0]['content']['parts'][0]['text'];

        // 不正常回應
        if ($answer == "") {
            $ret['msg'] = "gemini answer null";
            return response()->json($ret, 500);
        }
        
        // 渲染結果
        $ret['status'] = 'success';
        $ret['msg'] = '';
        $ret['data']['question'] = $question;
        $ret['data']['answer'] = $answer;

        return response()->json($ret, 200);
    }


}
