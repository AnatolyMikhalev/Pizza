<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

//    protected static array $foodNames = [
//        'Pizza', 'Burger', 'Sushi', 'Pasta', 'Salad', 'Taco', 'Burrito', 'Sandwich','Cheese Pizza', 'Hamburger', 'Cheeseburger', 'Bacon Burger', 'Bacon Cheeseburger',
//        'Little Hamburger', 'Little Cheeseburger', 'Little Bacon Burger', 'Little Bacon Cheeseburger',
//        'Veggie Sandwich', 'Cheese Veggie Sandwich', 'Grilled Cheese',
//        'Cheese Dog', 'Bacon Dog', 'Bacon Cheese Dog', 'Pasta',
//    ];
//
//    public function foodName()
//    {
//        return $this->faker->randomElement(static::$foodNames);
//    }

    public static array $typeOfProducts = [
      'Pizza', 'Beverage'
    ];

    public function typeOfProduct()
    {
        return $this->faker->randomElement(self::$typeOfProducts);
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));
        return [
            'name' => $faker->foodName(),
            'type' => $this->typeOfProduct(),
            'price' => $this->faker->randomFloat(2, 100, 1000), // Генерация случайной десятичной цены с двумя знаками после запятой
        ];
    }
}
