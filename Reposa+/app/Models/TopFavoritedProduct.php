<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopFavoritedProduct extends Model
{
    protected $table = 'v_top_favorited_products';
    public $timestamps = false;
    public $incrementing = false;
}
