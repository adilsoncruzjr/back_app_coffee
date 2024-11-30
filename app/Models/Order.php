<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;


    protected $table = 'orders';



    protected $fillable = [
        'id_user',
        'id_prod',
        'final_value',
        'status',
        'id_car'
    ];


    protected $casts = [
        'id_prod' => 'array',
    ];

    public $timestamps = false;


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }


    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'id_cart');
    }
}
