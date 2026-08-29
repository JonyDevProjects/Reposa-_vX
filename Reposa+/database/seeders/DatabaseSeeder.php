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

        // Crear productos específicos (Almohadas) con traducciones
        $productsData = [
            [
                'name' => ['es' => 'Almohada Viscoelástica Premium', 'en' => 'Premium Viscoelastic Pillow'],
                'description' => ['es' => 'Espuma de memoria de alta densidad que se adapta perfectamente a tu cuello.', 'en' => 'High-density memory foam that perfectly adapts to your neck.'],
                'price' => 45.99,
                'stock' => 50,
                'material' => ['es' => 'Viscoelástica', 'en' => 'Viscoelastic'],
                'firmness' => ['es' => 'Media-Alta', 'en' => 'Medium-High'],
                'dimensions' => ['es' => '70x40 cm', 'en' => '70x40 cm'],
                'image_url' => '/images/products/pillow_viscoelastica.png'
            ],
            [
                'name' => ['es' => 'Almohada de Gel Refrescante', 'en' => 'Cooling Gel Pillow'],
                'description' => ['es' => 'Capa de gel térmico para mantener la frescura durante toda la noche.', 'en' => 'Thermal gel layer to keep you cool all night long.'],
                'price' => 59.99,
                'stock' => 30,
                'material' => ['es' => 'Gel y Espuma', 'en' => 'Gel and Foam'],
                'firmness' => ['es' => 'Media', 'en' => 'Medium'],
                'dimensions' => ['es' => '70x40 cm', 'en' => '70x40 cm'],
                'image_url' => '/images/products/pillow_gel.png'
            ],
            [
                'name' => ['es' => 'Almohada Cervical Ergonómica', 'en' => 'Ergonomic Cervical Pillow'],
                'description' => ['es' => 'Diseño contorneado para aliviar la presión en la columna vertebral.', 'en' => 'Contoured design to relieve pressure on the spine.'],
                'price' => 39.50,
                'stock' => 100,
                'material' => ['es' => 'Látex', 'en' => 'Latex'],
                'firmness' => ['es' => 'Alta', 'en' => 'High'],
                'dimensions' => ['es' => '60x35 cm', 'en' => '60x35 cm'],
                'image_url' => '/images/products/pillow_cervical.png'
            ],
            [
                'name' => ['es' => 'Almohada de Plumas Naturales', 'en' => 'Natural Feathers Pillow'],
                'description' => ['es' => 'Relleno de plumón natural para una suavidad extrema y confort tradicional.', 'en' => 'Natural down filling for extreme softness and traditional comfort.'],
                'price' => 75.00,
                'stock' => 20,
                'material' => ['es' => 'Plumón de Oca', 'en' => 'Goose Down'],
                'firmness' => ['es' => 'Suave', 'en' => 'Soft'],
                'dimensions' => ['es' => '80x40 cm', 'en' => '80x40 cm'],
                'image_url' => '/images/products/pillow_plumas.png'
            ],
            [
                'name' => ['es' => 'Almohada Antiácaros Hipoalergénica', 'en' => 'Hypoallergenic Anti-Dust Mite Pillow'],
                'description' => ['es' => 'Tratamiento especial para prevenir alergias y garantizar un entorno limpio.', 'en' => 'Special treatment to prevent allergies and ensure a clean environment.'],
                'price' => 29.99,
                'stock' => 150,
                'material' => ['es' => 'Microfibra', 'en' => 'Microfiber'],
                'firmness' => ['es' => 'Media', 'en' => 'Medium'],
                'dimensions' => ['es' => '75x40 cm', 'en' => '75x40 cm'],
                'image_url' => '/images/products/pillow_antiacaros.png'
            ],
            [
                'name' => ['es' => 'Almohada Viaje Cuello 360', 'en' => '360 Neck Travel Pillow'],
                'description' => ['es' => 'Soporte completo para el cuello durante viajes largos en avión o coche.', 'en' => 'Complete neck support for long flights or car rides.'],
                'price' => 19.99,
                'stock' => 200,
                'material' => ['es' => 'Espuma', 'en' => 'Foam'],
                'firmness' => ['es' => 'Alta', 'en' => 'High'],
                'dimensions' => ['es' => '30x30 cm', 'en' => '30x30 cm'],
                'image_url' => '/images/products/pillow_viaje.png'
            ],
            [
                'name' => ['es' => 'Almohada de Bambú Ecológica', 'en' => 'Eco-Friendly Bamboo Pillow'],
                'description' => ['es' => 'Funda de bambú transpirable y relleno sostenible.', 'en' => 'Breathable bamboo cover and sustainable filling.'],
                'price' => 49.99,
                'stock' => 40,
                'material' => ['es' => 'Bambú y Fibras Recicladas', 'en' => 'Bamboo and Recycled Fibers'],
                'firmness' => ['es' => 'Media-Suave', 'en' => 'Medium-Soft'],
                'dimensions' => ['es' => '70x40 cm', 'en' => '70x40 cm'],
                'image_url' => '/images/products/pillow_bambu.png'
            ],
            [
                'name' => ['es' => 'Almohada Terapéutica con Aloe Vera', 'en' => 'Aloe Vera Therapeutic Pillow'],
                'description' => ['es' => 'Tejido impregnado con extractos de Aloe Vera para el cuidado de la piel.', 'en' => 'Fabric infused with Aloe Vera extracts for skin care.'],
                'price' => 35.50,
                'stock' => 80,
                'material' => ['es' => 'Algodón y Aloe Vera', 'en' => 'Cotton and Aloe Vera'],
                'firmness' => ['es' => 'Media', 'en' => 'Medium'],
                'dimensions' => ['es' => '70x40 cm', 'en' => '70x40 cm'],
                'image_url' => '/images/products/pillow_aloevera.png'
            ]
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $product = new Product();
            foreach (['name', 'description', 'material', 'firmness', 'dimensions'] as $field) {
                if (isset($pData[$field]) && is_array($pData[$field])) {
                    foreach ($pData[$field] as $locale => $value) {
                        $product->setTranslation($field, $locale, $value);
                    }
                    unset($pData[$field]);
                }
            }
            $product->fill($pData);
            $product->save();

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
