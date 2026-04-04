<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Database\Factories\FakeUserFactory;

class FakeUserSeeder extends Seeder
{
    public function run(): void
    {
        // 自訂的 FakeUserFactory
        FakeUserFactory::new()->count(3)->create();
            //從 protected $model 中指定資料表插入

        // 預設的 UserFactory
        User::factory()->count(3)->create();
            //當 Models\User 有方法 HasFactory 時，他就會預設找尋 [模組名]Factory
    }
}
