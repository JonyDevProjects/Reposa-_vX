<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear categorías
        $categories = Category::factory(5)->create();

        // Crear usuario administrador
        User::factory()->create([
            'name' => 'Admin Reposa+',
            'email' => 'admin@reposaplus.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Crear usuario normal
        User::factory()->create([
            'name' => 'Usuario Invitado',
            'email' => 'user@reposaplus.com',
            'password' => \Illuminate\Support\Facades\Hash::make('user123'),
            'role' => 'user',
        ]);

        // Crear productos específicos (Almohadas)
        $products = [
            [
                'name' => 'Almohada Viscoelástica Premium',
                'description' => 'Espuma de memoria de alta densidad que se adapta perfectamente a tu cuello.',
                'price' => 45.99,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1632150821033-d9229987f651?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'name' => 'Almohada de Gel Refrescante',
                'description' => 'Capa de gel térmico para mantener la frescura durante toda la noche.',
                'price' => 59.99,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&q=80&w=400'
            ],
            [
                'name' => 'Almohada Cervical Ergonómica',
                'description' => 'Diseño contorneado para aliviar la presión en la columna vertebral.',
                'price' => 39.50,
                'stock' => 100,
                'image_url' => 'https://images.unsplash.com/photo-1629732047847-50bad75599e5?auto=format&fit=crop&q=80&w=400'
            ],
        ];

        foreach ($products as $p) {
            $product = Product::create($p);
            $product->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')->toArray()
            );
        }
    }
}
