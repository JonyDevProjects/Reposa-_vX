<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_amount' => $this->faker->randomFloat(2, 20, 500),
            'status' => Order::STATUS_PENDING,
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => Order::STATUS_PENDING]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => Order::STATUS_COMPLETED]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => Order::STATUS_CANCELLED]);
    }

    public function refunded(): static
    {
        return $this->state(fn () => ['status' => Order::STATUS_REFUNDED]);
    }

    public function withPaymentIntent(): static
    {
        return $this->state(fn () => [
            'payment_intent_id' => 'pi_' . $this->faker->bothify('##############'),
            'stripe_session_id' => 'cs_test_' . $this->faker->bothify('##############'),
        ]);
    }
}
