<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected static $PizzaNames = [
        'Cheese Pizza', 'Pepperoni Pizza', 'Margherita Pizza', 'BBQ Chicken Pizza', 'Hawaiian Pizza',
        'Meat Lovers Pizza', 'Veggie Pizza', 'Buffalo Chicken Pizza', 'Supreme Pizza', 'Mushroom Pizza',
        'Sausage Pizza', 'Bacon Pizza', 'Four Cheese Pizza', 'Garlic Chicken Pizza', 'Spinach Alfredo Pizza',
        'Philly Cheese Steak Pizza', 'Taco Pizza', 'Breakfast Pizza', 'Pesto Chicken Pizza', 'Tomato Basil Pizza',
        'Shrimp Scampi Pizza', 'Greek Pizza', 'Prosciutto Pizza', 'Caprese Pizza', 'Mexican Pizza',
        'Italian Sausage Pizza', 'Artichoke Pizza', 'Clam Pizza', 'Spicy Italian Pizza', 'White Pizza',
        'Chicken Bacon Ranch Pizza', 'Pulled Pork Pizza', 'Seafood Pizza', 'BBQ Pulled Pork Pizza', 'Mac and Cheese Pizza',
        'Chicken Parmesan Pizza', 'Beef and Blue Cheese Pizza', 'Mediterranean Pizza', 'Truffle Mushroom Pizza', 'Ham and Pineapple Pizza',
        'Zucchini Pizza', 'Feta and Spinach Pizza', 'Eggplant Parmesan Pizza', 'Gorgonzola Pizza', 'Roasted Red Pepper Pizza',
        'Sun-Dried Tomato Pizza', 'Arugula Pizza', 'Broccoli and Cheddar Pizza', 'Buffalo Shrimp Pizza', 'Corn and Jalapeno Pizza',
        'Duck Confit Pizza', 'Fig and Prosciutto Pizza', 'Grilled Vegetable Pizza', 'Lamb and Feta Pizza', 'Mango Habanero Pizza',
        'Nacho Pizza', 'Olive and Feta Pizza', 'Peach and Prosciutto Pizza', 'Quattro Stagioni Pizza', 'Ricotta and Spinach Pizza',
        'Smoked Salmon Pizza', 'Sweet Potato and Goat Cheese Pizza', 'Three Meat Pizza', 'Ultimate Veggie Pizza', 'Vegan Margherita Pizza',
        'Wild Mushroom Pizza', 'Xtreme Cheese Pizza', 'Yellow Squash Pizza', 'Zesty Italian Pizza', 'Apple and Brie Pizza',
        'BBQ Beef Pizza', 'Cheddar Bacon Pizza', 'Double Pepperoni Pizza', 'Egg and Sausage Pizza', 'Fennel Sausage Pizza',
        'Garlic Shrimp Pizza', 'Hot Dog Pizza', 'Indian Butter Chicken Pizza', 'Jalapeno Popper Pizza', 'Kale and Ricotta Pizza',
        'Lobster Pizza', 'Meatball Pizza', 'Nutella Dessert Pizza', 'Onion and Anchovy Pizza', 'Pulled Chicken Pizza',
        'Quinoa and Veggie Pizza', 'Ricotta and Honey Pizza', 'Spinach and Artichoke Pizza', 'Tuna and Olive Pizza', 'Ultimate Meat Lovers Pizza',
        'Vegetarian Deluxe Pizza', 'Walnut and Blue Cheese Pizza', 'Xanadu Pizza', 'Yellow Pepper Pizza', 'Zesty BBQ Chicken Pizza',
        'Avocado and Bacon Pizza', 'Blue Cheese and Pear Pizza', 'Chorizo and Manchego Pizza', 'Duck and Hoisin Pizza', 'Eggplant and Tomato Pizza'
    ];

    public function pizzaName()
    {
        return function () {
            return $this->faker->randomElement(self::$PizzaNames);
        };
    }

    public static array $typeOfProducts = [
      'Pizza', 'Beverage'
    ];

    public function typeOfProduct()
    {
        return $this->faker->randomElement(self::$typeOfProducts);
    }

    public function withPizzaType()
    {
        return $this->state(function () {
            return [
                'name' => $this->pizzaName(),
                'type' => 'Pizza',
            ];
        });
//
//        return $this->state([
//                'name' => $this->pizzaName(),
//                'type' => 'Pizza',
//            ]);
    }
    public function withBeverageType()
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));
        return $this->state([
            'name' => $faker->beverageName(),
            'type' => 'Beverage',
        ]);
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
