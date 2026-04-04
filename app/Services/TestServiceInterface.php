<?php
namespace App\Services;

interface TestServiceInterface
{
    // 規定所有實作這個介面的類別，都必須有 getTestById 這個方法
    public function getTestById(): null;
}