<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'amount' => $this->faker->numberBetween(100, 10000),
            'title' => $this->faker->word(),
            'date' => $this->faker->date(),
        ];
    }
}
