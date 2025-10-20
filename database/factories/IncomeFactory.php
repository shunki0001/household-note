<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Income>
 */
class IncomeFactory extends Factory
{
    protected $model = Income::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'income_category_id' => IncomeCategory::factory(),
            'amount' => $this->faker->numberBetween(100, 10000),
            'income_date' => $this->faker->date(),
        ];
    }
}
