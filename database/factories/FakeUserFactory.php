<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // 指定 Model

class FakeUserFactory extends Factory
{
    protected $model = User::class; // 綁定 User Model

    public function definition(): array
    {
        return [
            'name' => "F_" . fake()->name(), // fake() 為全域方法
            'email' => $this->faker->unique()->safeEmail(), // $this->faker 為依賴注入
            'email_verified_at' => now(),
            'password' => Hash::make('tt123456'), // 密碼 tt123456
            'remember_token' => \Str::random(5),
        ];
    }
}
