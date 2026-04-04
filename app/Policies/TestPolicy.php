<?php

namespace App\Policies;
use App\Models\User;

class TestPolicy
{
    public function viewAdminCheck(User $user)
    {
        return $user->name === 'allen';
    }
}
