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
