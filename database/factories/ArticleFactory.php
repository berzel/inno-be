<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(mt_rand(5, 12));
        $slug = Str::slug($title) . '-' . bin2hex(random_bytes(4));

        return [
            'title' => $title,
            'slug' => $slug,
        ];
    }
}
