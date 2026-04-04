<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->index();
            $table->string('phone', 10)->nullable()->default(null);
            $table->unsignedInteger('num')->nullable()->default(null)->unique()->comment('staff id');
            $table->text('note')->nullable();
            $table->timestamps();  // 如果你需要 Laravel 自動紀錄建立與更新時間，可以加上
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test');
    }
};
