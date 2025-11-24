<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Category;
use App\Models\IncomeCategory;
use App\Models\Expense;
use App\Models\Income;

trait CreateUsers
{
    /**
    * ユーザーを作成してログインする
    */
    protected function createLoginUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    /**
    * ユーザー A/B 作成 Aログイン
    */
    protected function createLoginUsers()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->actingAs($userA);

        return compact('userA', 'userB');
    }


}

