<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'shopping_cart';


    protected $fillable = [
        'id_user',
        'id_prod_q',
        'final_value_car',
    ];


    protected $casts = [
        'id_prod_q' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
