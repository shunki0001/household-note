<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RelationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // TODO: DBのリレーションが正しく動く事をテスト
    // ExpenseがCategoryに紐づいているか
    // Userが削除された時に関連レコードがどうなるか
}
