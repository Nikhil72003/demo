<?php

namespace Database\Factories\Shop;

use App\Enums\DiscountType;
use App\Models\Shop\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Coupon::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(DiscountType::cases());

        return [
            'code' => strtoupper(fake()->unique()->bothify('??##??##')),
            'description' => fake()->sentence(),
            'discount_type' => $type,
            'discount_value' => $type === DiscountType::Percentage
                ? fake()->randomFloat(2, 5, 50)
                : fake()->randomFloat(2, 5, 100),
            'minimum_order_value' => fake()->optional(0.6)->randomFloat(2, 20, 200),
            'maximum_discount' => $type === DiscountType::Percentage ? fake()->optional(0.5)->randomFloat(2, 20, 100) : null,
            'usage_limit' => fake()->optional(0.7)->numberBetween(10, 500),
            'usage_count' => fake()->numberBetween(0, 50),
            'is_active' => fake()->boolean(80),
            'starts_at' => fake()->optional(0.5)->dateTimeBetween('-3 months', 'now'),
            'expires_at' => fake()->optional(0.7)->dateTimeBetween('now', '+6 months'),
        ];
    }
}
