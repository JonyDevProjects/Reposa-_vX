<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSummary extends Model
{
    protected $table = 'v_order_summary';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
