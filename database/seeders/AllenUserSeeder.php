<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AllenUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(  // firstOrCreate 也可以
        ['email' => 'sdfsds5271463@gmail.com'], // 條件
        [
            'name' => 'allen',
            'password' => Hash::make('tt123456'), //bcrypt 
        ]);
    }
}
