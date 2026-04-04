<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; //Policy authorize
use App\Policies\TestPolicy; //Policy
use App\Models\User;

class AdminAuthController extends Controller
{
    use AuthorizesRequests; //Policy authorize
    public function login(Request $request)
    {
        // 這裡直接用原本 users 表裡的帳密測試
        $credentials = [ //任何資料 + 密碼驗證都可以
            'name' => 'allen', //正式環境改成 Request 進來的資料
            'password' => 'tt123456',
        ];

        // 關鍵：指定使用 'admin' 守衛進行嘗試
        if (Auth::guard('admin')->attempt($credentials)) {
            // 登入成功，Laravel 會在 Session 裡開一個名為 'admin' 的房間
            return "後台登入成功！目前身分：" . Auth::guard('admin')->user()->name;  //正式環境改成轉跳
        }

        return "後台登入失敗，請確認資料庫是否有這筆資料";
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return "後台已登出";
    }

    public function check()
    {
        //使用者 這邊要抓出來，否則會使用預設的 web 守衛 user
        $user = Auth::guard('admin')->user();

        //[Policy] 驗證
        $this->authorizeForUser($user, 'viewAdminCheck', TestPolicy::class); //必須手動給 user，不能用 authorize
            //Policy authorize 沒過的話會直接 403，後面通通沒得顯示

        //[Gate] 驗證
        if (Gate::forUser( $user )->allows('gate-admin-name', 'allen')) {  //必須手動給 user
            echo "你通過了 Gate 的驗證<br>";
        }

        //[Guard] 登入驗證
        if (Auth::guard('admin')->check()) {
            return "你目前在後台內，身分是：" . Auth::guard('admin')->user()->name;
        }

        return "你不在後台，請先登入";
    }

    public function apiLogin(Request $request)
    {
        // 1. 驗證帳密，這邊正規流程要驗證 $request 參數通過才給 $user
        $user = User::where('name', 'allen')->first();

        // 2. 產出一組 Token 字串
        // 'mytask' 是這組 Token 的名稱，你可以隨便取
        $token = $user->createToken('api-token')->plainTextToken;
        file_put_contents("../api-token.txt", $token); //存起來好測試

        // 3. 回傳給前端
        return response()->json([
            'message' => 'API 登入成功',
            'token' => $token, // 這串字串前端要存起來 (LocalStorage)
        ]);
    }

}