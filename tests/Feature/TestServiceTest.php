<?php
namespace Tests\Feature;

use App\Models\Test;
use App\Services\TestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestServiceTest extends TestCase
{
    use RefreshDatabase; // 每次跑測試都會重置資料庫

    protected TestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TestService();
    }

    //以下測試，只會測試 test 開頭函數 或上方有 /** @test */ 的函數 

    /** @test */
    public function test_it_can_create_a_test_record()
    {
        $data = ['name' => 'AllenTest', 'num' => 100];
        $result = $this->service->createTest($data);

        // 斷言：檢查回傳物件
        $this->assertInstanceOf(Test::class, $result);
        $this->assertEquals('AllenTest', $result->name);
        
        // 斷言：檢查資料庫是否真的有這筆記錄
        $this->assertDatabaseHas('test', $data);
    }

    /** @test */
    public function test_it_can_get_test_by_id()
    {
        // 建立假資料
        $test = Test::create(['name' => 'FindMe', 'num' => 50]);
        $found = $this->service->getTestById($test->id);

        $this->assertEquals($test->id, $found->id);
        $this->assertEquals('FindMe', $found->name);
    }

    /** @test */
    public function test_it_can_update_test()
    {
        $test = Test::create(['name' => 'OldName']);
        $updated = $this->service->updateTest($test->id, ['name' => 'NewName']);

        $this->assertEquals('NewName', $updated->name);
        $this->assertDatabaseHas('test', ['id' => $test->id, 'name' => 'NewName']);
    }

    /** @test */
    public function test_it_can_delete_test()
    {
        $test = Test::create(['name' => 'DeleteMe']);
        $this->service->deleteTest($test->id);

        $this->assertDatabaseMissing('test', ['id' => $test->id]);
    }

}