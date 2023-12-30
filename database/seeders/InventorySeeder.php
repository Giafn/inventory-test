<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Provider\Commerce;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $table->string('code', 20)->unique();
        // $table->string('name', 100);
        // $table->decimal('price', 10, 2);
        // $table->integer('stock');

        // use faker loop 100x dengan data dummy
        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 100; $i++) {
            $list[] = [
                'code' => $faker->unique()->bothify('??###??###'),
                'name' => $faker->word,
                'price' => $faker->numberBetween(1000, 100000),
                'stock' => $faker->numberBetween(1, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Inventory::insert($list);

    }
}
