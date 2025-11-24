<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Category;
use App\Models\IncomeCategory;
use App\Models\Expense;
use App\Models\Income;

trait InteractsWithInertia
{

    // Inertiaの共通アサート

    // API呼び出し + ステータス確認
    protected function getAndAsset(string $url)
    {
        $response = $this->get($url);
        $response->assertStatus(200);
        return $response;
    }
}
