<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password1234',
                'password_confirmation' => 'new-password1234',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password1234', $user->refresh()->password));
    }

    // 現在のパスワードが間違っていたら更新できない
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password1234',
                'password_confirmation' => 'new-password1234',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect('/profile');
    }

    // バリデーションルールに違反していたらパスワード更新できない
    public function test_cannot_update_violate_roles(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password1234'), // 現在のパスワードを設定
        ]);

        // バリデーション違反パターン
        $invalidPasswords = [
            '1234567' => '数字のみ',
            'password' => '英字のみ',
            'pass12' => '8文字未満',
        ];

        foreach ($invalidPasswords as $invalidPassword => $case) {
            $response = $this
                ->actingAs($user)
                ->from('/profile')
                ->put('/password', [
                    'current_password' => 'password1234', // 正しい現在のパスワード
                    'password' => $invalidPassword,
                    'password_confirmation' => $invalidPassword,
                ]);

            $response
                ->assertSessionHasErrors('password', "失敗ケース: {$case}")
                ->assertRedirect('/profile');
        }

    }
}
