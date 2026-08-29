<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected static array $names = [
        'Cervical' => 'Cervical',
        'Anti-ronquidos' => 'Anti-snoring',
        'Viscoelástica' => 'Viscoelastic',
        'Látex' => 'Latex',
        'Espuma con memoria' => 'Memory Foam',
        'Térmica' => 'Thermal',
        'Viaje' => 'Travel',
        'Infantil' => 'Kids',
    ];

    public function definition(): array
    {
        $nameEs = $this->faker->unique()->randomElement(array_keys(self::$names));
        $nameEn = self::$names[$nameEs];

        return [
            'name' => $nameEs,
            'slug' => \Illuminate\Support\Str::slug($nameEs),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            $nameEs = is_array($category->name) ? reset($category->name) : $category->name;
            $nameEn = self::$names[$nameEs] ?? $nameEs;

            $category->setTranslation('name', 'es', $nameEs);
            $category->setTranslation('name', 'en', $nameEn);
            $category->save();
        });
    }
}
