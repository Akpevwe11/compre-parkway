<?php

namespace Tests\Database\Factories;

use Tests\App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'name' => $this->faker->firstName.' '.$this->faker->lastName,
            // 'email' => $this->faker->unique()->safeEmail,
            // 'password' => '$2y$10$VsbcYXhbcun/Dcw81M0m6ePIeix3mXsan24dn5LKu/3yyAilU/wnu',
            // 'phone' => $this->faker->phoneNumber,
            // 'valid_email' => 1,
            // 'email_verified_at' => $this->faker->dateTime(),
        ];
    }

    public function modelName()
    {
        return User::class;
    }

    public function unverified(): self
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}
