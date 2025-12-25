<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a unique English name first to ensure the slug is unique
        $nameEn = $this->faker->unique()->words(rand(1, 2), true);
        
        return [
            // 1. JSON Translatable Fields
            'name' => [
                'en' => ucfirst($nameEn),
                'de' => ucfirst($nameEn) . ' (DE)', // Simulation of German translation
            ],
            'description' => [
                'en' => $this->faker->sentence(10),
                'de' => $this->faker->sentence(10) . ' (DE)',
            ],

            // 2. Standard Fields
            'slug' => Str::slug($nameEn), // Generate slug from the English name
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'sort_order' => $this->faker->numberBetween(0, 50),
            'type' => 'default',
            'icon' => 'fa-solid fa-tag', // Placeholder class for frontend icons
            
            // 3. Meta & Relationships
            'meta' => [
                'color' => $this->faker->hexColor(),
                'keywords' => $this->faker->words(3),
            ],
            'parent_id' => null, // Default to Root category (no parent)
        ];
    }

    /**
     * State to create a child category attached to an existing or new parent.
     */
    public function child(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            ];
        });
    }

    /**
     * State to create an inactive category.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}