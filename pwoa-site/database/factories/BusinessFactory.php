<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company;
        $city = City::inRandomOrder()->first();
        $state = State::find($city?->state_id) ?? State::inRandomOrder()->first();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'type' => $this->faker->randomElement(['contractor', 'vendor']),
            'status' => 'approved',
            'description' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'website' => 'https://' . $this->faker->domainName,
            'country_id' => \App\Models\Country::inRandomOrder()->first()?->id,
            'state_id' => $state?->id,
            'city_id' => $city?->id,
            'address' => $this->faker->streetAddress,
            'zip' => $this->faker->postcode,
            'verified_at' => now(),
        ];
    }
}
