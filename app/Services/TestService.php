<?php
namespace App\Services;

use App\Models\Test;

class TestService
{
    public function getAllTests() {
        return Test::all();
    }

    public function createTest(array $data) {
        return Test::create($data);
    }

    public function getTestById($id) {
        return Test::findOrFail($id);
    }

    public function updateTest($id, array $data) {
        $test = Test::findOrFail($id);
        $test->update($data);
        return $test;
    }

    public function deleteTest($id) {
        return Test::destroy($id);
    }
}