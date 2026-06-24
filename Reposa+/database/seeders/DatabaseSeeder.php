<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        $user = User::factory()->create([
            'name' => 'Usuario Invitado',
            'email' => 'user@reposaplus.com',
            'password' => \Illuminate\Support\Facades\Hash::make('user123'),
            'role' => 'user',
        ]);

        // Crear perfil para el usuario normal
        Profile::create([
            'user_id' => $user->id,
            'phone' => '+34 600 123 456'
        ]);

        // Crear direcciones para el usuario normal
        Address::create([
            'user_id' => $user->id,
            'street' => 'Calle Principal 123',
            'city' => 'Madrid',
            'zip_code' => '28001',
            'is_main' => true
        ]);

        // Crear productos específicos (Almohadas)
        $products = [
            [
                'name' => 'Almohada Viscoelástica Premium',
                'description' => 'Espuma de memoria de alta densidad que se adapta perfectamente a tu cuello.',
                'price' => 45.99,
                'stock' => 50,
                'material' => 'Viscoelástica',
                'firmness' => 'Media-Alta',
                'dimensions' => '70x40 cm',
                'image_url' => '/images/products/pillow_viscoelastica.png'
            ],
            [
                'name' => 'Almohada de Gel Refrescante',
                'description' => 'Capa de gel térmico para mantener la frescura durante toda la noche.',
                'price' => 59.99,
                'stock' => 30,
                'material' => 'Gel y Espuma',
                'firmness' => 'Media',
                'dimensions' => '70x40 cm',
                'image_url' => '/images/products/pillow_gel.png'
            ],
            [
                'name' => 'Almohada Cervical Ergonómica',
                'description' => 'Diseño contorneado para aliviar la presión en la columna vertebral.',
                'price' => 39.50,
                'stock' => 100,
                'material' => 'Látex',
                'firmness' => 'Alta',
                'dimensions' => '60x35 cm',
                'image_url' => '/images/products/pillow_cervical.png'
            ],
            [
                'name' => 'Almohada de Plumas Naturales',
                'description' => 'Relleno de plumón natural para una suavidad extrema y confort tradicional.',
                'price' => 75.00,
                'stock' => 20,
                'material' => 'Plumón de Oca',
                'firmness' => 'Suave',
                'dimensions' => '80x40 cm',
                'image_url' => '/images/products/pillow_plumas.png'
            ],
            [
                'name' => 'Almohada Antiácaros Hipoalergénica',
                'description' => 'Tratamiento especial para prevenir alergias y garantizar un entorno limpio.',
                'price' => 29.99,
                'stock' => 150,
                'material' => 'Microfibra',
                'firmness' => 'Media',
                'dimensions' => '75x40 cm',
                'image_url' => '/images/products/pillow_antiacaros.png'
            ],
            [
                'name' => 'Almohada Viaje Cuello 360',
                'description' => 'Soporte completo para el cuello durante viajes largos en avión o coche.',
                'price' => 19.99,
                'stock' => 200,
                'material' => 'Espuma',
                'firmness' => 'Alta',
                'dimensions' => '30x30 cm',
                'image_url' => '/images/products/pillow_viaje.png'
            ],
            [
                'name' => 'Almohada de Bambú Ecológica',
                'description' => 'Funda de bambú transpirable y relleno sostenible.',
                'price' => 49.99,
                'stock' => 40,
                'material' => 'Bambú y Fibras Recicladas',
                'firmness' => 'Media-Suave',
                'dimensions' => '70x40 cm',
                'image_url' => '/images/products/pillow_bambu.png'
            ],
            [
                'name' => 'Almohada Terapéutica con Aloe Vera',
                'description' => 'Tejido impregnado con extractos de Aloe Vera para el cuidado de la piel.',
                'price' => 35.50,
                'stock' => 80,
                'material' => 'Algodón y Aloe Vera',
                'firmness' => 'Media',
                'dimensions' => '70x40 cm',
                'image_url' => '/images/products/pillow_aloevera.png'
            ]
        ];

        $createdProducts = [];
        foreach ($products as $p) {
            $product = Product::create($p);
            $product->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')->toArray()
            );
            $createdProducts[] = $product;
        }

        // Añadir favoritos al usuario
        $user->favorites()->attach([$createdProducts[0]->id, $createdProducts[1]->id, $createdProducts[3]->id]);

        // Crear un par de pedidos de ejemplo
        $order1 = Order::create([
            'user_id' => $user->id,
            'total_amount' => $createdProducts[0]->price + $createdProducts[2]->price,
            'status' => 'delivered'
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[0]->id,
            'quantity' => 1,
            'price_at_purchase' => $createdProducts[0]->price
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[2]->id,
            'quantity' => 1,
            'price_at_purchase' => $createdProducts[2]->price
        ]);

        // Decrementar stock para reflejar los productos vendidos en order1
        $createdProducts[0]->decrement('stock', 1);
        $createdProducts[2]->decrement('stock', 1);

        // Simular que el pedido se hizo hace 1 mes
        $order1->update(['created_at' => now()->subMonth()]);

        $order2 = Order::create([
            'user_id' => $user->id,
            'total_amount' => $createdProducts[4]->price * 2,
            'status' => 'pending'
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[4]->id,
            'quantity' => 2,
            'price_at_purchase' => $createdProducts[4]->price
        ]);

        // Decrementar stock para reflejar los productos vendidos en order2
        $createdProducts[4]->decrement('stock', 2);
    }
}
