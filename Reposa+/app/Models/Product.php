<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name', 'description', 'material', 'firmness', 'dimensions'];

    protected $fillable = [
        'name',
        'material',
        'firmness',
        'dimensions',
        'price',
        'stock',
        'description',
        'image_url',
    ];

    /**
     * Determina si el producto tiene existencias disponibles en almacén.
     */
    public function isInStock(): bool
    {
        return ((int) $this->stock) > 0;
    }

    /**
     * Comprueba si el inventario disponible cubre una cantidad solicitada.
     */
    public function hasStock(int $quantity = 1): bool
    {
        return $quantity > 0 && ((int) $this->stock) >= $quantity;
    }

    /**
     * Calcula el subtotal para una cantidad dada garantizando precisión decimal monetaria.
     */
    public function calculateSubtotal(int $quantity): float
    {
        if ($quantity <= 0) {
            return 0.0;
        }

        return round(((float) $this->price) * $quantity, 2);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_product');
    }
}
